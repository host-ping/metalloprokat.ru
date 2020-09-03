<?php

namespace Metal\AttributesBundle\Repository;

use Behat\Transliterator\Transliterator;
use Cocur\Slugify\Slugify;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Metal\AttributesBundle\DataFetching\AttributesFacetResult;
use Metal\AttributesBundle\Entity\Attribute;
use Metal\AttributesBundle\Entity\AttributeValue;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\AttributesBundle\Entity\DTO\LightAttributeValue;
use Metal\CategoriesBundle\Entity\Category;

class AttributeValueRepository extends EntityRepository
{
    private $cache = [];

    public function refreshPriorityOrders()
    {
        $conn = $this->_em->getConnection();

        $attributes = $conn->fetchAll('SELECT id FROM attribute');

        foreach ($attributes as $attribute) {
            $attributeValues = $conn->fetchAll(
                'SELECT id, value FROM attribute_value WHERE attribute_id = :attribute_id',
                array(
                    'attribute_id' => $attribute['id']
                )
            );

            usort(
                $attributeValues,
                function ($b, $a) {
                    return strnatcmp(mb_strtolower($a['value']), mb_strtolower($b['value'])) * -1;
                }
            );

            foreach ($attributeValues as $key => $attributeValue) {
                $conn->executeUpdate(
                    'UPDATE attribute_value SET output_priority = :priority WHERE id = :id',
                    array(
                        'priority' => $key,
                        'id' => $attributeValue['id']
                    )
                );
            }
        }
    }

    public function refreshUrlPriorityOrder()
    {
        $conn = $this->_em->getConnection();

        $attributeValues = $conn->fetchAll(
            'SELECT id, value FROM attribute_value ORDER BY value ASC'
        );

        usort(
            $attributeValues,
            function ($b, $a) {
                return strnatcmp(mb_strtolower($a['value']), mb_strtolower($b['value'])) * -1;
            }
        );

        foreach ($attributeValues as $key => $attributeValue) {
            $conn->executeUpdate(
                'UPDATE attribute_value SET url_priority = :priority WHERE id = :id',
                array(
                    'priority' => $key,
                    'id' => $attributeValue['id']
                )
            );
        }
    }

    public function createQueryBuilderForOptionsList($category, $attrCode)
    {
        return $this
            ->createQueryBuilder('av')
            ->join('MetalAttributesBundle:AttributeValueCategory', 'avc', 'WITH', 'avc.attributeValue = av.id')
            ->join('av.attribute', 'a')
            ->andWhere('avc.category = :category')
            ->setParameter('category', $category)
            ->andWhere('a.code = :code')
            ->setParameter('code', $attrCode)
            ->orderBy('av.value', 'ASC');
    }

    /**
     * @param Attribute $attribute
     * @param $value
     *
     * @return AttributeValue
     */
    public function findOrCreateAttributeValue(Attribute $attribute, $value, Slugify $slugify)
    {
        $attributeValue = $this->findOneBy(array('attribute' => $attribute, 'value' => $value));
        if ($attributeValue) {
            return $attributeValue;
        }

        $slug = $this->generateUniqueSlug($value, $attribute->getCode(), $slugify);
        $attributeValue = new AttributeValue();
        $attributeValue->setAttribute($attribute);
        $attributeValue->setSlug($slug);
        $attributeValue->setValue($value);

        $this->_em->persist($attributeValue);

        return $attributeValue;
    }

    public function generateUniqueSlug($value, $code, Slugify $slugify)
    {
        $slug = $slugify->slugify($value);
        $slug = Transliterator::urlize($slug);
        $isDoubleSlug = $this->findOneBy(array('slug' => $slug));
        if ($isDoubleSlug) {
            $slug = $code.'-'.$slug;
        }

        return $slug;
    }

