<?php

namespace Metal\CategoriesBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Metal\ProjectBundle\Entity\Metadata;
use Metal\ProjectBundle\Helper\TextHelperStatic;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasure;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;

/**
 * @ORM\Entity(repositoryClass="Metal\CategoriesBundle\Repository\CategoryRepository")
 * @UniqueEntity("slug")
 * @ORM\Table(name="Message73")
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\Tree(type="closure")
 * @Gedmo\TreeClosure(class="Metal\CategoriesBundle\Entity\CategoryClosure")
 */
class Category extends CategoryAbstract
{
    const CATEGORY_ID_TRUBA = 16;
    const CATEGORY_ID_TRUBA_ST = 516;
    const CATEGORY_ID_DRUGOE = 479;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="Message_ID")
     */
    protected $id;

    /**
     * @ORM\Column(type="integer", name="cat_parent")
     *
     * @deprecated
     */
    protected $parentId;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="children", cascade={"persist"})
     * @ORM\JoinColumn(name="cat_parent", referencedColumnName="Message_ID", nullable=true, onDelete="CASCADE")
     *
     * @var Category
     */
    protected $parent;

    /**
     * @ORM\Column(length=255, name="Keyword", unique=true)
     * @Assert\NotBlank()
     */
    protected $slug;

    /**
     * @ORM\Column(name="slug_combined", nullable=false, options={"default":""})
     */
    protected $slugCombined;

    /**
     * @ORM\Column(length=255, name="cat_name")
     * @Assert\NotBlank()
     */
    protected $title;

    /** @ORM\Column(length=255, name="redirect_slug", nullable=true) */
    protected $redirectSlug;

    /**
     * @ORM\Column(type="boolean", name="chCount", nullable=false, options={"default":0})
     */
    protected $hasChildren;

    /** @ORM\OneToMany(targetEntity="Category", mappedBy="parent") */
    protected $children;

    /** @ORM\Column(type="boolean", name="has_siblings", nullable=true) */
    protected $hasSiblings;

    /** @ORM\Column(type="boolean", name="Checked", nullable=false, options={"default":1}) */
    protected $isEnabled;

    /** @ORM\Column(type="boolean", name="is_enabled_metalspros", nullable=true) */
    protected $isEnabledMetalspros;

    /** @ORM\Column(length=100, name="title_accusative", nullable=true) */
    protected $titleAccusative;

    /** @ORM\Column(length=100, name="title_genitive", nullable=true) */
    protected $titleGenitive;

    /** @ORM\Column(length=100, name="title_prepositional", nullable=true) */
    protected $titlePrepositional;

    /** @ORM\Column(length=100, name="title_ablative", nullable=true) */
    protected $titleAblative;

    /**
     * @ORM\Column(name="virtual_parents_ids", nullable=true)
     * @Assert\Regex(pattern="/^[\d\,\s]+$/", message="Можно использовать только цифры, пробел и запятую.")
     */
    protected $virtualParentsIds;

    /**
     * @ORM\Column(name="virtual", type="boolean", nullable=false, options={"default":false})
     */
    protected $virtual;

    /**
     * @ORM\Column(name="noindex", type="boolean", nullable=false, options={"default":false})
     */
    protected $noindex;

    /**
     * @ORM\Column(type="boolean", name="allow_products", nullable=false, options={"default":true})
     * @Assert\NotNull()
     */
    protected $allowProducts;

    /**
     * @ORM\OneToMany(targetEntity="ParameterGroup", mappedBy="category")
     * @ORM\OrderBy({"priority"="ASC"})
     * @deprecated
     */
    protected $parameterGroup;

    /**
     * @ORM\ManyToOne(targetEntity="Category")
     * @ORM\JoinColumn(name="real_category", referencedColumnName="Message_ID", nullable=true)
     *
     * @var Category
     */
    protected $realCategory;

    /**
     * @ORM\OneToOne(targetEntity="CategoryExtended")
     * @ORM\JoinColumn(name="category_extended_id", referencedColumnName="id", onDelete="SET NULL")
     *
     * @var CategoryExtended
     */
    protected $categoryExtended;

    /**
     * @ORM\Column(name="is_visible_for_seo", type="boolean", nullable=true)
     */
    protected $isVisibleForSeo;

    /** @ORM\Column(length=100, name="title_for_seo", nullable=true) */
    protected $titleForSeo;

    /**
     * @ORM\Embedded(class="Metal\ProjectBundle\Entity\Metadata", columnPrefix=false)
     */
    protected $metadata;

    /**
     * @ORM\OneToMany(targetEntity="CategoryCityMetadata", mappedBy="category", cascade={"persist"}, orphanRemoval=true)
     *
     * @var ArrayCollection|CategoryCityMetadata[]
     */
    protected $categoryCityMetadatas;

    protected $newCategoryExtended;

    /**
     * @ORM\Column(name="check_age", type="boolean", nullable=false, options={"default":false})
     */
    protected $checkAge;

    /**
     * @ORM\Column(type="integer", name="volume_type", nullable=true)
     */
    protected $volumeTypeId;

    /**
     * @var ProductMeasure
     */
    protected $volumeType;

    public static function getClosureTableName()
    {
        return 'category_closure';
    }

    public static function getSlugColumnName()
    {
        return 'Keyword';
    }

    public function __construct()
    {
        parent::__construct();

        $this->virtual = false;
        $this->noindex = false;
        $this->hasChildren = false;
        $this->isEnabled = true;
        $this->isEnabledMetalspros = true;
        $this->allowProducts = true;
        $this->newCategoryExtended = new CategoryExtended();
        $this->metadata = new Metadata();
        $this->categoryCityMetadatas = new ArrayCollection();
        $this->setVolumeTypeId(1);
    }

    /**
     * @ORM\PostPersist()
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $em = $args->getEntityManager();
        $categoryExtended = $this->getCategoryExtended();
        $categoryExtended->setCategory($this);
        $em->persist($categoryExtended);
        $this->setCategoryExtended($categoryExtended);

        $em->flush();

        $em->getRepository('MetalCategoriesBundle:Category')->refreshCategoryData($this);
    }

    /**
     * @return Category
     */
    public function getRealCategory()
    {
        return $this->realCategory;
    }

    /**
     * @param Category $realCategory
     */
    public function setRealCategory($realCategory)
    {
        $this->realCategory = $realCategory;
    }

    public function getVirtual()
    {
        return $this->virtual;
    }

    public function setVirtual($virtual)
    {
        $this->virtual = $virtual;
    }

    public function getNoindex()
    {
        return $this->noindex;
    }

    public function setNoindex($noindex)
    {
        $this->noindex = $noindex;
    }

    public function getRedirectSlug()
    {
        return $this->redirectSlug;
    }

    public function setRedirectSlug($redirectSlug)
    {
        $this->redirectSlug = $redirectSlug;
    }

    public function getUrl($slugs)
    {
        $slugParts = array();
        $slugParts[] = $this->slugCombined;
        if ($slugs && is_string($slugs)) {
            $slugParts[] = $slugs;
        }

        return implode('/', $slugParts);
    }

    public function getVirtualParentsIds()
    {
        return $this->virtualParentsIds;
    }

    public function setVirtualParentsIds($virtualParentsIds)
    {
        $this->virtualParentsIds = $virtualParentsIds;
    }

    public function getHasChildren()
    {
        return $this->hasChildren;
    }

    public function setHasChildren($hasChild)
    {
        $this->hasChildren = $hasChild;
    }

    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return ArrayCollection|CategoryCityMetadata[]
     */
    public function getCategoryCityMetadatas()
    {
        return $this->categoryCityMetadatas;
    }

    public function addCategoryCityMetadata(CategoryCityMetadata $categoryCityMetadata)
    {
        $categoryCityMetadata->setCategory($this);
        $this->categoryCityMetadatas->add($categoryCityMetadata);
    }

    public function addCategoryCityMetadatas(CategoryCityMetadata $categoryCityMetadata)
    {
        $this->addCategoryCityMetadata($categoryCityMetadata);
    }

    public function removeCategoryCityMetadata(CategoryCityMetadata $categoryCityMetadata)
    {
        $this->categoryCityMetadatas->removeElement($categoryCityMetadata);
    }

    public function getTitleForEmbed()
    {
        return TextHelperStatic::normalizeTitleForEmbed($this->getTitle());
    }

    public function getTitleAccusative()
    {
        if ($this->titleAccusative) {
            return $this->titleAccusative;
        }

        $title = TextHelperStatic::declinePhraseAccusative($this->getTitle());
        $title = mb_strtolower(mb_substr($title, 0, 1)).mb_substr($title, 1);

        return $title;
    }

    public function getTitleAccusativeForEmbed()
    {
        return TextHelperStatic::normalizeTitleForEmbed($this->getTitleAccusative());
    }

    public function getTitleGenitive()
    {
        if ($this->titleGenitive) {
            return $this->titleGenitive;
        }

        $title = TextHelperStatic::declinePhraseGenitive($this->getTitle());
        $title = mb_strtolower(mb_substr($title, 0, 1)).mb_substr($title, 1);

        return $title;
    }

    public function getTitleGenitiveForEmbed()
    {
        return TextHelperStatic::normalizeTitleForEmbed($this->getTitleGenitive());
    }

    public function getTitlePrepositional()
    {
        if ($this->titlePrepositional) {
            return $this->titlePrepositional;
        }

        $title = TextHelperStatic::declinePhrasePrepositional($this->getTitle());
        $title = mb_strtolower(mb_substr($title, 0, 1)).mb_substr($title, 1);

        return $title;
    }

    public function getTitlePrepositionalForEmbed()
    {
        return TextHelperStatic::normalizeTitleForEmbed($this->getTitlePrepositional());
    }

    public function getTitleAblative()
    {
        if ($this->titleAblative) {
            return $this->titleAblative;
        }

        $title = TextHelperStatic::declinePhraseAblative($this->getTitle());
        $title = mb_strtolower(mb_substr($title, 0, 1)).mb_substr($title, 1);

        return $title;
    }

    public function getTitleAblativeForEmbed()
    {
        return TextHelperStatic::normalizeTitleForEmbed($this->getTitleAblative());
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

    public function getTitleForSeo()
    {
        return $this->titleForSeo;
    }

    public function setTitleForSeo($titleForSeo)
    {
        $this->titleForSeo = $titleForSeo;
    }

    /**
     * @return CategoryExtended
     */
    public function getCategoryExtended()
    {
        return $this->categoryExtended ?: $this->newCategoryExtended;
    }

    /**
     * @param CategoryExtended $categoryExtended
     */
    public function setCategoryExtended(CategoryExtended $categoryExtended)
    {
        $this->categoryExtended = $categoryExtended;
    }

    public function getIsVisibleForSeo()
    {
        return $this->isVisibleForSeo;
    }

    public function setIsVisibleForSeo($isVisibleForSeo)
    {
        $this->isVisibleForSeo = $isVisibleForSeo;
    }

    public function setHasSiblings($hasSiblings)
    {
        $this->hasSiblings = $hasSiblings;
    }

    public function getHasSiblings()
    {
        return $this->hasSiblings;
    }

    public function setIsEnabledMetalspros($isEnabledMetalspros)
    {
        $this->isEnabledMetalspros = $isEnabledMetalspros;
    }

    public function getIsEnabledMetalspros()
    {
        return $this->isEnabledMetalspros;
    }

    public function setIsEnabled($isEnabled)
    {
        $this->isEnabled = $isEnabled;
    }

    public function getIsEnabled()
    {
        return $this->isEnabled;
    }

    public function setAllowProducts($allowProducts)
    {
        $this->allowProducts = $allowProducts;
    }

    public function getAllowProducts()
    {
        return $this->allowProducts;
    }

    public function getRealCategoryId()
    {
        if ($this->getRealCategory()) {
            return $this->getRealCategory()->getId();
        }

        return $this->id;
    }

    public function setParameterGroup($parameterGroup)
    {
        $this->parameterGroup = $parameterGroup;
    }

    public function getParameterGroup()
    {
        return $this->parameterGroup;
    }


    public function getNestedTitleAndIsAllowedAddProducts()
    {
        $string = ' - (not allowed add products)';
        if ($this->allowProducts) {
            $string = ' - (allowed add products)';
        }

        return $this->getNestedTitle().$string;
    }

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

    public function getDescription()
    {
        return $this->getCategoryExtended()->getDescription();
    }

    public function setDescription($description)
    {
        $this->getCategoryExtended()->setDescription($description);
    }

    /**
     * @deprecated use getTitleWithString instead
     *
     * @return string
     */
    public function __toString()
    {
        $level = 0;
        $prefix = "";
        $parent = $this->getParent();
        while ($parent && $parent = $parent->getParent()) {
            $level++;
        }

        for ($i = 1; $i <= $level; $i++) {
            $prefix .= '---';
        }

        return $prefix.$this->title;
    }

    /**
     * @return mixed
     */
    public function getCheckAge()
    {
        return $this->checkAge;
    }

    /**
     * @param mixed $checkAge
     */
    public function setCheckAge($checkAge)
    {
        $this->checkAge = $checkAge;
    }

    public function getVolumeTypeId()
    {
        return $this->volumeTypeId;
    }

    public function setVolumeTypeId($volumeTypeId)
    {
        $this->volumeTypeId = $volumeTypeId;
        $this->initializeVolumeType();
    }

    public function getVolumeType(): ?ProductMeasure
    {
        return $this->volumeType;
    }

    public function getVolumeTypeTitle()
    {
        return $this->volumeType->getTitle();
    }

    public function setVolumeType(ProductMeasure $volumeType)
    {
        $this->volumeType = $volumeType;
        $this->volumeTypeId = $volumeType->getId();
    }

    /**
     * @ORM\PostLoad
     */
    public function initializeVolumeType()
    {
        $this->volumeType = null;
        if ($this->volumeTypeId !== null) {
            $this->volumeType = ProductMeasureProvider::create($this->volumeTypeId);
        }
    }
}
