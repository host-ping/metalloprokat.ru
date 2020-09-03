<?php

namespace Metal\CatalogBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\ProjectBundle\Entity\Behavior\Attributable;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Metal\ProjectBundle\Validator\Constraints\Image;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddableFile;

/**
 * @ORM\Entity(repositoryClass="Metal\CatalogBundle\Repository\ManufacturerRepository")
 * @ORM\Table(name="catalog_manufacturer")
 *
 * @Vich\Uploadable
 */
class Manufacturer
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=100, name="title", nullable=false)
     * @Assert\NotBlank()
     */
    protected $title;

    /**
     * @ORM\Column(length=100, name="slug", nullable=false, unique=true)
     * @Assert\NotBlank()
     */
    protected $slug;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\AttributesBundle\Entity\AttributeValue")
     * @ORM\JoinColumn(name="attribute_value_id", referencedColumnName="id", nullable=false)
     *
     * @var AttributeValue
     */
    protected $attributeValue;

    /**
     * @ORM\Column(name="description", type="text")
     */
    protected $description;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File", columnPrefix="logo_")
     *
     * @var EmbeddableFile
     */
    protected $logo;

    /**
     * @Vich\UploadableField(mapping="manufacturer_logo", fileNameProperty="logo.name", originalName="logo.originalName", mimeType="logo.mimeType", size="logo.size")
     * @Image(maxSize="10M")
     *
     * @var File|UploadedFile
     */
    protected $uploadedLogo;

    /**
     * @Assert\Url(protocols={"http", "https"})
     * @ORM\Column(length=100, name="site", nullable=true)
     */
    protected $site;

    use Attributable;

    use Updateable;

    public function __construct()
    {
        $this->logo = new EmbeddableFile();
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return AttributeValue
     *
     */
    public function getAttributeValue()
    {
        return $this->attributeValue;
    }

    /**
     * @param AttributeValue $attributeValue
     */
    public function setAttributeValue(AttributeValue $attributeValue)
    {
        $this->attributeValue = $attributeValue;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getCutDescription()
    {
        $arr = explode('.', $this->description);

        return current($arr);
    }

    public function getSite()
    {
        return $this->site;
    }

    public function setSite($site)
    {
        $this->site = $site;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return EmbeddableFile
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * @param EmbeddableFile $logo
     */
    public function setLogo(EmbeddableFile $logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return File|UploadedFile
     */
    public function getUploadedLogo()
    {
        return $this->uploadedLogo;
    }

    public function setUploadedLogo(File $uploadedLogo = null)
    {
        $this->uploadedLogo = $uploadedLogo;
        if ($this->uploadedLogo) {
            $this->setUpdatedAt(new \DateTime());
        }
    }
}
