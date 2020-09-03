<?php

namespace Metal\ProductsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Metal\ProjectBundle\Validator\Constraints\Image;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddableFile;

use Doctrine\ORM\Mapping\Index;

/**
 * @ORM\Entity(repositoryClass="Metal\ProductsBundle\Repository\ProductImageRepository")
 * @ORM\Table(name="Companies_images", indexes={@Index(name="IDX_url", columns={"url"})})
 *
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class ProductImage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="ID")
     */
    protected $id;

    /** @ORM\Column(type="datetime", name="Created", nullable=true) */
    protected $createdAt;


    /** @ORM\Column(type="integer", name="Company_ID", nullable=true) */
    protected $companyId;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="Company_ID", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Company
     */
    protected $company;

    /** @ORM\Column(name="Descr", nullable=false, options={"default":""}) */
    protected $description;

    /**
     * @ORM\Column(type="boolean", name="optimized", options={"default":1})
     */
    protected $optimized;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File", columnPrefix="photo_")
     *
     * @var EmbeddableFile
     */
    protected $photo;

    /**
     * @Vich\UploadableField(mapping="product_photo", fileNameProperty="photo.name", originalName="photo.originalName", mimeType="photo.mimeType", size="photo.size")
     * @Image(maxSize="10M")
     * @Assert\NotBlank(groups={"new_item"})
     *
     * @var File|UploadedFile
     */
    protected $uploadedPhoto;

    /** @ORM\Column(length=1000, name="url", nullable=true) */
    protected $url;

    /** @ORM\Column(type="boolean", name="downloaded", nullable=false, options={"default":false}) */
    protected $downloaded;

    /** @ORM\Column(type="smallint", name="retries_count", nullable=false, options={"default":0}) */
    protected $retriesCount;

    use Updateable;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->photo = new EmbeddableFile();
        $this->description = '';
        $this->optimized = true;
        $this->downloaded = false;
        $this->retriesCount = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Category|null $category
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
    }

    /**
     * @return Category|null
     */
    public function getCategory()
    {
        return $this->category;
    }

    public function setOptimized($optimized)
    {
        $this->optimized = $optimized;
    }

    public function getOptimized()
    {
        return $this->optimized;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getCompanyId()
    {
        return $this->companyId;
    }

    public function getSubDir()
    {
        return 'products';
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company = null)
    {
        $this->companyId = null;
        $this->company = $company;
        if ($this->company) {
            $this->companyId = $company->getId();
        }
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->companyId ? $this->company : null;
    }

    public function isCommon()
    {
        return $this->company === null;
    }

    public function setDescription($description)
    {
        $this->description = (string)$description;
    }

    public function getDescription()
    {
        return $this->description;
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

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getDownloaded()
    {
        return $this->downloaded;
    }

    public function setDownloaded($downloaded)
    {
        $this->downloaded = $downloaded;
    }

    public function getRetriesCount()
    {
        return $this->retriesCount;
    }

    public function setRetriesCount($retriesCount)
    {
        $this->retriesCount = $retriesCount;
    }

    public function increaseRetriesCount()
    {
        $this->retriesCount++;
    }
}
