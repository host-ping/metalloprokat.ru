<?php

namespace Metal\UsersBundle\Entity;

use Brouzie\Bundle\CrossdomainAuthBundle\Security\Core\User\VersionableUserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\UserCity;
use Metal\ProjectBundle\Entity\Behavior\Attributable;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Metal\ProjectBundle\Util\RandomGenerator;
use Metal\ProjectBundle\Helper\FormattingHelper;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Country;
use Metal\UsersBundle\Validator\Constraints as UserAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddableFile;
use Metal\ProjectBundle\Validator\Constraints\Image;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="Metal\UsersBundle\Repository\UserRepository")
 * @ORM\Table(name="User")
 *
 * @UniqueEntity("email", message="Пользователь с таким email уже существует", groups={"registration", "change_email", "change_email_admin", "registration_employees"})
 *
 * @Vich\Uploadable
 */
class User implements UserInterface, EquatableInterface, VersionableUserInterface, \Serializable
{
    const CONSUMER = 1;
    const TRADER = 2;

    const PERMISSION_GROUP_MODERATOR = 1;
    const PERMISSION_GROUP_USER = 2;

    const ROLE_SUPER_ADMIN = 9;
    const ROLE_DEFAULT = 0;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="User_ID")
     */
    protected $id;

    /** @ORM\Column(length=45, name="Password") */
    protected $password;

    public $oldPassword;

    /**
     * @Assert\NotBlank(groups={"change_password", "registration"})
     * @Assert\Length(min=6, max=20, groups={"change_password", "registration"})
     */
    public $newPassword;

    /**
     * @ORM\Column(length=255, name="Email")
     * @Assert\NotBlank(groups={"registration", "change_email_admin", "registration_employees"})
     * @Assert\Email(groups={"registration", "change_email_admin", "registration_employees"}, strict=true)
     */
    protected $email;

    /**
     * @ORM\Column(length=255, name="NewEmail", nullable=true)
     * @Assert\NotBlank(groups={"change_email"})
     * @Assert\Email(groups={"change_email"}, strict=true)
     * @UserAssert\IsUniqueEmail(groups={"change_email"})
     */
    protected $newEmail;

    /**
     * @ORM\Column(length=255, name="ForumName")
     * @Assert\NotBlank(groups={"edit_profile"})
     * @Assert\Length(max="255")
     */
    protected $firstName;

    /**
     * @ORM\Column(length=255, name="LastName", nullable=true)
     * @Assert\Length(max="255")
     */
    protected $secondName;

    /**
     * @ORM\Column(length=255, name="Job", nullable=true)
     * @Assert\Length(min="2", max="255")
     */
    protected $job;

    /**
     * @ORM\Column(length=255, name="Phones")
     *
     * @deprecated use User::$phone
     */
    protected $phones;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="ConnectCompany", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Company
     */
    protected $company;

    /** @ORM\Column(length=255, name="company_title", nullable=true) */
    protected $companyTitle;

    /** @ORM\Column(type="boolean", name="RigthToEdit") */
    protected $hasEditPermission;

    /** @ORM\Column(type="boolean", name="RightToUse") */
    protected $canUseService;

    /** @ORM\Column(type="integer", name="PermissionGroup_ID") */
    protected $permissionGroupId;

    /** @ORM\Column(length=255, name="ForumAvatar") */
    protected $avatarName;

    /** @ORM\Column(type="boolean", name="Checked") */
    protected $isEnabled;

    /** @ORM\Column(type="boolean", name="Confirmed") */
    protected $isEmailConfirmed;

    /** @ORM\Column(length=50, name="skype", nullable=true) */
    protected $skype;

    /** @ORM\Column(length=12, name="Icq", nullable=true) */
    protected $icq;

    /**
     * @ORM\Column(length=120, name="phone", nullable=true)
     *
     * @Assert\NotBlank(groups={"phone"})
     * @Assert\Regex(
     *   pattern="/\d+/",
     *   message="Неправильный номер телефона",
     *   groups={"registration", "edit_profile", "admin_panel", "registration_employees"}
     * )
     * @Assert\Length(
     *   min=6,
     *   max=255,
     *   groups={"registration", "edit_profile", "admin_panel", "registration_employees"}
     * )
     */
    protected $phone;

    /** @ORM\Column(length=20, name="phone_canonical", nullable=true) */
    protected $phoneCanonical;

    /** @ORM\Column(length=50, name="additional_code", nullable=true) */
    protected $additionalCode;

    /** @ORM\Column(type="datetime", name="LastUpdated") */
    protected $lastUpdated;

    /**
     * @ORM\OneToOne(targetEntity="UserCounter")
     * @ORM\JoinColumn(name="counter_id", referencedColumnName="id", nullable=true)
     *
     * @var UserCounter
     */
    protected $counter;

    /** @ORM\Column(length=255, name="RegistrationCode", nullable=false) */
    protected $registrationCode;

    /** @ORM\Column(length=255, name="recover_code", nullable=true) */
    protected $recoverCode;

    /** @ORM\Column(length=255, name="change_email_code", nullable=true) */
    protected $changeEmailCode;

    /** @ORM\Column(type="datetime", name="Created") */
    protected $createdAt;

    /** @ORM\Column(type="integer", name="user_version", nullable=false, options={"default":1}) */
    protected $userVersion;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\City")
     * @ORM\JoinColumn(name="city_id", referencedColumnName="Region_ID", nullable=true)
     *
     * @var City
     */
    protected $city;

    //TODO: this field is required due to Symfony issue https://github.com/symfony/symfony/issues/10519
    public $cityTitle;

    /** @ORM\Column(type="datetime", name="approved_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $approvedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\TerritorialBundle\Entity\Country")
     * @ORM\JoinColumn(name="country_id", referencedColumnName="Country_ID", nullable=false)
     *
     * @var Country
     */
    protected $country;

    /** @ORM\Column(length=255, name="referer", nullable=false, options={"default": ""}) */
    protected $referer;

    /**
     * @ORM\Column(type="smallint", name="additional_role_id", nullable=false)
     */
    protected $additionalRoleId;

    /**
     * @ORM\Column(type="boolean", name="display_in_contacts", options={"default":true})
     */
    protected $displayInContacts;

    /**
     * @ORM\Column(type="smallint", name="display_position", nullable=false, options={"default":1})
     * @Assert\NotBlank()
     * @Assert\GreaterThanOrEqual(1)
     * @Assert\Type(type="integer")
     */
    protected $displayPosition;

    /**
     * @ORM\Column(type="boolean", name="display_only_in_specified_cities", options={"default":false})
     */
    protected $displayOnlyInSpecifiedCities;

    /**
     * @ORM\OneToMany(targetEntity="Metal\CompaniesBundle\Entity\UserCity", mappedBy="user", cascade={"persist"}, orphanRemoval=true)
     *
     * @var ArrayCollection|UserCity[]
     */
    protected $userCities;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File", columnPrefix="photo_")
     *
     * @var EmbeddableFile
     */
    protected $photo;

    /**
     * @Vich\UploadableField(mapping="user_photo", fileNameProperty="photo.name", originalName="photo.originalName", mimeType="photo.mimeType", size="photo.size")
     * @Image(maxSize="10M")
     *
     * @var File|UploadedFile
     */
    protected $uploadedPhoto;

    use Attributable;

    use Updateable;

    public function __construct()
    {
        $this->userCities = new ArrayCollection();
        $this->photo = new EmbeddableFile();
        $this->hasEditPermission = false;
        $this->canUseService = false;
        $this->permissionGroupId = self::PERMISSION_GROUP_USER;
        $this->registrationCode = RandomGenerator::generateRandomCode();
        $this->isEnabled = true;
        $this->createdAt = new \DateTime();
        $this->isEmailConfirmed = false;
        $this->userVersion = 1;
        $this->additionalRoleId = 0;
        $this->referer = '';
        $this->displayInContacts = true;
        $this->displayPosition = 10;
        $this->displayOnlyInSpecifiedCities = false;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * @return ArrayCollection|UserCity[]
     */
    public function getUserCities()
    {
        return $this->userCities;
    }

    /**
     * @param UserCity $userCity
     */
    public function addUserCity(UserCity $userCity)
    {
        $userCity->setUser($this);
        $this->userCities->add($userCity);
    }

    /**
     * @param UserCity $userCity
     */
    public function addUserCities(UserCity $userCity)
    {
        $this->addUserCity($userCity);
    }

    /**
     * @param UserCity $userCity
     */
    public function removeUserCity(UserCity $userCity)
    {
        $this->userCities->removeElement($userCity);
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        return $this->firstName = $firstName;
    }

    public function getSecondName()
    {
        return $this->secondName;
    }

    public function setSecondName($secondName)
    {
        $this->secondName = $secondName;
    }

    public function getFullName()
    {
        return implode(' ', array_filter(array($this->firstName, $this->secondName)));
    }

    public function setFullName($fullName)
    {
        $nameParts = preg_split('/\s+/ui', $fullName);

        $this->firstName = (string)array_shift($nameParts);
        if ($nameParts) {
            $this->secondName = (string)array_shift($nameParts);
        }
    }

    public function getJob()
    {
        return $this->job;
    }

    public function setJob($job)
    {
        $this->job = $job;
    }

    public function getReferer()
    {
        return $this->referer;
    }

    public function setReferer($referer)
    {
        $this->referer = (string)$referer;
    }

    /**
     * @deprecated
     */
    public function getPhones()
    {
        return $this->phones;
    }

    /**
     * @deprecated
     */
    public function setPhones($phones)
    {
        $this->phones = $phones;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    public function setCompany($company)
    {
        $this->scheduleSynchronization();

        $this->company = $company;

        $this->scheduleSynchronization();
    }

    public function hasEditPermission()
    {
        return $this->hasEditPermission;
    }

    public function setEditPermission($hasEditPermission)
    {
        $this->hasEditPermission = $hasEditPermission;
    }

    public function getHasEditPermission()
    {
        return $this->hasEditPermission;
    }

    public function setHasEditPermission($hasEditPermission)
    {
        $this->hasEditPermission = $hasEditPermission;
    }

    public function getCanUseService()
    {
        return $this->canUseService;
    }

    public function setCanUseService($canUseService)
    {
        $this->canUseService = $canUseService;
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used by the equals method and the username.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->email,
            $this->id,
        ));
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        list(
            $this->password,
            $this->email,
            $this->id
            ) = $data;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
        // do nothing
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        $roles = array('ROLE_USER');

        if ($this->isEmailConfirmed) {
            $roles[] = 'ROLE_CONFIRMED_EMAIL';
        }

        if ($this->getAdditionalRoleId()) {
            $availableRoles = self::getAvailableUserRoles();
            $roles[] = $availableRoles[$this->getAdditionalRoleId()];
        }

        $callbackModerators = array(
            'v.fokin@metalloprokat.ru',
            'marina_f@product.ru',
            't.borodacheva@metalloprokat.ru',
            'tb@product.ru',
        );

        if (in_array($this->email, $callbackModerators)) {
            $roles[] = 'ROLE_CALLBACK_ADMIN';
        }

        if ($company = $this->getCompany()) {
            $roles[] = 'ROLE_SUPPLIER';

            if ($this->isApproved()) {
                $roles[] = 'ROLE_APPROVED_USER';
            }

            if ($this->isMainUserForCompany()) {
                $roles[] = 'ROLE_MAIN_USER';
            }

            if ($this->canUseService && $company->getSprosEndsAt() > new \DateTime()) {
                $roles[] = 'ROLE_ALLOWED_VIEW_DEMAND_CONTACTS';
            }
        }

        return $roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->getEmail();
    }

    public function isMainUserForCompany()
    {
        $company = $this->getCompany();

        return $company && $company->getCompanyLog()->getCreatedBy()->getId() == $this->getId();
    }

    public function requireApplyTerritorialRules()
    {
        $company = $this->getCompany();

        return $company && $company->getHasTerritorialRules()
            && (!$this->isMainUserForCompany() || !$company->getMainUserAllSees());
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(UserInterface $user)
    {
        if ($this->getPassword() !== $user->getPassword()) {
            return false;
        }

        if ($this->getUsername() !== $user->getUsername()) {
            return false;
        }

        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }

        if ($user instanceof self) {
            // Check that the roles are the same, in any order
            $isEqual = count($this->getRoles()) == count($user->getRoles());
            if ($isEqual) {
                foreach ($this->getRoles() as $role) {
                    $isEqual = $isEqual && in_array($role, $user->getRoles());
                }
            }

            return $isEqual;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserVersion()
    {
        return $this->userVersion;
    }

    public function setPermissionGroupId($permissionGroupId)
    {
        $this->permissionGroupId = $permissionGroupId;
    }

    public function getPermissionGroupId()
    {
        return $this->permissionGroupId;
    }

    public function setAvatarName($avatarName)
    {
        $this->avatarName = $avatarName;
    }

    public function getAvatarName()
    {
        return $this->avatarName;
    }

    /**
     * @return EmbeddableFile
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * @param EmbeddableFile $photo
     */
    public function setPhoto(EmbeddableFile $photo)
    {
        $this->photo = $photo;
    }

    /**
     * @return File|UploadedFile
     */
    public function getUploadedPhoto()
    {
        return $this->uploadedPhoto;
    }

    public function setUploadedPhoto(File $uploadedPhoto = null)
    {
        $this->uploadedPhoto = $uploadedPhoto;
        if ($this->uploadedPhoto) {
            $this->setUpdatedAt(new \DateTime());
        }
    }

    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    /**
     * @param UserCounter $counter
     */
    public function setCounter(UserCounter $counter)
    {
        $this->counter = $counter;
        $this->counter->setUser($this);
    }

    /**
     * @return UserCounter
     */
    public function getCounter()
    {
        return $this->counter;
    }

    public function setAdditionalCode($additionalCode)
    {
        $this->additionalCode = $additionalCode;
    }

    public function getAdditionalCode()
    {
        return $this->additionalCode;
    }

    public function setIcq($icq)
    {
        $this->icq = $icq;
    }

    public function getIcq()
    {
        return $this->icq;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
        $this->phoneCanonical = FormattingHelper::canonicalizePhone($phone);
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function setSkype($skype)
    {
        $this->skype = $skype;
    }

    public function getSkype()
    {
        return $this->skype;
    }

    /**
     * @param \DateTime $lastUpdated
     */
    public function setLastUpdated(\DateTime $lastUpdated)
    {
        $this->lastUpdated = $lastUpdated;
    }

    /**
     * @return \DateTime
     */
    public function getLastUpdated()
    {
        return $this->lastUpdated;
    }

    public function setRegistrationCode($registrationCode)
    {
        $this->registrationCode = $registrationCode;
    }

    public function getRegistrationCode()
    {
        return $this->registrationCode;
    }

    public function setRecoverCode($recoverCode)
    {
        $this->recoverCode = $recoverCode;
    }

    public function getRecoverCode()
    {
        return $this->recoverCode;
    }

    public function getChangeEmailCode()
    {
        return $this->changeEmailCode;
    }

    public function setChangeEmailCode($changeEmailCode)
    {
        $this->changeEmailCode = $changeEmailCode;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function setIsEmailConfirmed($mailConfirmed)
    {
        $this->isEmailConfirmed = $mailConfirmed;

        $this->scheduleSynchronization();
    }

    public function getIsEmailConfirmed()
    {
        return $this->isEmailConfirmed;
    }

    public function setNewEmail($newEmail)
    {
        $this->newEmail = $newEmail;
    }

    public function getNewEmail()
    {
        return $this->newEmail;
    }

    public function setCity(City $city = null)
    {
        $this->city = $city;
        if ($city) {
            $this->country = $city->getCountry();
        }
    }

    /**
     * @return City
     */
    public function getCity()
    {
        return $this->city;
    }

    public function setCompanyTitle($companyTitle)
    {
        $this->companyTitle = $companyTitle;
    }

    public function getCompanyTitle()
    {
        return $this->companyTitle;
    }

    /**
     * @param Country $country
     */
    public function setCountry(Country $country)
    {
        $this->country = $country;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    public static function randomPassword($len = 8)
    {
        if (($len % 2) !== 0) {
            $len = 8;
        }
        $length = $len - 2;
        $consonants = array('b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z');
        $vocal = array('a', 'e', 'i', 'o', 'u');
        $password = '';
        mt_srand(microtime(true) * 1000000);
        $max = $length / 2;
        for ($i = 1; $i <= $max; $i++) {
            $password .= $consonants[mt_rand(0, 19)];
            $password .= $vocal[mt_rand(0, 4)];
        }
        $password .= mt_rand(10, 99);

        return $password;
    }

    public static function getAvailableUserRoles()
    {
        return array(
            1 => 'ROLE_PRODUCT_MODERATOR',
            2 => 'ROLE_MANAGER',
            3 => 'ROLE_EXTENDED_MODERATOR',
            4 => 'ROLE_CATALOG_PRODUCT_MODERATOR',
            5 => 'ROLE_CONTENT_MODERATOR',
            6 => 'ROLE_SEO_ADMINISTRATOR',
            9 => 'ROLE_SUPER_ADMIN',
        );
    }

    /**
     * @return City
     */
    public function getCityWithFallback()
    {
        return $this->company ? $this->getCompany()->getCity() : $this->city;
    }

    /**
     * @param \DateTime $approvedAt
     */
    public function setApprovedAt(\DateTime $approvedAt = null)
    {
        $this->approvedAt = $approvedAt;
    }

    /**
     * @return \DateTime
     */
    public function getApprovedAt()
    {
        return $this->approvedAt;
    }

    public function setApproved($approvedAt = true)
    {
        $this->approvedAt = $approvedAt ? new \DateTime() : null;
    }

    public function scheduleSynchronization()
    {
        if ($this->company) {
            $this->company->scheduleSynchronization();
        }
    }

    public function setAdditionalRoleId($additionalRoleId)
    {
        $this->additionalRoleId = (int)$additionalRoleId;
    }

    public function getAdditionalRoleId()
    {
        return $this->additionalRoleId;
    }

    public function isApproved()
    {
        return $this->approvedAt !== null;
    }

    public function getDisplayInContacts()
    {
        return $this->displayInContacts;
    }

    public function setDisplayInContacts($displayInContacts)
    {
        $this->displayInContacts = $displayInContacts;
    }

    public function getDisplayPosition()
    {
        return $this->displayPosition;
    }

    public function setDisplayPosition($displayPosition)
    {
        $this->displayPosition = $displayPosition;
    }

    public function getDisplayOnlyInSpecifiedCities()
    {
        return $this->displayOnlyInSpecifiedCities;
    }

    public function setDisplayOnlyInSpecifiedCities($displayOnlyInSpecifiedCities)
    {
        $this->displayOnlyInSpecifiedCities = $displayOnlyInSpecifiedCities;
    }

    public function isAllowedAddInFavorite()
    {
        return $this->company ? $this->company->getPackageChecker()->isAllowedAddInFavorite() : false;
    }

    public function getPhoneCanonical()
    {
        return $this->phoneCanonical;
    }
}
