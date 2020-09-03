<?php

namespace Metal\ContentBundle\Entity;

use Metal\ProjectBundle\Validator\Constraints\Image;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddableFile;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="Metal\ContentBundle\Repository\ContentEntryRepository")
 *
 * @Vich\Uploadable
 */
class Topic extends AbstractContentEntry
{
    const SUBDIR = 'topics';

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File", columnPrefix="image_")
     *
     * @var EmbeddableFile
     */
    protected $image;

    /**
     * @Vich\UploadableField(mapping="topic_image", fileNameProperty="image.name", originalName="image.originalName", mimeType="image.mimeType", size="image.size")
     * @Image(maxSize="10M")
     *
     * @var File|UploadedFile
     */
    protected $uploadedImage;

    public function __construct()
    {
        parent::__construct();
        $this->image = new EmbeddableFile();
    }

    /**
     * @return EmbeddableFile
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param EmbeddableFile $image
     */
    public function setImage(EmbeddableFile $image)
    {
        $this->image = $image;
    }

    public function getKind()
    {
        return 'topic';
    }

    /**
     * @return File|UploadedFile
     */
    public function getUploadedImage()
    {
        return $this->uploadedImage;
    }

    public function setUploadedImage(File $uploadedImage = null)
    {
        $this->uploadedImage = $uploadedImage;
        if ($this->uploadedImage) {
            $this->setUpdatedAt(new \DateTime());
        }
    }
}