    /**
     * @param array|null $attrsByGroup An array of (Attribute.id => AttributeValue.id[]) structure
     *
     * @return AttributesCollection
     */
    public function loadCollectionByGroups($attrsByGroup)
    {
        //TODO: объединить с loadCollectionByFacetResult, нужно создать какой-то класс, который будет реализовывать интерфейс с методом eachAttributeValuesIds
        $attributesValuesIds = array();
        foreach ((array)$attrsByGroup as $attrsInGroup) {
            $attributesValuesIds = array_merge($attributesValuesIds, $attrsInGroup);
        }

        $attributesCollection = new AttributesCollection();
        if (!$attributesValuesIds) {
            return $attributesCollection;
        }

        $attributeValues = $this
            ->createQueryBuilder('av')
            ->select(LightAttributeValue::getCreateDQL())
            ->join('av.attribute', 'a')
            ->where('av.id IN (:ids)')
            ->setParameter('ids', $attributesValuesIds)
            ->addOrderBy('a.urlPriority')
            ->addOrderBy('av.urlPriority')
            ->getQuery()
            ->getResult();

        /* @var $attributeValues LightAttributeValue[] */

        $this->attachAttributesToLightAttributeValues($attributeValues);
        $attributesCollection->appendAttributeValues($attributeValues);

        return $attributesCollection;
    }

    public function loadCollectionByAttributesValuesIds(array $attributeValuesIds, array $orders = []): AttributesCollection
    {
        if (!$attributeValuesIds) {
            return new AttributesCollection();
        }

        $availableOrders = array(
            Attribute::ORDER_OUTPUT_PRIORITY,
            AttributeValue::ORDER_OUTPUT_PRIORITY,
        );

        if ($diff = array_diff($orders, $availableOrders)) {
            throw new \InvalidArgumentException(sprintf('Invalid orders given: "%s".', implode(',', $diff)));
        }

        $qb = $this
            ->createQueryBuilder('av')
            ->select(LightAttributeValue::getCreateDQL())
            ->where('av.id IN(:ids)')
            ->setParameter('ids', $attributeValuesIds);

        foreach ($orders as $order) {
            if (Attribute::ORDER_OUTPUT_PRIORITY === $order) {
                $qb->join('av.attribute', 'a');
            }

            $qb->addOrderBy($order);
        }

        $attributeValues = $qb
            ->getQuery()
            ->getResult();

        /* @var $attributeValues LightAttributeValue[] */

        $this->attachAttributesToLightAttributeValues($attributeValues);

        $attributesCollection = new AttributesCollection();
        if ($orders) {
            $attributesCollection->appendAttributeValues($attributeValues);
        } else {
            $orderedAttributeValues = array();
            foreach ($attributeValues as $attributeValue) {
                $orderedAttributeValues[$attributeValue->getId()] = $attributeValue;
            }

            // preserve original order from facet
            foreach ($attributeValuesIds as $attributeValueId) {
                if (isset($orderedAttributeValues[$attributeValueId])) {
                    $attributesCollection->appendAttributeValue($orderedAttributeValues[$attributeValueId]);
                }
            }
        }

        return $attributesCollection;
    }

    public function loadCollectionByFacetResult(AttributesFacetResult $attributesFacetResult, array $orders = array())
    {
        $attributesValuesIds = array();
        foreach ($attributesFacetResult->eachAttributeValuesIds() as $attributeValueId) {
            $attributesValuesIds[] = $attributeValueId;
        }

        return $this->loadCollectionByAttributesValuesIds($attributesValuesIds, $orders);
    }

    /**
     * @param LightAttributeValue[] $attributeValues
     */
    public function attachAttributesToLightAttributeValues($attributeValues)
    {
        $attributesIdsToLoad = array();
        foreach ($attributeValues as $attributeValue) {
            $attributesIdsToLoad[$attributeValue->attributeId] = true;
        }

        if ($attributesIdsToLoad) {
            $this->_em->getRepository('MetalAttributesBundle:Attribute')
                ->findBy(array('id' => array_keys($attributesIdsToLoad)));
        }

        foreach ($attributeValues as $attributeValue) {
            $attributeValue->setAttribute($this->_em->getReference('MetalAttributesBundle:Attribute', $attributeValue->attributeId));
        }
    }

