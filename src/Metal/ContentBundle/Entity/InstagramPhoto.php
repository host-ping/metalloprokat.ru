<?php

namespace Metal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Symfony\Component\HttpFoundation\File\File;
use Metal\ProjectBundle\Validator\Constraints\Image;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

use Vich\UploaderBundle\Entity\File as EmbeddableFile;

/**
 * @ORM\Entity()
 * @ORM\Table(name="instagram_photo")
 *
 * @Vich\Uploadable
 */
class InstagramPhoto
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="photo_id")
     */
    protected $photoId;

    /**
     * @ORM\Column(length=255, name="code")
     */
    protected $code;

    /**
     * @ORM\ManyToOne(targetEntity="InstagramUser")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     * @var InstagramUser
     */
    protected $user;

    /** @ORM\Column(length=255, name="url", unique=true) */
    protected $url;

    /** @ORM\Column(length=255, name="description", nullable=true) */
    protected $description;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File", columnPrefix="image_")
     *
     * @var EmbeddableFile
     */
    protected $photo;

    /**
     * @Vich\UploadableField(mapping="instagram_photo", fileNameProperty="photo.name", originalName="photo.originalName", mimeType="photo.mimeType", size="photo.size")
     * @Image(maxSize="10M")
     *
     * @var File|UploadedFile
     */
    protected $uploadedPhoto;

    /**
     * @ORM\Column(type="array", name="tags")
     */
    protected $tags;

    /**
     * @ORM\Column(length=255, name="repost_id", nullable=true)
     */
    protected $repostId;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $createdAt;

    use Updateable;

    public function __construct()
    {
        $this->photo = new EmbeddableFile();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPhotoId()
    {
        return $this->photoId;
    }

    public function setPhotoId($photoId)
    {
        $this->photoId = $photoId;
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

    public function getCode()
    {
        return $this->code;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return InstagramUser
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param InstagramUser $parseUser
     */
    public function setUser(InstagramUser $parseUser)
    {
        $this->user = $parseUser;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return array
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * @param array $tags
     */
    public function setTags(array $tags)
    {
        $this->tags = $tags;
    }

    public function getRepostId()
    {
        return $this->repostId;
    }

    public function setRepostId($repostId)
    {
        $this->repostId = $repostId;
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
