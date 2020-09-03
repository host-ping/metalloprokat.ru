<?php

namespace Metal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Metal\ProjectBundle\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddableFile;

/**
 * @ORM\Entity(repositoryClass="Metal\ContentBundle\Repository\ContentImageRepository")
 * @ORM\Table(name="content_image")
 *
 * @Vich\Uploadable
 */
class ContentImage
{
    const SUBDIR = 'netcat_files/188/372';

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID", nullable=false)
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\Column(type="smallint", name="priority", nullable=false, options={"default":0})
     */
    protected $priority;

    /**
     * @ORM\Column(type="boolean", name="checked", nullable=false, options={"default":1})
     */
    protected $checked;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\ManyToOne(targetEntity="ContentImageAlbum")
     * @ORM\JoinColumn(name="album_id", referencedColumnName="id", nullable=false)
     *
     * @var ContentImageAlbum
     */
    protected $album;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File", columnPrefix="image_")
     *
     * @var EmbeddableFile
     */
    protected $image;

    /**
     * @Vich\UploadableField(mapping="content_image", fileNameProperty="image.name", originalName="image.originalName", mimeType="image.mimeType", size="image.size")
     * @Image(maxSize="10M")
     *
     * @var File|UploadedFile
     */
    protected $uploadedImage;

    use Updateable;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->image = new EmbeddableFile();
        $this->priority = 0;
        $this->checked = true;
    }

    public function getId()
    {
        return $this->id;
    }

    /**
     * @return ContentImageAlbum
     */
    public function getAlbum()
    {
        return $this->album;
    }

    /**
     * @param ContentImageAlbum $album
     */
    public function setAlbum(ContentImageAlbum $album)
    {
        $this->album = $album;
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

    public function getChecked()
    {
        return $this->checked;
    }

    public function setChecked($checked)
    {
        $this->checked = $checked;
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
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
