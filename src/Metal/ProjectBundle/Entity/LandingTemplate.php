<?php

namespace Metal\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Entity\File as EmbeddableFile;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity
 * @ORM\Table(name="landing_template")
 * @UniqueEntity(fields={"category"})
 *
 * @Vich\Uploadable
 */
class LandingTemplate
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\OneToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true, onDelete="SET NULL")
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File", columnPrefix="file_")
     *
     * @var EmbeddableFile
     */
    protected $file;

    /**
     * @Vich\UploadableField(mapping="landing_template_file", fileNameProperty="file.name", originalName="file.originalName", mimeType="file.mimeType", size="file.size")
     * @Assert\NotBlank(groups={"new_item"})
     *
     * @var File|UploadedFile
     */
    protected $uploadedFile;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    protected $createdAt;

    use Updateable;

    public function __construct()
    {
        $this->file = new EmbeddableFile();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
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
        $this->id = $category->getId();
        $this->category = $category;
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
        }
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
}
