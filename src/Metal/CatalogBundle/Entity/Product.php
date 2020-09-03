<?php

namespace Metal\CatalogBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProductsBundle\Entity\Product as CustomerProduct;
use Metal\ProjectBundle\Entity\Behavior\Attributable;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Metal\ProjectBundle\Validator\Constraints\Image;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddableFile;

/**
 * @ORM\Entity(repositoryClass="Metal\CatalogBundle\Repository\ProductRepository")
 * @ORM\Table(name="catalog_product")
 *
 * @Vich\Uploadable
 */
class Product
{
    const ATTR_CODE_MANUFACTURER = 'manufacturer';
    const ATTR_CODE_BRAND = 'brand';

    /**
     * @var AttributeValue[]
     */
    public $attributeValues = array();

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="title")
     *
     * @Assert\NotBlank()
     * @Assert\Length(max=255)
     */
    protected $title;

    /** @ORM\Column(type="boolean", name="is_title_non_unique", options={"default":0}) */
    protected $isTitleNonUnique;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="User_ID", nullable=true)
     *
     * @var User
     */
    protected $createdBy;

    /**
     * @ORM\Column(length=255, name="size", options={"default":""})
     *
     * @Assert\Length(max=255)
     */
    protected $size;

    /**
     * @ORM\Column(type="string", length=5000, name="description", options={"default":""})
     *
     * @Assert\Length(max=5000)
     */
    protected $description;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @Assert\NotBlank()
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\AttributesBundle\Entity\AttributeValue")
     * @ORM\JoinColumn(name="manufacturer_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @Assert\NotBlank()
     *
     * @var AttributeValue
     */
    protected $manufacturer;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\AttributesBundle\Entity\AttributeValue")
     * @ORM\JoinColumn(name="brand_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @Assert\NotBlank()
     *
     * @var AttributeValue
     */
    protected $brand;

    /**
     * @ORM\OneToMany(targetEntity="ProductAttributeValue", mappedBy="product", cascade={"persist"}, orphanRemoval=true)
     *
     * @var ArrayCollection|ProductAttributeValue[]
     */
    protected $productAttributesValues;

    /**
     * @ORM\OneToMany(targetEntity="ProductCity", mappedBy="product", cascade={"persist"}, orphanRemoval=true)
     * @Assert\Valid
     *
     * @var ArrayCollection|ProductCity[]
     */
    protected $productCities;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File", columnPrefix="photo_")
     *
     * @var EmbeddableFile
     */
    protected $photo;

    /**
     * @Vich\UploadableField(mapping="catalog_product_photo", fileNameProperty="photo.name", originalName="photo.originalName", mimeType="photo.mimeType", size="photo.size")
     * @Image(maxSize="10M")
     *
     * @var File|UploadedFile
     */
    protected $uploadedPhoto;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @var \DateTime
     */
    protected $createdAt;

    use Updateable;

    use Attributable;

    public function __construct()
    {
        $this->productAttributesValues = new ArrayCollection();
        $this->productCities = new ArrayCollection();
        $this->photo = new EmbeddableFile();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->size = '';
        $this->description = '';
        $this->isTitleNonUnique = false;

        $this->addProductCity(new ProductCity());
    }

    public function getAuthor()
    {
        return $this->createdBy;
    }

    public function getId()
    {
        return $this->id;
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

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = CustomerProduct::normalizeTitle($title);
    }

    public function getIsTitleNonUnique()
    {
        return $this->isTitleNonUnique;
    }

    public function setIsTitleNonUnique($isTitleNonUnique)
    {
        $this->isTitleNonUnique = $isTitleNonUnique;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setSize($size)
    {
        $this->size = preg_replace("/(\\d)([а-яА-Яa-zA-Z])/i", "$1 $2", CustomerProduct::normalizeTitle($size));
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = (string)$description;
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

    /**
     * @return AttributeValue
     */
    public function getManufacturer()
    {
        return $this->manufacturer;
    }

    public function setManufacturer(AttributeValue $manufacturer = null)
    {
        $this->manufacturer = $manufacturer;
        $this->attributeValues[$manufacturer->getAttribute()->getCode()] = $manufacturer;
    }

    /**
     * @return AttributeValue
     */
    public function getBrand()
    {
        return $this->brand;
    }

    public function setBrand(AttributeValue $brand = null)
    {
        $this->brand = $brand;
        $this->attributeValues[$brand->getAttribute()->getCode()] = $brand;
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

    /**
     * @return ArrayCollection|ProductCity[]
     */
    public function getProductCities()
    {
        return $this->productCities;
    }

    public function getProductCitiesIds()
    {
        $ids = array();
        foreach ($this->getProductCities() as $productCity) {
            $ids[] = $productCity->getCity()->getId();
        }

        return $ids;
    }

    public function addProductCity(ProductCity $productCity)
    {
        $productCity->setProduct($this);
        $this->productCities->add($productCity);
    }

    public function addProductCities(ProductCity $productCity)
    {
        $this->addProductCity($productCity);
    }

    public function removeProductCity(ProductCity $productCity)
    {
        $this->productCities->removeElement($productCity);
    }

    /**
     * @return ArrayCollection|ProductAttributeValue[]
     */
    public function getProductAttributesValues()
    {
        return $this->productAttributesValues;
    }

    public function addProductAttributeValue(ProductAttributeValue $productAttributeValue)
    {
        $productAttributeValue->setProduct($this);
        $this->productAttributesValues->add($productAttributeValue);
    }

    public function removeProductAttributeValue(ProductAttributeValue $productAttributeValue)
    {
        $this->productAttributesValues->removeElement($productAttributeValue);
    }

    public function initializeAdditionalAttributeValues()
    {
        $this->attributeValues = array();
        foreach ($this->productAttributesValues as $productAttributeValue) {
            $attributeValue = $productAttributeValue->getAttributeValue();
            $this->attributeValues[$attributeValue->getAttribute()->getCode()] = $productAttributeValue->getAttributeValue();
        }
    }

    public function handleAdditionalAttributeValues()
    {
        $currentProductAttributeValues = array();
        /* @var $currentProductAttributeValues ProductAttributeValue[] */
        foreach ($this->productAttributesValues as $productAttributeValue) {
            $attributeValue = $productAttributeValue->getAttributeValue();
            $currentProductAttributeValues[$attributeValue->getAttribute()->getCode()] = $productAttributeValue;
        }

        foreach ($this->attributeValues as $attrCode => $attrValue) {
            if ($attrValue) {
                if (isset($currentProductAttributeValues[$attrCode])) {
                    $currentProductAttributeValues[$attrCode]->setAttributeValue($attrValue);
                } else {
                    $productAttributeValue = new ProductAttributeValue();
                    $productAttributeValue->setAttributeValue($attrValue);
                    $this->addProductAttributeValue($productAttributeValue);
                }

                continue;
            }

            if (isset($currentProductAttributeValues[$attrCode])) {
                $this->removeProductAttributeValue($currentProductAttributeValues[$attrCode]);
            }
        }
    }

    /**
     * @return User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @param User $createdBy
     */
    public function setCreatedBy(User $createdBy)
    {
        $this->createdBy = $createdBy;
    }
}
