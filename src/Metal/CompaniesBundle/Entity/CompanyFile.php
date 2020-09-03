<?php

namespace Metal\CompaniesBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddableFile;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity
 * @ORM\Table(name="company_file")
 *
 * @ORM\HasLifecycleCallbacks
 *
 * @Vich\Uploadable
 */
class CompanyFile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(name="title")
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=false)
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File", columnPrefix="file_")
     *
     * @var EmbeddableFile
     */
    protected $file;

    /**
     * @Vich\UploadableField(mapping="company_file", fileNameProperty="file.name", originalName="file.originalName", mimeType="file.mimeType", size="file.size")
     * @Assert\NotBlank(groups={"new_item"})
     *
     * @var File|UploadedFile
     */
    protected $uploadedFile;

    use Updateable;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->file = new EmbeddableFile();
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company)
    {
        $this->company = $company;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
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

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    public function setTitle($title)
    {
        $this->title = $title;
    }

    public function getTitle()
    {
        return $this->title;
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

            $filename = $this->uploadedFile->getClientOriginalName();
            $this->setTitle(substr($filename, 0 , (strrpos($filename, "."))));
        }
    }

    public function getExtension()
    {
        return pathinfo($this->file->getOriginalName(), PATHINFO_EXTENSION);
    }
}
