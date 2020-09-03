<?php

namespace Metal\ProjectBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\AttributesBundle\Entity\DTO\AttributesCollection;
use Metal\CategoriesBundle\Entity\Category;
use Metal\ProjectBundle\Entity\SeoTemplate;

class SeoTemplateRepository extends EntityRepository
{
    /**
     * @param Category $category
     * @param AttributesCollection $attributesCollection
     *
     * @return SeoTemplate|null
     */
    public function findBestSeoTemplate(Category $category, AttributesCollection $attributesCollection)
    {
        $seoTemplates = $this
            ->createQueryBuilder('st')
            ->andWhere('st.category = :category')
            ->setParameter('category', $category)
            ->orderBy('st.priority', 'DESC')
            ->getQuery()
            ->getResult();

        /** @var SeoTemplate[] $seoTemplates */
        foreach ($seoTemplates as $seoTemplate) {
            if ($seoTemplate->matches($category, $attributesCollection)) {
                return $seoTemplate;
            }
        }

        return null;
    }
}
