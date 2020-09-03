<?php

namespace Metal\CompaniesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Metal\ServicesBundle\Entity\Package;
use Metal\ServicesBundle\Entity\ValueObject\ServicePeriodTypesProvider;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Entity\File as EmbeddableFile;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @ORM\Table(name="company_registration")
 * @UniqueEntity(fields={"company"})
 * @ORM\HasLifecycleCallbacks
 *
 * @Vich\Uploadable
 */
class CompanyRegistration
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true, onDelete="SET NULL")
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File", columnPrefix="price_")
     *
     * @var EmbeddableFile
     */
    protected $price;

    /**
     * @Vich\UploadableField(mapping="company_registration_price", fileNameProperty="price.name", originalName="price.originalName", mimeType="price.mimeType", size="price.size")
     * @Assert\NotBlank(groups={"new_item"})
     *
     * @var File|UploadedFile
     */
    protected $uploadedPrice;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\ServicesBundle\Entity\Package")
     * @ORM\JoinColumn(name="package_id", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Package
     */
    protected $package;

    /**
     * @ORM\Column(type="smallint", name="term_package", nullable=true)
     */
    protected $termPackage;

    /**
     * @ORM\Column(type="boolean", name="is_second_step_done", nullable=false, options={"default":0})
     */
    protected $isSecondStepDone;

    /**
     * @ORM\Column(type="boolean", name="is_third_step_done", nullable=false, options={"default":0})
     */
    protected $isThirdStepDone;

    /**
     * @ORM\Column(type="smallint", name="updater", nullable=false, options={"default":1})
     */
    protected $updater;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;

    use Updateable;

    public function __construct()
    {
        $this->updater = 1;
        $this->isSecondStepDone = false;
        $this->isThirdStepDone = false;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->price = new EmbeddableFile();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return Category
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category)
    {
        $this->category = $category;
    }

    public function getIsSecondStepDone()
    {
        return $this->isSecondStepDone;
    }

    public function setIsSecondStepDone($isSecondStepDone)
    {
        $this->isSecondStepDone = (bool)$isSecondStepDone;
    }

    public function getIsThirdStepDone()
    {
        return $this->isThirdStepDone;
    }

    public function setIsThirdStepDone($isThirdStepDone)
    {
        $this->isThirdStepDone = (bool)$isThirdStepDone;
    }

    public function getUpdater()
    {
        return $this->updater;
    }

    public function setUpdater($updater)
    {
        $this->updater = $updater;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return EmbeddableFile
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param EmbeddableFile $price
     */
    public function setFile(EmbeddableFile $price)
    {
        $this->price = $price;
    }

    /**
     * @return File|UploadedFile
     */
    public function getUploadedPrice()
    {
        return $this->uploadedPrice;
    }

    public function setUploadedPrice(File $uploadedPrice = null)
    {
        $this->uploadedPrice = $uploadedPrice;
        if ($this->uploadedPrice instanceof UploadedFile) {
            $this->setUpdatedAt(new \DateTime());
        }
    }

    public function getPackage()
    {
        return $this->package;
    }

    public function setPackage($package)
    {
        $this->package = $package;
    }

    public function getTermPackage()
    {
        return $this->termPackage;
    }

    public function setTermPackage($termPackage)
    {
        $this->termPackage = $termPackage;
    }

    public function addCompanyCategories(CompanyCategory $companyCategory)
    {
        $this->company->addCompanyCategories($companyCategory);
    }

    public function removeCompanyCategories(CompanyCategory $companyCategory)
    {
        $this->company->removeCompanyCategories($companyCategory);
    }

    public function getCompanyCategories()
    {
        return $this->company->getCompanyCategories();
    }

    public function addCompanyCategory(CompanyCategory $companyCategory)
    {
        $this->company->addCompanyCategory($companyCategory);
    }

    public function removeCompanyCategory(CompanyCategory $companyCategory)
    {
        $this->company->removeCompanyCategory($companyCategory);
    }

    public static function getSimpleTermPackage()
    {
        return array(
            ServicePeriodTypesProvider::QUARTER => '3 месяца',
            ServicePeriodTypesProvider::HALF_YEAR => '6 месяцев',
            ServicePeriodTypesProvider::YEAR => '1 год'
        );
    }

    public static function getAvailablePromotions()
    {
        return array(
            Package::BASE_PACKAGE => 'Не продвигать товары',
            Package::ADVANCED_PACKAGE => 'Рекомендованное продвижение',
            Package::FULL_PACKAGE => 'Максимальное продвижение'
        );
    }
}
