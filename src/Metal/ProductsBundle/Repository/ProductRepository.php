<?php

namespace Metal\ProductsBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\QueryBuilder;
use Metal\CategoriesBundle\Entity\Category;
use Metal\CategoriesBundle\Entity\CategoryAbstract;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CustomCompanyCategory;
use Metal\ProductsBundle\ChangeSet\ProductsBatchEditStructure;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\DataFetching\Sphinxy\FacetResultExtractor;
use Metal\ProjectBundle\Doctrine\EntityRepository;
use Metal\ProjectBundle\Doctrine\Utils;
use Metal\UsersBundle\Entity\User;

class ProductRepository extends EntityRepository
{
    /**
     * @param string $itemHash
     *
     * @return Product|null
     */
    public function loadProductForEditing($itemHash)
    {
        $priorities = array(
            Product::STATUS_CHECKED => 0,
            Product::STATUS_NOT_CHECKED => 1,
            Product::STATUS_LIMIT_EXCEEDING => 2,
            Product::STATUS_PENDING_CATEGORY_DETECTION => 3,
            Product::STATUS_PROCESSING => 4,
            Product::STATUS_DELETED => 5,
        );

        $productsCollection = $this->findBy(array('itemHash' => $itemHash));

        usort($productsCollection, function (Product $a, Product $b) use ($priorities) {
            $aPriority = $priorities[$a->getChecked()];
            $bPriority = $priorities[$b->getChecked()];

            if ($aPriority === $bPriority) {
                return 0;
            }

            return ($aPriority < $bPriority) ? -1 : 1;
        });

        return reset($productsCollection);
    }

    public function getProductsQbBySpecification($specification, $orderBy = array())
    {
        $qb = $this->_em->createQueryBuilder()
            ->select('p')
            ->from($this->_entityName, 'p', !empty($specification['index_by_id']) ? 'p.id' : null);

        $this->applySpecificationToQueryBuilder($qb, $specification);
        $this->applyOrderByToQueryBuilder($qb, $orderBy);

        return $qb;
    }

    private function applyOrderByToQueryBuilder(QueryBuilder $qb, array $orderBy)
    {

    }

    public function createOrUpdateVirtualProduct(Company $company)
    {
        $mainOffice = $this->_em->getRepository('MetalCompaniesBundle:CompanyCity')
            ->findOneBy(array('company' => $company, 'isMainOffice' => true));

        if (!$mainOffice) {
            throw new \LogicException('Main office should be set before calling this method.');
        }

        $virtualProduct = $this->findOneBy(array('company' => $company, 'isVirtual' => true));

        if (!$virtualProduct) {
            $virtualProduct = Product::createVirtualProduct($company);
            $this->_em->persist($virtualProduct);
        }

        $virtualProduct->setBranchOffice($mainOffice);
        $company->setVirtualProduct($virtualProduct);

        $this->_em->flush();
    }

    /*
     * N < 0 - нужно отключить N'е количество продуктов
     * N > 0 - включить N'е количество продуктов
     * N == 0 - ничего не делаем
     */
    public function getAvailableAddProductsCountToCompany(Company $company)
    {
        $maxAvailableProductsCount = $company->getPackageChecker()->getMaxAvailableProductsCount();

        if (null === $maxAvailableProductsCount || null === $company->getPackageChecker()->getMaxAvailableProductsCountMinisite()) {
            return null;
        }

        $addedProductsCount = $this->_em->getRepository('MetalProductsBundle:Product')
            ->createQueryBuilder('product')
            ->select('COUNT(product)')
            ->andWhere('product.company = :company')
            ->andWhere('product.isVirtual = false')
            ->andWhere('product.checked NOT IN (:statuses)')
            ->setParameter('statuses', array(Product::STATUS_DELETED, Product::STATUS_LIMIT_EXCEEDING))
            ->setParameter('company', $company)
            ->getQuery()
            ->getSingleScalarResult();

        return $maxAvailableProductsCount - $addedProductsCount;
    }

