<?php

namespace Metal\CorpsiteBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Metal\CompaniesBundle\Entity\Company;
use Metal\ProjectBundle\Entity\Behavior\SoftDeleteable;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Vich\UploaderBundle\Entity\File as EmbeddableFile;
use Metal\ProjectBundle\Validator\Constraints\Image;

/**
 * @ORM\Entity(repositoryClass="Metal\CorpsiteBundle\Repository\ClientReviewRepository")
 * @ORM\Table(name="client_review")
 *
 * @Vich\Uploadable
 */
class ClientReview
{
    const SUBDIR = 'corpsite';

    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * @ORM\Column(name="position", nullable=true)
     */
    protected $position;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(length=140, name="short_comment")
     * @Assert\NotBlank()
     * @Assert\Length(max="140")
     */
    protected $shortComment;

    /**
     * @ORM\Column(length=2000, name="comment")
     * @Assert\NotBlank()
     * @Assert\Length(max="2000")
     */
    protected $comment;

    /**
     * @ORM\Column(length=255, name="name", nullable=true)
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    protected $name;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="deleted_by", referencedColumnName="User_ID", nullable=true)
     */
    protected $deletedBy;

    /**
     * @ORM\Column(type="datetime", name="moderated_at", nullable=true)
     *
     * @var \DateTime
     */
    protected $moderatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="moderated_by", referencedColumnName="User_ID", nullable=true)
     *
     * @var User
     */
    protected $moderatedBy;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CompaniesBundle\Entity\Company")
     * @ORM\JoinColumn(name="company_id", referencedColumnName="Message_ID", nullable=false, onDelete="CASCADE")
     *
     * @var Company
     */
    protected $company;

    /**
     * @ORM\Embedded(class="Vich\UploaderBundle\Entity\File", columnPrefix="photo_")
     *
     * @var EmbeddableFile
     */
    protected $photo;

    /**
     * @Vich\UploadableField(mapping="client_photo", fileNameProperty="photo.name", originalName="photo.originalName", mimeType="photo.mimeType", size="photo.size")
     * @Image(maxSize="10M")
     * @Assert\NotBlank(groups={"new_item"})
     * @Assert\Image()
     *
     * @var File|UploadedFile
     */
    protected $uploadedPhoto;

    use SoftDeleteable;

    use Updateable;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->photo = new EmbeddableFile();
        $this->position = '';
        $this->setModerated();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;
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

    public function getShortComment()
    {
        return $this->shortComment;
    }

    public function setShortComment($shortComment)
    {
        $this->shortComment = $shortComment;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setComment($comment)
    {
        $this->comment = $comment;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return User
     */
    public function getDeletedBy()
    {
        return $this->deletedBy;
    }

    /**
     * @param User $deletedBy
     */
    public function setDeletedBy(User $deletedBy)
    {
        $this->deletedBy = $deletedBy;
    }

    /**
     * @return \DateTime
     */
    public function getModeratedAt()
    {
        return $this->moderatedAt;
    }

    /**
     * @param \DateTime $moderatedAt
     */
    public function setModeratedAt(\DateTime $moderatedAt)
    {
        $this->moderatedAt = $moderatedAt;
    }

    public function setModerated($moderatedAt = true)
    {
        $this->moderatedAt = $moderatedAt ? new \DateTime : null;
    }

    public function isModerated()
    {
        return $this->moderatedAt !== null;
    }

    /**
     * @return User
     */
    public function getModeratedBy()
    {
        return $this->moderatedBy;
    }

    /**
     * @param User $moderatedBy
     */
    public function setModeratedBy(User $moderatedBy)
    {
        $this->moderatedBy = $moderatedBy;
    }

    /**
     * @return Company
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * @param Company $company
     */
    public function setCompany(Company $company = null)
    {
        $this->company = $company;
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

    public function getSubDir()
    {
        return self::SUBDIR;
    }
}
