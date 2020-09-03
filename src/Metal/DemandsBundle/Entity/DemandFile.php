<?php

namespace Metal\DemandsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Entity\File as EmbeddableFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="Metal\DemandsBundle\Repository\DemandFileRepository")
 * @ORM\Table(name="demand_file")
 *
 * @Vich\Uploadable
 */
class DemandFile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="AbstractDemand", inversedBy="demandFiles")
     * @ORM\JoinColumn(name="demand_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @var AbstractDemand
     */
    protected $demand;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File", columnPrefix=false)
     *
     * @var EmbeddableFile
     */
    protected $file;

    /**
     * @Vich\UploadableField(mapping="demand_attachment", fileNameProperty="file.name", originalName="file.originalName", size="file.size", mimeType="file.mimeType")
     * @Assert\File(maxSize="10M")
     *
     * @var File
     */
    protected $uploadedFile;

    use Updateable;

    /**
     * @ORM\Column(type="boolean", name="is_processed", nullable=false, options={"default":0})
     */
    public $isProcessed;

    public function __construct()
    {
        $this->file = new EmbeddableFile();
        $this->updatedAt = new \DateTime();
        $this->isProcessed = true;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return AbstractDemand
     */
    public function getDemand()
    {
        return $this->demand;
    }

    /**
     * @param AbstractDemand $demand
     */
    public function setDemand(AbstractDemand $demand)
    {
        $this->demand = $demand;
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
     * @return File
     */
    public function getUploadedFile()
    {
        return $this->uploadedFile;
    }

    public function setUploadedFile(File $uploadedFile = null)
    {
        $this->uploadedFile = $uploadedFile;
        if ($this->uploadedFile) {
            $this->setUpdated();
        }
    }

    public function getIsProcessed()
    {
        return $this->isProcessed;
    }

    public function setIsProcessed($isProcessed)
    {
        $this->isProcessed = (bool)$isProcessed;
    }
}