    public function attachUsersToProducts(array $products = array())
    {
        if (!$products) {
            return array();
        }

        $this->_em->createQueryBuilder()
            ->select('createdBy')
            ->addSelect('updatedBy')
            ->addSelect('productLog')
            ->from('MetalProductsBundle:ProductLog', 'productLog')
            ->leftJoin('productLog.createdBy', 'createdBy')
            ->leftJoin('productLog.updatedBy', 'updatedBy')
            ->where('productLog.product IN(:products)')
            ->setParameter('products', $products)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @param Product[] $products
     * @param $user
     */
    public function attachFavoriteToProducts(array $products, $user)
    {
        if (!count($products)) {
            return;
        }

        $directedProducts = array();
        foreach ($products as $product) {
            $directedProducts[$product->getId()] = $product;
            $product->setAttribute('isInFavorite', false);
        }

        if (!$user) {
            return;
        }

        $qb = $this->_em->createQueryBuilder()
            ->from('MetalUsersBundle:Favorite', 'f')
            ->select('IDENTITY(f.product) AS productId')
            ->andWhere('f.user = :user')
            ->andWhere('f.product IN (:products_ids)')
            ->setParameter('user', $user)
            ->setParameter('products_ids', array_keys($directedProducts));

        $favoriteProducts = $qb
            ->getQuery()
            ->getResult();

        foreach ($favoriteProducts as $favoriteProduct) {
            $directedProducts[$favoriteProduct['productId']]
                ->setAttribute('isInFavorite', true);
        }
    }

    private function applySpecificationToQueryBuilder(QueryBuilder $qb, array $specification)
    {
        if (isset($specification['id'])) {
            $qb->andWhere('p.id IN (:id)')
                ->setParameter('id', $specification['id']);
        }

        if (isset($specification['preload_image'])) {
            $qb->leftJoin('p.image', 'img')
                ->addSelect('img');
        }

        if (!isset($specification['enabled_only']) || $specification['enabled_only']) {
            $qb->andWhere('p.checked = :status_checked')
                ->setParameter('status_checked', Product::STATUS_CHECKED);
        }

        if (isset($specification['company'])) {
            $qb
                ->andWhere('p.company = :company')
                ->setParameter('company', $specification['company']);
        }

        if (isset($specification['category']) && $specification['category']) {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', $specification['category']);
        }

        if (!empty($specification['preload_company'])) {
            $qb
                ->join('p.company', 'com')
                ->join('com.counter', 'counter')
                ->addSelect('com')
                ->addSelect('counter')
                ->join('com.city', 'c')
                ->addSelect('c');
        }

        if (isset($specification['preload_branch_office'])) {
            $qb->leftJoin('p.branchOffice', 'bo')
                ->addSelect('bo');
        }

        if (!empty($specification['preload_administrative_center'])) {
            $qb
                ->leftJoin('c.administrativeCenter', 'adc')
                ->addSelect('adc');
        }
    }

    public function onProductStatusChanging(ProductsBatchEditStructure $structure)
    {
        $companiesIds = array();
        foreach ($structure->products as $product) {
            $companiesIds[$product['companyId']] = true;
        }

        $this->updateDuplicatedTitles(array_keys($companiesIds));
    }

    public function updateDuplicatedTitles(array $companiesIds = array())
    {
        if (!count($companiesIds)) {
            return;
        }

        $connection = $this->_em->getConnection();

        $connection->executeUpdate('
            UPDATE Message142 p SET p.Memo2Title = 1 WHERE p.Company_ID IN (:companies) AND p.Checked != :status_deleted',
            array(
                'companies' => $companiesIds,
                'status_deleted' => Product::STATUS_DELETED
            ),
            array(
                'companies' => Connection::PARAM_INT_ARRAY
            )
        );

        $productsIds = $connection->executeQuery('
            SELECT p.Message_ID
            FROM Message142 p
            WHERE p.Company_ID IN (:companies) AND p.Checked != :status_deleted
            GROUP BY p.Company_ID, p.Name',
            array(
                'companies' => $companiesIds,
                'status_deleted' => Product::STATUS_DELETED
            ),
            array(
                'companies' => Connection::PARAM_INT_ARRAY
            )
        )->fetchAll();

        $productsIds = array_column($productsIds, 'Message_ID');

        $connection->executeUpdate('
            UPDATE Message142 product
            SET product.Memo2Title = 0
            WHERE product.Message_ID IN (:productsIds)',
            array(
                'productsIds' => $productsIds,
            ),
            array(
                'productsIds' => Connection::PARAM_INT_ARRAY)
        );

    }

    /**
     * @param Company[] $companies
     */
    public function attachProductsCountToCompanies(array $companies)
    {
        if (!count($companies)) {
            return;
        }

        $directedCompanies = array();

        foreach ($companies as $company) {
            $directedCompanies[$company->getId()] = $company;
            $company->setAttribute('productCount', 0);
        }

        $qb = $this->_em->createQueryBuilder()
            ->from('MetalProductsBundle:Product', 'p')
            ->select('IDENTITY(p.company) AS companyId, COUNT(p.id) AS productsCount')
            ->join('p.branchOffice', 'office', 'WITH', 'office.isMainOffice = true')
            ->andWhere('p.company IN (:companies_ids)')
            ->andWhere('p.isVirtual = 0')
            ->andWhere('p.checked = :status')
            ->groupBy('p.company')
            ->setParameter('companies_ids', array_keys($directedCompanies), Connection::PARAM_INT_ARRAY)
            ->setParameter('status', Product::STATUS_CHECKED);

        $countersProductCompany = $qb
            ->getQuery()
            ->getArrayResult();

        foreach ($countersProductCompany as $countProductCompany) {
            $directedCompanies[$countProductCompany['companyId']]
                ->setAttribute('productsCount', $countProductCompany['productsCount']);
        }
    }

    /**
     * @param Product[] $products
     */
    public function attachCategoriesToProducts(array $products)
    {
        $categoriesIds = array();
        foreach ($products as $product) {
            if ($product->getCategory()) {
                $categoriesIds[$product->getCategory()->getId()] = true;
            }
        }

        if (!$categoriesIds) {
            return;
        }

        $this->_em->getRepository('MetalCategoriesBundle:Category')->findBy(array('id' => array_keys($categoriesIds)));
    }

    /**
     * @param array $productsIds
     * @param array $dataForCategories
     *
     * @return ProductsBatchEditStructure
     */
    public function initializeProductsStructure(array $productsIds, array $dataForCategories = null)
    {
        $products = $this->_em->createQueryBuilder()
            ->select('p.id, IDENTITY(p.category) AS categoryId, IDENTITY(p.company) as companyId, p.title, p.size')
            ->from('MetalProductsBundle:Product', 'p')
            ->where('p.id IN (:products)')
            ->setParameter('products', $productsIds)
            ->getQuery()
            ->getResult();

        if (null !== $dataForCategories) {
            foreach ($products as $i => $product) {
                // подменяем текущую категорию у продукта на прошлую
                $products[$i]['categoryId'] = $dataForCategories[$product['id']]['old'];
            }
        }

        $structure = new ProductsBatchEditStructure();

        $usedCompanies = array();
        $usedCategories = array();
        foreach ($products as $product) {
            $structure->products[$product['id']] = $product;
            $usedCategories[$product['categoryId']] = true;
            $usedCompanies[$product['companyId']] = true;
        }

        Utils::checkEmConnection($this->_em);

        $cities = $this->_em->createQueryBuilder()
            ->select('IDENTITY(cc.company) AS companyId, IDENTITY(cc.city) AS cityId')
            ->addSelect('c.slug AS citySlug, IDENTITY(c.administrativeCenter) as alternativeCityId')
            ->addSelect('IDENTITY(c.country) as countryId')
            ->from('MetalCompaniesBundle:CompanyCity', 'cc')
            ->join('cc.city', 'c')
            ->where('cc.company IN (:companies)')
            ->setParameter('companies', array_keys($usedCompanies))
            ->getQuery()
            ->getResult();

        foreach ($cities as $city) {
            $structure->companies[$city['companyId']]['cities'][] = $city['citySlug'] ? $city['cityId'] : $city['alternativeCityId'];
            $structure->cities[$city['cityId']]['countryId'] = $city['countryId'];
        }

        $categories = $this->_em->createQueryBuilder()
            ->select('c.id, c.branchIds')
            ->from('MetalCategoriesBundle:Category', 'c')
            ->where('c.id IN (:categories)')
            ->setParameter('categories', array_keys($usedCategories))
            ->getQuery()
            ->getResult();

        foreach ($categories as $category) {
            $structure->categories[$category['id']]['branchIds'] = array_filter(explode(',', $category['branchIds']));
        }

        return $structure;
    }

    public function getEnabledDuplicateForProduct(Product $product)
    {
        return $this->findOneBy(array('checked' => Product::STATUS_CHECKED, 'itemHash' => $product->getItemHash()));
    }

    /**
     * @param array $companiesIds
     * @return array
     */
    public function disableDuplicatedProductsForCompanies(array $companiesIds)
    {
        if (!$companiesIds) {
            return array();
        }

        $statuses = array(
            Product::STATUS_CHECKED,
            Product::STATUS_NOT_CHECKED
        );

        $disabledProductsIds = array();
        foreach ($companiesIds as $companyId) {
            $duplicatesHashes = $this->_em->createQueryBuilder()
                ->select('product.itemHash')
                ->from('MetalProductsBundle:Product', 'product', 'product.itemHash')
                ->where('product.company = :company')
                ->andWhere('product.checked IN (:statuses)')
                ->groupBy('product.itemHash')
                ->having('COUNT(product) > 1')
                ->setParameter('company', $companyId)
                ->setParameter('statuses', $statuses)
                ->getQuery()
                ->getArrayResult();

            $ids = $this->disableDuplicatedProductsByProductHashes(array_keys($duplicatesHashes));
            $disabledProductsIds = array_merge($disabledProductsIds, $ids);
        }

        return $disabledProductsIds;
    }

    public function disableDuplicatedProductsByProductHashes(array $hashes)
    {
        if (!$hashes) {
            return array();
        }

        $statuses = array(
            Product::STATUS_CHECKED,
            Product::STATUS_NOT_CHECKED
        );

        $productDuplicates = $this->_em->createQueryBuilder()
            ->from('MetalProductsBundle:Product', 'product')
            ->select('product.id')
            ->addSelect('IDENTITY(product.image) AS image')
            ->addSelect('product.price')
            ->addSelect('product.measureId')
            ->addSelect('product.currencyId')
            ->addSelect('product.itemHash')
            ->where('product.itemHash IN (:hashes)')
            ->andWhere('product.checked IN (:statuses)')
            ->setParameter('statuses', $statuses)
            ->setParameter('hashes', $hashes)
            ->orderBy('product.id', 'ASC')
            ->getQuery()
            ->getArrayResult();

        $productsToHash = array();
        foreach ($productDuplicates as $productDuplicate) {
            $productsToHash[$productDuplicate['itemHash']][] = $productDuplicate;
        }

        $disabledProductsIds = array();
        foreach ($productsToHash as $productsDuplicates) {
            if (count($productsDuplicates) < 2) {
                continue;
            }

            $oldProduct = array_shift($productsDuplicates);
            foreach ($productsDuplicates as $productDuplicate) {
                $disabledProductsIds[] = $productDuplicate['id'];
            }

            $productDuplicate = end($productsDuplicates);
            unset($productDuplicate['id'], $productDuplicate['itemHash']);

            $qb = $this->_em
                ->createQueryBuilder()
                ->update('MetalProductsBundle:Product', 'p')
                ->where('p.id = :id')
                ->setParameter('id', $oldProduct['id']);

            foreach ($productDuplicate as $field => $value) {
                $paramName = sprintf(':%s', $field);
                $qb
                    ->set(sprintf('p.%s', $field), $paramName)
                    ->setParameter($paramName, $value);
            }

            $qb->getQuery()->execute();
        }

        $this->_em->createQueryBuilder()
            ->update('MetalProductsBundle:Product', 'p')
            ->set('p.checked', ':status')
            ->setParameter('status', Product::STATUS_DELETED)
            ->where('p.id IN (:ids)')
            ->setParameter('ids', $disabledProductsIds)
            ->getQuery()
            ->execute();

        return $disabledProductsIds;
    }

    public function removeProductsLinksWithBadImage()
    {
        $productsIds = $this->_em->createQueryBuilder()
            ->select('p.id')
            ->from('MetalProductsBundle:Product', 'p', 'p.id')
            ->join('p.image', 'i')
            ->where('i.retriesCount >= 5')
            ->getQuery()
            ->getResult();


        $this->_em->createQueryBuilder()
            ->update('MetalProductsBundle:Product', 'p')
            ->set('p.image', ':null')
            ->setParameter('null', null)
            ->where('p.id IN (:ids)')
            ->setParameter('ids', array_keys($productsIds))
            ->getQuery()
            ->execute();
    }

    /**
     * @param Product[] $products
     * @param $country
     */
    public function attachNormalizedPrice($products = array(), $country = null)
    {
        if (!$products || !$country) {
            return;
        }

        $exchangeRates = $this->_em->createQueryBuilder()
            ->select('er.course, er.currencyId')
            ->from('MetalProjectBundle:ExchangeRate', 'er', 'er.currencyId')
            ->andWhere('er.country = :country')
            ->andWhere('er.isLast = TRUE')
            ->setParameter('country', $country)
            ->getQuery()
            ->getResult();

        foreach ($products as $product) {
            if (isset($exchangeRates[$product->getCurrencyId()])) {
                $productNormalizedPrice = $exchangeRates[$product->getCurrencyId()]['course'] * $product->getPrice();
                $product->setAttribute('normalized_price', round($productNormalizedPrice, 1));
            }
        }
    }

    /**
     * @param Company $company
     */
    public function disableProductsNotBranchOffice(Company $company)
    {
        $this->_em->getConnection()->executeUpdate(
            '
            UPDATE Message142 AS product
            SET product.branch_office_id = null, product.Checked = :checked
            WHERE NOT EXISTS(
                    SELECT * FROM company_delivery_city AS cdc WHERE cdc.company_id = :company_id AND product.branch_office_id = cdc.id
            )
            AND product.Company_ID = :company_id
            ',
            array(
                'company_id' => $company->getId(),
                'checked' => Product::STATUS_DELETED
            )
        );

        $this->_em->getConnection()->executeUpdate(
            'UPDATE Message142 SET Checked = :checkedTo WHERE Checked IN(:checkedFrom) AND Company_ID = :company_id AND branch_office_id IS NULL',
            array(
                'checkedFrom' => array(
                    Product::STATUS_NOT_CHECKED,
                    Product::STATUS_CHECKED
                ),
                'checkedTo' => Product::STATUS_DELETED,
                'company_id' => $company->getId()
            ),
            array(
                'checkedFrom' => Connection::PARAM_INT_ARRAY
            )
        );
    }

    public function updateNormalizedPrice(array $productsIds)
    {
        if (!$productsIds) {
            return;
        }

        $this->_em->getConnection()->executeUpdate('
                UPDATE Message142 p
                JOIN Message75 c ON p.Company_ID = c.Message_ID
                JOIN exchange_rate er ON c.country_id = er.country_id AND p.currency_id = er.currency_id AND er.is_last = true
                SET p.normalized_price = p.Price * er.course
                WHERE p.Message_ID IN (:products_ids)',
            array('products_ids' => $productsIds),
            array('products_ids' => Connection::PARAM_INT_ARRAY)
        );
    }

    public function updatePermissionShowProducts(Company $company)
    {
        $packageChecker = $company->getPackageChecker();

        $maxAvailableProductsCount = $packageChecker->getMaxAvailableProductsCount();
        $maxAvailableProductsCountMinisite = $packageChecker->getMaxAvailableProductsCountMinisite();

        // Если нет ограничений ни для портала ни для минисайта - все товары могут быть показаны
        if (null === $maxAvailableProductsCount && null === $maxAvailableProductsCountMinisite) {
            $this->updateProductsForUnlimitedPackage($company);

            return;
        }

        // Включаем или отключаем часть товаров для добавления в сфинкс в зависимости от пакета
        $this->updateAllowVisibleStatus($company, $this->getAvailableAddProductsCountToCompany($company));

        // Обновляем количество продуктов, которые должны показыватся на портале
        $this->updateAllowShowOnPortal($company, $maxAvailableProductsCount);
    }

    public function restorePreviousProductStatusForCompany(Company $company)
    {
        $this->_em->getConnection()->executeUpdate(
            'UPDATE Message142 AS product SET product.Checked = product.previous_status
                    WHERE product.Company_ID = :company_id
                    AND product.Checked = :status_exceeding
                    AND product.is_virtual = false 
            ',
            array(
                'company_id' => $company->getId(),
                'status_exceeding' => Product::STATUS_LIMIT_EXCEEDING,
            )
        );
    }

    public function updateProductsForUnlimitedPackage(Company $company)
    {
        // проставляем всем товарам статус show_on_portal = true
        $this->_em->getConnection()->executeUpdate(
            'UPDATE Message142 AS product SET product.show_on_portal = true
                    WHERE product.Company_ID = :company_id 
                    AND product.is_virtual = false 
            ',
            array(
                'company_id' => $company->getId(),
            )
        );

        // Product::STATUS_LIMIT_EXCEEDING -> previous_status
        $this->restorePreviousProductStatusForCompany($company);
    }

    private function updateAllowVisibleStatus(Company $company, $limit)
    {
        $connection = $this->_em->getConnection();

        //TODO: переработать этот блок
        $status = null;
        $statuses = array(Product::STATUS_CHECKED, Product::STATUS_PROCESSING, Product::STATUS_NOT_CHECKED, Product::STATUS_PENDING_CATEGORY_DETECTION);
        if ($limit > 0) {
            $status = Product::STATUS_NOT_CHECKED;
            $statuses = array(Product::STATUS_LIMIT_EXCEEDING);
        } elseif ($limit < 0) {
            $limit *= -1;
            $status = Product::STATUS_LIMIT_EXCEEDING;
        }

        if ($limit === null) {
            $this->restorePreviousProductStatusForCompany($company);
        }

        if (null !== $status) {
            $connection->executeUpdate(
                'UPDATE Message142 AS product 
                    SET 
                      product.previous_status = product.Checked,
                      product.Checked = :new_status
                    WHERE product.Company_ID = :company_id 
                    AND product.Checked IN (:statuses) 
                    AND product.is_virtual = false LIMIT :limit',
                array(
                    'company_id' => $company->getId(),
                    'statuses' => $statuses,
                    'new_status' => $status,
                    'limit' => $limit
                ),
                array(
                    'limit' => \PDO::PARAM_INT,
                    'statuses' => Connection::PARAM_INT_ARRAY
                )
            );
        }
    }

    private function updateAllowShowOnPortal(Company $company, $maxAvailableProductsCount)
    {
        $this->_em->getConnection()->executeUpdate(
            'UPDATE Message142 AS product SET product.show_on_portal = false
                    WHERE product.Company_ID = :company_id 
                    AND product.Checked IN (:statuses) 
                    AND product.is_virtual = false',
            array(
                'company_id' => $company->getId(),
                'statuses' => array(Product::STATUS_CHECKED, Product::STATUS_PROCESSING, Product::STATUS_NOT_CHECKED, Product::STATUS_PENDING_CATEGORY_DETECTION),
            ),
            array(
                'statuses' => Connection::PARAM_INT_ARRAY,
                'company_id' => \PDO::PARAM_INT,
            )
        );

        $this->_em->getConnection()->executeUpdate(
            'UPDATE Message142 AS product SET product.show_on_portal = true
                    WHERE product.Company_ID = :company_id 
                    AND product.Checked IN (:statuses) 
                    AND product.is_virtual = false 
                    LIMIT :limit',
            array(
                'company_id' => $company->getId(),
                'limit' => $maxAvailableProductsCount,
                'statuses' => array(Product::STATUS_CHECKED, Product::STATUS_PROCESSING, Product::STATUS_NOT_CHECKED, Product::STATUS_PENDING_CATEGORY_DETECTION),
            ),
            array(
                'statuses' => Connection::PARAM_INT_ARRAY,
                'limit' => \PDO::PARAM_INT,
                'company_id' => \PDO::PARAM_INT,
            )
        );
    }

    public function resetProductsAutoAssociationWithPhotos()
    {
        // Если сняли чекбокс автоассоциации с общими фото, то мы таким продуктам ставим p.Image_ID = NULL
        $this->_em->getConnection()->executeUpdate('UPDATE Message142 AS p
            JOIN Companies_images AS pi
              ON p.Image_ID = pi.ID
            JOIN Message75 AS c
              ON p.Company_ID = c.Message_ID
            SET p.Image_ID = NULL
            WHERE c.enabled_auto_association_with_photos = 0 AND pi.Company_ID IS NULL
         ');
    }

    /**
     * @param FacetResultExtractor $facetResultExtractor
     *
     * @return Product[]
     */
    public function loadByFacetResult(FacetResultExtractor $facetResultExtractor)
    {
        return $this->findByIds($facetResultExtractor->getIds(), true);
    }

    /*
     * $data = array(category => defaultCategoryId, customCategory => null)
     */
    public function getProductsCountInCategory(Company $company, array $data)
    {
        $qb = $this
            ->createQueryBuilder('product')
            ->select('COUNT(product) AS _count')
            ->andWhere('product.company = :company')
            ->setParameter('company', $company)
            ->andWhere('product.checked NOT IN (:statuses)')
            ->setParameter('statuses', array(Product::STATUS_DELETED, Product::STATUS_LIMIT_EXCEEDING))
            ->andWhere('product.isVirtual = false');

        $i = 0;
        foreach ($data as $column => $value) {
            if (null === $value) {
                $qb->andWhere(sprintf('product.%s IS NULL', $column));
            } else {
                $qb->andWhere(sprintf('product.%s = :%s', $column, $column.$i))->setParameter($column.$i, $value);
            }

            $i++;
        }

        return $qb->setMaxResults(1)->getQuery()->getSingleScalarResult();
    }

    public function updateProductsItemHash(array $productsIds = array())
    {
        if (!$productsIds) {
            return;
        }

        $connection = $this->_em->getConnection();
        $products = $connection->fetchAll(
            'SELECT product.Message_ID AS id, product.Memo AS size, product.Name AS title, product.branch_office_id AS branch_office_id
          FROM Message142 AS product WHERE product.Message_ID IN(:productsIds) AND product.is_virtual = false',
            array(
                'productsIds' => $productsIds
            ),
            array(
                'productsIds' => Connection::PARAM_INT_ARRAY
            )
        );

        foreach ($products as $product) {
            $itemHash = Product::calculateItemHash($product['branch_office_id'], $product['title'], $product['size']);
            $connection->executeUpdate('UPDATE Message142 SET item_hash = :item_hash WHERE Message_ID = :id',
                array(
                    'item_hash' => $itemHash,
                    'id' => $product['id']
                )
            );
        }
    }

    public function moveToCategory(Company $company, CategoryAbstract $category, array $productsIds)
    {
        $productsQb = $this->getCheckedProductsQb($company, $productsIds);
        $updateQb = $this->createQueryBuilder('p')->update('MetalProductsBundle:Product', 'p');

        switch ($category) {
            case $category instanceof CustomCompanyCategory:
                $updateQb->set('p.customCategory', ':category');

                $productsQb->andWhere('(p.customCategory != :category OR p.customCategory IS NULL)');
                break;
            case $category instanceof Category:
                $updateQb->set('p.category', ':category')->set('p.checked', Product::STATUS_NOT_CHECKED);

                $productsQb->andWhere('p.category != :category');
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Category can not be instanceof "%s"', get_class($category)));
        }

        $products = $productsQb->setParameter('category', $category)->getQuery()->getResult();

        $updateQb->setParameter('category', $category)
            ->andWhere('p.id IN (:products)')
            ->setParameter('products', $productsIds)
            ->andWhere('p.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->execute()
        ;

        return $products;
    }

    public function changeOfferStatus(Company $company, $field, $status, array $productsIds)
    {
        $allowedFields = array('isSpecialOffer', 'isHotOffer');

        if (!in_array($field, $allowedFields)) {
            throw new \InvalidArgumentException('Allowed "isSpecialOffer"/"isHotOffer".');
        }

        $this
            ->createQueryBuilder('p')
            ->update('MetalProductsBundle:Product', 'p')
            ->set(sprintf('p.%s', $field), ':status')
            ->setParameter('status', $status)
            ->andWhere('p.id IN (:products)')
            ->setParameter('products', $productsIds)
            ->andWhere('p.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->execute()
        ;
    }

    public function getProductsToEnable(Company $company, array $productsIds)
    {
        if (!$productsIds) {
            return array();
        }

        return $this->getCheckedProductsQb($company, $productsIds)->getQuery()->getResult();
    }

    public function getCheckedProductsQb(Company $company, array $productsIds)
    {
        return $this
            ->createQueryBuilder('p', 'p.id')
            ->select('p.id')
            ->andWhere('p.id IN (:products_id)')
            ->setParameter('products_id', $productsIds, Connection::PARAM_INT_ARRAY)
            ->andWhere('p.company = :company')
            ->setParameter('company', $company)
            ->andWhere('p.checked = :status')
            ->setParameter('status', Product::STATUS_CHECKED);
    }

    public function getAllCompanyProductsPerBatches(Company $company, $batchSize = 5000)
    {
        do {
            $productsIds = $this
                ->createQueryBuilder('p', 'p.id')
                ->select('p.id')
                ->andWhere('p.checked <> :status')
                ->andWhere('p.company = :company')
                ->andWhere('p.isVirtual = false')
                ->setParameter('status', Product::STATUS_DELETED)
                ->setParameter('company', $company)
                ->setMaxResults($batchSize)
                ->getQuery()
                ->getResult();

            $productsIds = array_keys($productsIds);
            if ($productsIds) {
                yield $productsIds;
            }
        } while ($productsIds);
    }

    public function disableProducts(array $ids, User $user)
    {
        $this
            ->_em
            ->createQueryBuilder()
            ->update('MetalProductsBundle:Product', 'p')
            ->set('p.checked', ':status')
            ->setParameter('status', Product::STATUS_DELETED)
            ->andWhere('p.id IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->execute();

        $this
            ->_em
            ->createQueryBuilder()
            ->update('MetalProductsBundle:ProductLog', 'pl')
            ->set('pl.updatedBy', ':user')
            ->setParameter('user', $user)
            ->andWhere('pl.product IN (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()
            ->execute();
    }

    public function createIndexerQueryBuilder()
    {
        return $this->_em
            ->createQueryBuilder()
            ->select('e AS product')
            ->from('MetalProductsBundle:Product', 'e');
    }
}