    public function loadCollectionBySlugs(Category $category, $slugs, $field = 'slug')
    {
        $attributesCollection = new AttributesCollection();

        if (is_string($slugs)) {
            $slugs = preg_split('#[/_]#', $slugs);
        }

        if (!$slugs) {
            return $attributesCollection;
        }

        $attributeValues = $this
            ->_em
            ->createQueryBuilder()
            ->select('av')
            ->from('MetalAttributesBundle:AttributeValue', 'av', 'av.id')
            ->join('av.attribute', 'a')
            ->join('MetalAttributesBundle:AttributeValueCategory', 'avc', 'WITH', 'avc.attributeValue = av.id AND avc.category = :category')
            ->addSelect('a')
            ->andWhere(sprintf('av.%s IN (:parts)', $field))
            ->addOrderBy('a.urlPriority')
            ->addOrderBy('av.urlPriority')
            ->setParameter('category', $category->getRealCategoryId())
            // явно указываем, что нужно биндить как строку, иначе для числовых слагов, наподобие "12" будут неправильно находиться соответствия
            ->setParameter('parts', $slugs, Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult();

        $attributesCollection->appendAttributeValues($attributeValues);

        return $attributesCollection;
    }

    public function loadCollectionBySlugsOnly($slugs, $field = 'slug')
    {
        $attributesCollection = new AttributesCollection();

        if (is_string($slugs)) {
            $slugs = preg_split('#[/_]#', $slugs);
        }

        if (!$slugs) {
            return $attributesCollection;
        }

        $attributeValues = $this
            ->_em
            ->createQueryBuilder()
            ->select('av')
            ->from('MetalAttributesBundle:AttributeValue', 'av', 'av.id')
            ->join('av.attribute', 'a')
            ->addSelect('a')
            ->andWhere(sprintf('av.%s IN (:parts)', $field))
            ->addOrderBy('a.urlPriority')
            ->addOrderBy('av.urlPriority')
            // явно указываем, что нужно биндить как строку, иначе для числовых слагов, наподобие "12" будут неправильно находиться соответствия
            ->setParameter('parts', $slugs, Connection::PARAM_STR_ARRAY)
            ->getQuery()
            ->getResult();

        $attributesCollection->appendAttributeValues($attributeValues);

        return $attributesCollection;
    }


    /**
     * @param $attributesValues
     *
     * @return Attribute[]
     */
    public function getAttributesForAttributesValues($attributesValues)
    {
        if (!$attributesValues) {
            return array();
        }

        $attributes = $this
            ->_em
            ->createQueryBuilder()
            ->select('a')
            ->from('MetalAttributesBundle:Attribute', 'a')
            ->join('MetalAttributesBundle:AttributeValue', 'at', 'WITH', 'at.attribute = a.id')
            ->andWhere('at.id IN (:attributesValue)')
            ->setParameter('attributesValue', $attributesValues)
            ->groupBy('a.id')
            ->getQuery()
            ->getResult();

        return $attributes;
    }

    public function getAttributesCollectionByCategory($category)
    {
        $categoryId = $category instanceof Category ? $category->getRealCategoryId() : $category;

        if (isset($this->cache[$categoryId])) {
            return $this->cache[$categoryId];
        }

        $attributeValues = $this
            ->createQueryBuilder('av')
            ->join('MetalAttributesBundle:AttributeValueCategory', 'avc', 'WITH', 'avc.attributeValue = av.id AND avc.category = :category')
            ->setParameter('category', $categoryId)
//            ->orderBy('a.outputPriority')
            ->getQuery()
            ->getResult();

        $attributesCollection = new AttributesCollection();
        $attributesCollection->appendAttributeValues($attributeValues);

        return $this->cache[$categoryId] = $attributesCollection;
    }
}
