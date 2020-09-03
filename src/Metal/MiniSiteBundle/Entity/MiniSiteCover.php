<?php

namespace Metal\MiniSiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Metal\CompaniesBundle\Entity\Company;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Metal\ProjectBundle\Validator\Constraints\Image;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddableFile;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @ORM\Entity(repositoryClass="Metal\MiniSiteBundle\Repository\MiniSiteCoverRepository")
 * @ORM\Table(name="mini_site_cover")
 *
 * @ORM\HasLifecycleCallbacks
 * @Vich\Uploadable
 */
class MiniSiteCover
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /** @ORM\Column(type="datetime", name="created_at") */
    protected $createdAt;

    /**
     * @ORM\OneToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File", columnPrefix="file_")
     *
     * @var EmbeddableFile
     */
    protected $file;

    /**
     * @Vich\UploadableField(mapping="mini_site_cover", fileNameProperty="file.name", originalName="file.originalName", mimeType="file.mimeType", size="file.size")
     * @Assert\NotBlank(groups={"new_item"})
     *  @Image(
     *   minWidth="1008",
     *   minHeight="192"
     * )
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

    public function getId()
    {
        return $this->id;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company = null)
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
}
