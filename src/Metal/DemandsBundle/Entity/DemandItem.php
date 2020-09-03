<?php

namespace Metal\DemandsBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasure;
use Metal\ProductsBundle\Entity\ValueObject\ProductMeasureProvider;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="Metal\DemandsBundle\Repository\DemandItemRepository")
 * @ORM\Table(name="demand_item")
 * @ORM\HasLifecycleCallbacks
 */
class DemandItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * @ORM\Column(length=2000, name="title")
     * @Assert\NotBlank(groups={"required_fields"})
     * @Assert\Length(min=2, groups={"required_fields"})
     */
    protected $title;

    /**
     * @ORM\Column(type="decimal", name="volume", scale=2, nullable=true)
     */
    protected $volume;

    /**
     * @ORM\Column(length=255, name="size", nullable=true)
     */
    protected $size;

    /**
     * @ORM\Column(type="integer", name="volume_type", nullable=true)
     * @Assert\NotBlank(
     *     message="Выберите единицы измерения либо поставьте прочерк",
     *     groups={"required_fields"}
     * )
     */
    protected $volumeTypeId;

    /**
     * @var ProductMeasure
     */
    protected $volumeType;

    /**
     * @ORM\ManyToOne(targetEntity="AbstractDemand", inversedBy="demandItems")
     * @ORM\JoinColumn(name="demand_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     */
    protected $demand;

    /**
     * @ORM\ManyToOne(targetEntity="Metal\CategoriesBundle\Entity\Category")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="Message_ID", nullable=true, onDelete="SET NULL")
     *
     * @var Category
     */
    protected $category;

    /**
     * @ORM\OneToMany(targetEntity="DemandItemAttributeValue", mappedBy="demandItem", cascade={"persist"}, orphanRemoval=true)
     * @ORM\OrderBy({"id" = "ASC"}))
     *
     *
     * @var ArrayCollection|DemandItemAttributeValue[]
     */
    protected $demandItemAttributeValues;

    /**
     * @ORM\Column(type="boolean", name="is_locked_attribute_values", nullable=false, options={"default":0})
     */
    protected $isLockedAttributeValues;

    public function __construct()
    {
        $this->demandItemAttributeValues = new ArrayCollection();
        $this->isLockedAttributeValues = false;
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

    public function getVolume()
    {
        return $this->volume;
    }

    public function setVolume($volume)
    {
        $volume = str_replace(',', '.', $volume);
        if ($volume === '') {
            $volume = null;
        }
        $this->volume = $volume;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function setSize($size)
    {
        $this->size = $size;
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
     * @return Demand
     */
    public function getDemand()
    {
        return $this->demand;
    }

    public function setDemand($demand)
    {
        $this->demand = $demand;
    }

    /**
     * @param Category $category
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;
    }

    /**
     * @return Category|null
     */
    public function getCategory()
    {
        return $this->category;
    }

    public function getCategoryId()
    {
        return $this->category ? $this->category->getId() : null;
    }

    /**
     * @return ArrayCollection|DemandItemAttributeValue[]
     */
    public function getDemandItemAttributeValues()
    {
        return $this->demandItemAttributeValues;
    }

    /**
     * @param DemandItemAttributeValue $demandItemAttributeValue
     */
    public function addDemandItemAttributeValue(DemandItemAttributeValue $demandItemAttributeValue)
    {
        $demandItemAttributeValue->setDemandItem($this);
        $this->demandItemAttributeValues->add($demandItemAttributeValue);
    }

    /**
     * @param DemandItemAttributeValue $demandItemAttributeValue
     */
    public function removeDemandItemAttributeValue(DemandItemAttributeValue $demandItemAttributeValue)
    {
        $this->demandItemAttributeValues->removeElement($demandItemAttributeValue);
    }

    /**
     * @return AttributeValue[]
     */
    public function getAttributeValues()
    {
        $attributeValues = array();
        foreach ($this->demandItemAttributeValues as $demandItemAttributeValue) {
            $attributeValues[] = $demandItemAttributeValue->getAttributeValue();
        }

        return $attributeValues;
    }

    public function addAttributeValue(AttributeValue $attributeValue)
    {
       /* foreach ($this->getDemandItemAttributeValues() as $demandItemAttributeValue) {
            if ($demandItemAttributeValue->getAttributeValue()->getAttribute() === $attributeValue->getAttribute()) {
                $demandItemAttributeValue->setAttributeValue($attributeValue);
                return;
            }
        }*/

        $demandItemAttributeValue = new DemandItemAttributeValue();
        $demandItemAttributeValue->setAttributeValue($attributeValue);
        $this->addDemandItemAttributeValue($demandItemAttributeValue);
    }

    public function removeAttributeValue(AttributeValue $attributeValue)
    {
        foreach ($this->getDemandItemAttributeValues() as $demandItemAttributeValue) {
            if ($demandItemAttributeValue->getAttributeValue() === $attributeValue) {
                $this->demandItemAttributeValues->removeElement($demandItemAttributeValue);
                break;
            }
        }
    }

    public function getIsLockedAttributeValues()
    {
        return $this->isLockedAttributeValues;
    }

    public function setIsLockedAttributeValues($isLockedAttributeValues)
    {
        $this->isLockedAttributeValues = $isLockedAttributeValues;
    }

    public function isEmpty()
    {
        return !$this->title && !$this->volume;
    }

    public function getCategoryTitle()
    {
        return $this->category ? $this->category->getTitle() : '';
    }

    public function setCategoryTitle($categoryTitle)
    {
        // dummy
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

    /**
     * @Assert\Callback(groups={"anonymous_prokat", "authenticated_prokat", "anonymous", "admin_panel"})
     */
    public function validate(ExecutionContextInterface $context)
    {
        // если не выбран файл и это первая позиция или же это не пустая позиция
        $demand = $this->getDemand();

        $isFirstItem = $demand->getDemandItems()->indexOf($this) === 0;
        $hasFiles = false;
        foreach ($demand->getDemandFiles() as $demandFile) {
            if (null !== $demandFile->getUploadedFile()) {
                $hasFiles = true;
            }
        }

        if (!$hasFiles && ($isFirstItem || !$this->isEmpty())) {
            $context
                ->getValidator()
                ->inContext($context)
                ->atPath('')
                ->validate($this, null, array('required_fields'));
        }

        if (!$this->isEmpty()) {
            if ($this->volumeType && $this->volumeType->getId() != ProductMeasureProvider::WITHOUT_VOLUME) {
                $constraints = array(
                    new Assert\NotBlank(),
                    new Assert\Type('numeric'),
                );

                $context
                    ->getValidator()
                    ->inContext($context)
                    ->atPath('volume')
                    ->validate($this->volume, $constraints, array(Constraint::DEFAULT_GROUP));
            }
        }

        // каждая не пустая позиция промодерированной заявки должна быть присоединена к категории
        if (!$this->isEmpty() && $context->getGroup() === 'admin_panel' && $demand->isModerated()) {
            $constraints = array(
                new Assert\NotBlank(array('message' => 'Нужно выбрать категорию из списка')),
            );

            $context
                ->getValidator()
                ->inContext($context)
                ->atPath('category')
                ->validate($this->category, $constraints, array(Constraint::DEFAULT_GROUP));
        }
    }
}
