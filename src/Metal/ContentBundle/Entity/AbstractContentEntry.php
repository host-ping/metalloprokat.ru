<?php

namespace Metal\ContentBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Metal\ContentBundle\Entity\ValueObject\StatusType;
use Metal\ContentBundle\Entity\ValueObject\StatusTypeProvider;
use Metal\ContentBundle\Entity\ValueObject\SubjectType;
use Metal\ContentBundle\Entity\ValueObject\SubjectTypeProvider;
use Metal\ProjectBundle\Entity\Behavior\Attributable;
use Metal\ProjectBundle\Entity\Behavior\Updateable;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="Metal\ContentBundle\Repository\ContentEntryRepository")
 * @ORM\Table(name="content_entry", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_type_id", columns={"entry_type", "id"})})
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="entry_type", type="smallint")
 * @ORM\DiscriminatorMap({1="Topic", 2="Question"})
 * @ORM\HasLifecycleCallbacks()
 */
abstract class AbstractContentEntry
{
    const ENTRY_TYPE_TOPIC = 1;
    const ENTRY_TYPE_QUESTION = 2;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="content_entry_id")
     */
    protected $contentEntryId;

    /**
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=255, name="title", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(min=5)
     */
    protected $title;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\UsersBundle\Entity\User")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="User_ID")
     *
     * @var User
     */
    protected $user;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="category_secondary_id", referencedColumnName="id", nullable=true)
     *
     * @var Category
     */
    protected $categorySecondary;

    /**
     * @ORM\OneToMany(targetEntity="ContentEntryTag", mappedBy="contentEntry", cascade={"persist"}, orphanRemoval=true)
     *
     * @var ArrayCollection|ContentEntryTag[]
     */
    protected $contentEntryTags;

    /**
     * @ORM\Column(type="datetime", name="created_at", nullable=false)
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="integer", name="status_type_id")
     */
    protected $statusTypeId;

    /**
     * @var StatusType
     */
    protected $statusType;

    /**
     * @ORM\Column(type="integer", name="subject_type_id")
     * @Assert\NotBlank()
     */
    protected $subjectTypeId;

    /**
     * @var SubjectType
     */
    protected $subjectType;

    /**
     * @ORM\Column(type="text", name="description", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(min=5)
     */
    protected $description;

    /**
     * @ORM\Column(type="text", name="short_description", nullable=false)
     * @Assert\NotBlank()
     * @Assert\Length(min=5)
     */
    protected $shortDescription;

    /**
     * @ORM\Column(length=255, name="page_title", nullable=true)
     * @Assert\Length(min=5)
     */
    protected $pageTitle;

    /**
     * @ORM\Column(type="boolean", name="notify", nullable=false, options={"default":0})
     */
    protected $notify;

    /**
     * @ORM\Column(length=255, name="email", nullable=true)
     * @Assert\Length(min=5)
     * @Assert\Email(strict=true)
     * @Assert\NotBlank(groups={"anonymous"})
     */
    protected $email;

    /**
     * @ORM\Column(length=255, name="name", nullable=true)
     * @Assert\Length(min=5)
     * @Assert\NotBlank(groups={"anonymous"})
     */
    protected $name;

    use Attributable;

    use Updateable;

    public function __construct()
    {
        $this->id = 0;
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
        $this->contentEntryTags = new ArrayCollection();
        $this->notify = false;
        $this->setStatusTypeId(StatusTypeProvider::NOT_CHECKED);
    }

    abstract public function getKind();

    public function getContentEntryId()
    {
        return $this->contentEntryId;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
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
     * @return Category
     */
    public function getCategorySecondary()
    {
        return $this->categorySecondary;
    }

    /**
     * @param Category $categorySecondary
     */
    public function setCategorySecondary(Category $categorySecondary)
    {
        $this->categorySecondary = $categorySecondary;
    }

    public function getStatusTypeId()
    {
        return $this->statusTypeId;
    }

    public function setStatusTypeId($statusTypeId)
    {
        $this->statusTypeId = $statusTypeId;
        $this->statusType = null;
    }

    /**
     * @return StatusType
     */
    public function getStatusType()
    {
        if (null !== $this->statusType) {
            return $this->statusType;
        }

        return $this->statusType = StatusTypeProvider::create($this->statusTypeId);
    }

    /**
     * @param StatusType $statusType
     */
    public function setStatusType(StatusType $statusType)
    {
        $this->statusType = $statusType;
        $this->statusTypeId = $statusType->getId();
    }

    public function getSubjectTypeId()
    {
        return $this->subjectTypeId;
    }

    public function setSubjectTypeId($subjectTypeId)
    {
        $this->subjectTypeId = $subjectTypeId;
        $this->statusType = null;
    }

    /**
     * @return SubjectType
     */
    public function getSubjectType()
    {
        if (null !== $this->subjectType) {
            return $this->subjectType;
        }

        return $this->subjectType = SubjectTypeProvider::create($this->subjectTypeId);
    }

    /**
     * @param SubjectType $subjectType
     */
    public function setSubjectType(SubjectType $subjectType)
    {
        $this->subjectType = $subjectType;
        $this->subjectTypeId = $subjectType->getId();
    }

    /**
     * @return ArrayCollection|ContentEntryTag[]
     */
    public function getContentEntryTags()
    {
        return $this->contentEntryTags;
    }

    public function addContentEntryTag(ContentEntryTag $questionTag)
    {
        $questionTag->setContentEntry($this);
        $this->contentEntryTags->add($questionTag);
    }

    public function removeContentEntryTag(ContentEntryTag $questionTag)
    {
        $this->contentEntryTags->removeElement($questionTag);
    }

    public function getTags()
    {
        $tags = array();
        foreach ($this->getContentEntryTags() as $contentEntryTag) {
            $tags[] = $contentEntryTag->getTag();
        }

        return $tags;
    }

    public function addTag(Tag $tag)
    {
        $questionTag = new ContentEntryTag();
        $questionTag->setTag($tag);
        $this->addContentEntryTag($questionTag);
    }

    public function removeTag(Tag $tag)
    {
        foreach ($this->getContentEntryTags() as $contentEntryTag) {
            if ($contentEntryTag->getTag() === $tag) {
                $this->contentEntryTags->removeElement($contentEntryTag);
                break;
            }
        }
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getShortDescription()
    {
        return $this->shortDescription;
    }

    public function setShortDescription($shortDescription)
    {
        $this->shortDescription = $shortDescription;
    }

    public function getPageTitle()
    {
        return $this->pageTitle;
    }

    public function setPageTitle($pageTitle)
    {
        $this->pageTitle = $pageTitle;
    }

    public function getNotify()
    {
        return $this->notify;
    }

    public function setNotify($notify)
    {
        $this->notify = $notify;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getBranchTitle()
    {
        $fullPath[] = $this->getCategory()->getTitle();
        $parentCategory = $this->getCategory();
        while ($parentCategory = $parentCategory->getParent()) {
            $fullPath[] = $parentCategory->getTitle();
        }

        return implode(' / ', $fullPath);
    }

    /**
     * @ORM\PostPersist()
     */
    public function generateEntityId(LifecycleEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();

        $contentEntryId = $em->getUnitOfWork()->getSingleIdentifierValue($this);

        $entryType = 1;
        if ($this instanceof Question) {
            $entryType = 2;
        }

        $id = $em->getConnection()->fetchColumn(
            'SELECT IFNULL(MAX(id), 0) + 1 FROM content_entry WHERE entry_type = :entry_type',
            array('entry_type' => $entryType)
        );
        $em->getConnection()->update('content_entry', array('id' => $id), array('content_entry_id' => $contentEntryId));
        $this->id = $id;
    }

    public function getCategoriesIds()
    {
        $categoriesIds = array($this->getCategory()->getId());
        if ($this->getCategorySecondary()) {
            $categoriesIds[] = $this->getCategorySecondary()->getId();
        }
        return $categoriesIds;
    }
}
