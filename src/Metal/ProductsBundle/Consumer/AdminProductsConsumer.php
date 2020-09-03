<?php

namespace Metal\ProductsBundle\Consumer;

use Brouzie\Components\Indexer\Indexer;
use Doctrine\ORM\EntityManager;
use Metal\ProductsBundle\ChangeSet\ProductsBatchEditChangeSet;
use Metal\ProductsBundle\ChangeSet\ProductsBatchEditStructure;
use Metal\ProductsBundle\DataFetching\Spec\ProductsFilteringSpec;
use Metal\ProjectBundle\Doctrine\Utils;
use Sonata\NotificationBundle\Consumer\ConsumerEvent;
use Sonata\NotificationBundle\Consumer\ConsumerInterface;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

class AdminProductsConsumer implements ConsumerInterface
{
    private $em;

    private $indexer;

    private $cache;

    public function __construct(EntityManager $em, Indexer $indexer, TagAwareAdapterInterface $cache = null)
    {
        $this->em = $em;
        $this->indexer = $indexer;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function process(ConsumerEvent $event)
    {
        $productsData = $event->getMessage()->getBody();

        if (isset($productsData['changeset'])) {
            $productsChangeSet = $productsData['changeset'];
            /* @var $productsChangeSet ProductsBatchEditChangeSet */

            if ($productsChangeSet->productsToDisable) {
                $this->processProductsStatusChanging($productsChangeSet->productsToDisable, false);
            }

            if ($productsChangeSet->productsToEnable) {
                $this->processProductsStatusChanging($productsChangeSet->productsToEnable, true);
                $this->em->getRepository('MetalProductsBundle:Product')->updateNormalizedPrice(
                    $productsChangeSet->productsToEnable
                );
            }

            if ($productsChangeSet->productsToChangeCategory) {
                $this->processProductCategoryChanging($productsChangeSet->productsToChangeCategory);
            }
        }

        if (!empty($productsData['reindex']['products_reindex_ids'])) {
            $this->indexer->reindexIds($productsData['reindex']['products_reindex_ids']);
        }
    }

    protected function processProductsStatusChanging(array $productsIds, $enable)
    {
        Utils::checkEmConnection($this->em);

        $productRepo = $this->em->getRepository('MetalProductsBundle:Product');
        $structure = $productRepo->initializeProductsStructure($productsIds);

        // 1. привязываем атрибуты к тем товарам, которые только включились
        if ($enable) {
            $this->em->getRepository('MetalProductsBundle:ProductParameterValue')
                ->onProductStatusChanging($structure, $enable);
        }

        // 2.1 Выставляем hasProducts у BranchOffices
        $this->em->getRepository('MetalCompaniesBundle:CompanyCity')
            ->updateCompanyCityHasProducts($productsIds, $enable);

        // 2.2 реиндексируем в сфинксе
        if ($enable) {
            $this->indexer->reindexIds($productsIds);
        } else {
            $this->indexer->delete($productsIds);
        }

        Utils::checkEmConnection($this->em);

        // 3. обновляем счетчики компаний
        // 3.1. общее кол-во продуктов в компании
        $this->em->getRepository('MetalCompaniesBundle:CompanyCounter')->onProductStatusChanging($structure, $enable);

        // 4. указываем, что компания представлена в данной категории и удаляем те категории, где она больше не представлена
        $this->em->getRepository('MetalCompaniesBundle:CompanyCategory')->onProductStatusChanging($structure);

        // 5. обновляем товары с одинаковым тайтлом
        $productRepo->onProductStatusChanging($structure);

        $this->calculateCanCompanyHasCustomCategory($structure);
        $this->invalidateCache($structure);
    }

    protected function processProductCategoryChanging(array $productsToCategories)
    {
        Utils::checkEmConnection($this->em);

        $productRepo = $this->em->getRepository('MetalProductsBundle:Product');
        $structure = $productRepo->initializeProductsStructure(array_keys($productsToCategories));

        // 1. перепривязываем атрибуты товаров, у которых была изменена категория +
        $this->em->getRepository('MetalProductsBundle:ProductParameterValue')
            ->onProductStatusChanging($structure, true);

        // 2. реиндексируем в сфинксе +
        $this->indexer->reindexIds(array_keys($productsToCategories));

        Utils::checkEmConnection($this->em);

        // 3. указываем, что компания представлена в данной категории и удаляем те категории, где она больше не представлена +
        $this->em->getRepository('MetalCompaniesBundle:CompanyCategory')
            ->onProductStatusChanging($structure);

        $this->invalidateCache($structure);
    }

    protected function calculateCanCompanyHasCustomCategory(ProductsBatchEditStructure $structure)
    {
        $customCompanyCategoryRepository = $this->em->getRepository('MetalCompaniesBundle:CustomCompanyCategory');
        foreach ($structure->companies as $companyId => $citiesIds) {
            $customCompanyCategoryRepository->calculateCanCompanyHasCustomCategory($companyId);
        }
    }

    protected function invalidateCache(ProductsBatchEditStructure $structure)
    {
        if (!$this->cache) {
            return;
        }

        $tags = array();
        foreach ($structure->companies as $companyId => $citiesIds) {
            $tags[] = sprintf(ProductsFilteringSpec::COMPANY_TAG_PATTERN, $companyId);
        }

        if ($tags) {
            $this->cache->invalidateTags($tags);
        }
    }
}
