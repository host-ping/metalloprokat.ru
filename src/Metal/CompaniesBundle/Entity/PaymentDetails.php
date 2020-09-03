<?php

namespace Metal\CompaniesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Entity\File as EmbeddableFile;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Metal\CompaniesBundle\Repository\PaymentDetailsRepository")
 * @ORM\Table(name="company_payment_details", uniqueConstraints={
 *   @ORM\UniqueConstraint(name="UNIQ_company", columns={"company_id"} )})
 *
 * @Vich\Uploadable
 */
class PaymentDetails
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID")
     *
     * @var Company
     */
    protected $company;

    /** @ORM\Column(type="datetime", name="attachment_uploaded_at", nullable=true) */
    protected $uploadedAt;

    /**
     * @ORM\Column(type="datetime", name="attachment_approved_at", nullable=true)
     * @var \DateTime
     */
    protected $approvedAt;

    /**
     * @ORM\Column(length=255, name="name_of_legal_entity", nullable=true)
     * @Assert\Length(max="255", groups={"company_details"})
     */
    protected $nameOfLegalEntity;

    /**
     * @ORM\Column(length=255, name="legal_address", nullable=true)
     * @Assert\Length(max="255", groups={"company_details"})
     */
    protected $legalAddress;

    /**
     * @ORM\Column(length=255, name="mail_address", nullable=true)
     * @Assert\Length(max="255", groups={"company_details"})
     */
    protected $mailAddress;

    /**
     * @ORM\Column(length=12, name="inn", nullable=true)
     * @Assert\Length(max="12", groups={"company_details"})
     */
    protected $inn;

    /**
     * @ORM\Column(length=11, name="kpp", nullable=true)
     * @Assert\Length(max="11", groups={"company_details"})
     */
    protected $kpp;

    //TODO: согласовать с Наташей переименование этой колонки - тут буквы местами переставлены, аббривеатура не так звучит
    /**
     * @ORM\Column(length=13, name="orgn", nullable=true)
     * @Assert\Length(max="13", groups={"company_details"})
     */
    protected $orgn;

    /**
     * @ORM\Column(length=255, name="bank_title", nullable=true)
     * @Assert\Length(max="255", groups={"company_details"})
     */
    protected $bankTitle;

    /** @ORM\Column(type="string", name="bank_bik", nullable=true, length=20) */
    protected $bankBik;

    /**
     * @ORM\Column(length=30, name="bank_account", nullable=true)
     * @Assert\Length(max="30", groups={"company_details"})
     */
    protected $bankAccount;

    /**
     * @ORM\Column(length=30, name="bank_correspondent_account", nullable=true)
     * @Assert\Length(max="30", groups={"company_details"})
     */
    protected $bankCorrespondentAccount;

    /** @ORM\Column(type="string", name="okpo", nullable=true, length=20) */
    protected $okpo;

    /**
     * @ORM\Column(length=255, name="okved", nullable=true)
     * @Assert\Length(max="255", groups={"company_details"})
     */
    protected $okved;

    /**
     * @ORM\Column(length=255, name="director_full_name", nullable=true)
     * @Assert\Length(max="255", groups={"company_details"})
     */
    protected $directorFullName;

    /** @ORM\Column(type="boolean", name="display_on_minisite") */
    protected $displayOnMiniSite;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File", columnPrefix="file_")
     *
     * @var EmbeddableFile
     */
    protected $file;

    /**
     * @Vich\UploadableField(mapping="payment_details_file", fileNameProperty="file.name", originalName="file.originalName", mimeType="file.mimeType", size="file.size")
     * @Assert\NotBlank(groups={"new_item"})
     *
     * @var File|UploadedFile
     */
    protected $uploadedFile;

    use Updateable;

    public function __construct()
    {
        $this->updatedAt = new \DateTime();
        $this->displayOnMiniSite = false;
        $this->file = new EmbeddableFile();
    }

    public function setNameOfLegalEntity($nameOfLegalEntity)
    {
        $this->nameOfLegalEntity = $nameOfLegalEntity;
    }

    public function getNameOfLegalEntity()
    {
        return $this->nameOfLegalEntity;
    }

    public function setUpdated()
    {
        $this->updatedAt = new \DateTime();
        if ($this->company) {
            $this->company->scheduleSynchronization();
        }
    }

    /**
     * @param \DateTime $approvedAt
     */
    public function setApprovedAt(\DateTime $approvedAt)
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

    /**
     * @return bool
     */
    public function isApproved()
    {
        return $this->approvedAt !== null;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
        $this->id = $company->getId();
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param \DateTime $uploadedAt
     */
    public function setUploadedAt(\DateTime $uploadedAt)
    {
        $this->uploadedAt = $uploadedAt;
    }

    /**
     * @return \DateTime
     */
    public function getUploadedAt()
    {
        return $this->uploadedAt;
    }

    public function setBankAccount($bankAccount)
    {
        $this->bankAccount = $bankAccount;
    }

    public function getBankAccount()
    {
        return $this->bankAccount;
    }

    public function setBankBik($bankBik)
    {
        $this->bankBik = $bankBik;
    }

    public function getBankBik()
    {
        return $this->bankBik;
    }

    public function setBankCorrespondentAccount($bankCorrespondentAccount)
    {
        $this->bankCorrespondentAccount = $bankCorrespondentAccount;
    }

    public function getBankCorrespondentAccount()
    {
        return $this->bankCorrespondentAccount;
    }

    public function setBankTitle($bankTitle)
    {
        $this->bankTitle = $bankTitle;
    }

    public function getBankTitle()
    {
        return $this->bankTitle;
    }

    public function setDirectorFullName($directorFullName)
    {
        $this->directorFullName = $directorFullName;
    }

    public function getDirectorFullName()
    {
        return $this->directorFullName;
    }

    public function setOkpo($okpo)
    {
        $this->okpo = $okpo;
    }

    public function getOkpo()
    {
        return $this->okpo;
    }

    public function setOkved($okved)
    {
        $this->okved = $okved;
    }

    public function getOkved()
    {
        return $this->okved;
    }

    public function setOrgn($orgn)
    {
        $this->orgn = $orgn;
    }

    public function getOrgn()
    {
        return $this->orgn;
    }

    public function setInn($inn)
    {
        $this->inn = $inn;
    }

    public function getInn()
    {
        return $this->inn;
    }

    public function setKpp($kpp)
    {
        $this->kpp = $kpp;
    }

    public function getKpp()
    {
        return $this->kpp;
    }

    public function setLegalAddress($legalAddress)
    {
        $this->legalAddress = $legalAddress;
    }

    public function getLegalAddress()
    {
        return $this->legalAddress;
    }

    public function setMailAddress($mailAddress)
    {
        $this->mailAddress = $mailAddress;
    }

    public function getMailAddress()
    {
        return $this->mailAddress;
    }

    public function setDisplayOnMiniSite($displayOnMiniSite)
    {
        $this->displayOnMiniSite = $displayOnMiniSite;
    }

    public function getDisplayOnMiniSite()
    {
        return $this->displayOnMiniSite;
    }

    /**
     * @return EmbeddableFile
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param EmbeddableFile $file
     */
    public function setFile(EmbeddableFile $file)
    {
        $this->file = $file;
    }

    /**
     * @return File|UploadedFile
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    public function setUploadedFile(File $uploadedFile = null)
    {
        $this->uploadedFile = $uploadedFile;
        if ($this->uploadedFile instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTime());
            $this->setUploadedAt(new \DateTime());
            $this->setApproved(false);
        }
    }
}
