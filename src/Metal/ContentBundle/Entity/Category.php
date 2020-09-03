<?php

namespace Metal\ContentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Metal\CategoriesBundle\Entity\CategoryAbstract;
use Metal\ProjectBundle\Entity\Metadata;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Gedmo\Mapping\Annotation as Gedmo;

/**
 * @ORM\Entity(repositoryClass="Metal\ContentBundle\Repository\CategoryRepository")
 * @ORM\Table(name="content_category")
 * @UniqueEntity("slugCombined")
 *
 * @Gedmo\Tree(type="closure")
 * @Gedmo\TreeClosure(class="Metal\ContentBundle\Entity\CategoryClosure")
 */
class Category extends CategoryAbstract
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @var self[]
     */
    public $loadedChildren = array();

    /**
     * @var self[]
     */
    public $loadedSiblings = array();

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", cascade={"persist"})
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", nullable=true, onDelete="CASCADE")
     *
     * @var Category
     */
    protected $parent;

    /**
     * @ORM\Column(length=255, name="slug", nullable=false, options={"default":""})
     * @Assert\NotBlank()
     */
    protected $slug;

    /**
     * @ORM\Column(name="slug_combined", nullable=false, options={"default":""})
     */
    protected $slugCombined;

    /**
     * @ORM\Column(length=255, name="title")
     * @Assert\NotBlank()
     */
    protected $title;

    /** @ORM\Column(type="boolean", name="is_enabled", nullable=false, options={"default":1}) */
    protected $isEnabled;

    /** @ORM\Column(length=100, name="title_genitive", nullable=true) */
    protected $titleGenitive;

    /** @ORM\Column(length=100, name="title_accusative", nullable=true) */
    protected $titleAccusative;

    /** @ORM\Column(length=100, name="title_prepositional", nullable=true) */
    protected $titlePrepositional;

    /** @ORM\Column(length=100, name="title_ablative", nullable=true) */
    protected $titleAblative;

    /**
     * @ORM\Embedded(class="Metal\ProjectBundle\Entity\Metadata", columnPrefix=false)
     */
    protected $metadata;

    /**
     * @return Metadata
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * @param Metadata $metadata
     */
    public function setMetadata(Metadata $metadata)
    {
        $this->metadata = $metadata;
    }

    public static function getClosureTableName()
    {
        return 'content_category_closure';
    }

    public function __construct()
    {
        parent::__construct();

        $this->isEnabled = true;
        $this->metadata = new Metadata();
    }

    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    public function isLabel()
    {
        return false;
    }

    public function getTitleAccusative()
    {
        return $this->titleAccusative;
    }

    public function getTitleGenitive()
    {
        return $this->titleGenitive;
    }

    public function getTitlePrepositional()
    {
        return $this->titlePrepositional;
    }

    public function getTitleAblative()
    {
        return $this->titleAblative;
    }

    public function setTitleAccusative($titleAccusative)
    {
        $this->titleAccusative = $titleAccusative;
    }

    public function setTitleGenitive($titleGenitive)
    {
        $this->titleGenitive = $titleGenitive;
    }

    public function setTitlePrepositional($titlePrepositional)
    {
        $this->titlePrepositional = $titlePrepositional;
    }

    public function setTitleAblative($titleAblative)
    {
        $this->titleAblative = $titleAblative;
    }
}
