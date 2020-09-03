<?php

namespace Metal\CompaniesBundle\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityRepository;
use Metal\CompaniesBundle\Entity\Company;
use Metal\CompaniesBundle\Entity\CompanyCategory;
use Metal\ProductsBundle\ChangeSet\ProductsBatchEditStructure;
use Metal\ProductsBundle\Entity\Product;
use Metal\ProjectBundle\Util\InsertUtil;

class CompanyCategoryRepository extends EntityRepository
{
    public function loadCompanyCategoriesCollectionForCompany(Company $company)
    {
        $companyCategories = $this->_em->createQueryBuilder()
            ->select('cc')
            ->from('MetalCompaniesBundle:CompanyCategory', 'cc')
            ->join('cc.category', 'c')
            ->addSelect('c')
            ->orderBy('c.title')
            ->where('cc.company = :company')
            ->setParameter('company', $company)
            ->getQuery()
            ->getResult();
        /* @var $companyCategories CompanyCategory[] */

        $coll = $company->getCompanyCategories();
        foreach ($companyCategories as $companyCategory) { /* @var $coll \Doctrine\ORM\PersistentCollection */
            $coll->hydrateSet($companyCategory->getCategoryId(), $companyCategory);
        }
        $coll->setInitialized(true);
    }

    public function onProductStatusChanging(ProductsBatchEditStructure $structure)
    {
        $productsToCompanies = array();
        foreach ($structure->products as $product) {
            $productsToCompanies[$product['companyId']][] = $product['id'];
        }

        $this->updateCompaniesCategories($productsToCompanies);
    }

    /**
     * @param array $productsToCompanies Array like array(companyId1 => array(productId1, productId2, ...), companyId2 => array(productId4))
     * @param int $defaultCategoryId
     */
    public function updateCompaniesCategories(array $productsToCompanies, $defaultCategoryId = null)
    {
        if (!count($productsToCompanies)) {
            return;
        }

        $conn = $this->_em->getConnection();
        /* @var $conn Connection */

        $users = $conn->fetchAll(
            'SELECT u.ConnectCompany AS company_id, u.User_ID AS user_id FROM User AS u 
              WHERE u.ConnectCompany IN (:companies_ids)',
            array('companies_ids' => array_keys($productsToCompanies)),
            array('companies_ids' => Connection::PARAM_INT_ARRAY)
        );

        /*$usersToCompanies = array();
        foreach ($users as $user) {
            $usersToCompanies[$user['company_id']][] = $user['user_id'];
        }*/

        unset($users);

        foreach ($productsToCompanies as $companyId => $productsIds) {
            // 1. выбрать все текущие категории компании

            // 2. выбрать категории измененных продуктов

            // 3. взять те категории измененных продуктов, которые были автоматически добавлены

            // 4. для этих категорий проверить наличие товаров в базе

            // 5. построить массивы вставок/удалений

            // 6. на основании этих массивов триггернуть что-то в подписках

            $productsCategoriesQb = $conn
                ->createQueryBuilder()
                ->select('p.Category_ID AS category_id')
                ->from('Message142', 'p')
                ->where('p.Company_ID = :company_id')
                ->setParameter('company_id', $companyId);

            if ($defaultCategoryId) {
                $productsCategoriesQb
                    ->andWhere('p.Category_ID <> :default_category_id')
                    ->setParameter('default_category_id', $defaultCategoryId);
            }

            if ($productsIds) {
                $productsCategoriesQb->andWhere('p.Message_ID IN (:products_ids)')
                    ->setParameter('products_ids', $productsIds, Connection::PARAM_INT_ARRAY);
            } else {
                $productsCategoriesQb->andWhere('p.Checked = :checked')
                    ->setParameter('checked', Product::STATUS_CHECKED);
            }

            // получаем категории для измененных продуктов компании
            $productsCategories = $productsCategoriesQb
                ->groupBy('p.Category_ID')
                ->execute()
                ->fetchAll();

            if (!$productsCategories) {
                continue;
            }

            $productsCategoriesIds = array_column($productsCategories, 'category_id');
            $rowsQb = $conn->createQueryBuilder()
                ->select('product.Company_ID AS company_id')
                ->addSelect('product.Category_ID AS cat_id')
                ->addSelect('MIN(product.Created) AS Created')
                ->addSelect('1 AS is_automatically_added')
                ->from('Message142', 'product')
                ->where('product.Checked = :checked')
                ->setParameter('checked', Product::STATUS_CHECKED)
                ->andWhere('product.Category_ID IN (:categories_ids)')
                ->setParameter('categories_ids', $productsCategoriesIds, Connection::PARAM_INT_ARRAY)
                ->andWhere('product.Company_ID = :company_id')
                ->setParameter('company_id', $companyId);

            if ($defaultCategoryId) {
                $rowsQb
                    ->andWhere('product.Category_ID <> :default_category_id')
                    ->setParameter('default_category_id', $defaultCategoryId);
            }

            $rows = $rowsQb
                ->groupBy('product.Category_ID')
                ->execute()
                ->fetchAll();

            if ($rows) {
                InsertUtil::insertMultipleOrUpdate($conn, 'Message76', $rows, array('cat_id'), 100);
            }

            //1. Выгружаем те категории которые автоматически загруженные
            $companyCategoriesQb = $conn
                ->createQueryBuilder()
                ->select('companyCategory.cat_id AS category_id')
                ->from('Message76', 'companyCategory')
                ->where('companyCategory.is_automatically_added = true')
                ->andWhere('companyCategory.company_id = :company_id')
                ->setParameter('company_id', $companyId);

            if ($productsIds) {
                $companyCategoriesQb
                    ->andWhere('companyCategory.cat_id IN (:categories_ids)')
                    ->setParameter('categories_ids', $productsCategoriesIds, Connection::PARAM_INT_ARRAY);
            }

            $companyCategories = $companyCategoriesQb->execute()->fetchAll();

            $companyCategoriesIds = array_column($companyCategories, 'category_id');
            $existingCategoriesIds = array_column($rows, 'cat_id', 'cat_id');
            $categoriesToDeleted = array();
            foreach ($companyCategoriesIds as $companyCategoryId) {
                if (!isset($existingCategoriesIds[$companyCategoryId])) {
                    $categoriesToDeleted[] = $companyCategoryId;
                }
            }

            if ($categoriesToDeleted) {
                $conn->executeUpdate(
                    'DELETE FROM Message76 WHERE is_automatically_added = :is_automatically_added 
                  AND company_id = :company_id AND cat_id IN(:categories_ids)',
                    array(
                        'categories_ids' => $categoriesToDeleted,
                        'company_id' => $companyId,
                        'is_automatically_added' => true,
                    ),
                    array(
                        'categories_ids' => Connection::PARAM_INT_ARRAY,
                    )
                );
            }

            /*$demandSubscriptionCategoryData = array();
            foreach ((array) $usersToCompanies[$companyId] AS $userCompany) {
                foreach ($companyCategoriesIds as $companyCategoryId) {
                    $demandSubscriptionCategoryData[] = array(
                        'user_id' => $userCompany,
                        'category_id' => $companyCategoryId,
                    );
                }
            }*/

            // подписываем пользователя на новые категории
            /*if ($demandSubscriptionCategoryData) {
                InsertUtil::insertMultipleOrUpdate($conn, 'demand_subscription_category', $demandSubscriptionCategoryData, array('category_id'), 100);
            }*/

            // отписывать пользователей от удаленных категорий нет необходимости
        }

        // вызвать onInsertCompanyCategory, мы подписываем пользователя на категории только в том случае, если он уже был на что-то подписан
        $this->onInsertCompanyCategory(array_keys($productsToCompanies));
    }

    public function getCompanyCategoriesByCompany($companyId)
    {
        $companyCategoriesQb = $this->_em
            ->getConnection()
            ->createQueryBuilder()
            ->select('cc.id')
            ->addSelect('cc.cat_id AS category_id')
            ->addSelect('cc.is_automatically_added')
            ->from('Message76', 'cc')
            ->andWhere('cc.company_id = :company_id')
            ->setParameter('company_id', $companyId);

        $companyCategories = $companyCategoriesQb->execute()->fetchAll();

        $result = array();
        foreach ($companyCategories as $companyCategory) {
            $result[$companyCategory['category_id']] = $companyCategories;
        }

        return $result;
    }

    /**
     * @param array $companiesIds
     */
    public function onInsertCompanyCategory(array $companiesIds = array())
    {
        //TODO: можно переделать на 2 аргумента - companyId, categoriesIds
        // тогда первым запросом выгребсти всех пользователей компании, у которых есть хоть одна запись в demand_subscription_category
        // и далее составить массив вставок и bachInsert сделать, не лезть при этом в message76 и вставлять меньше строк а не все
        $conn = $this->_em->getConnection();
        $params = array();
        $paramsTypes = array();

        $sqb = $conn->createQueryBuilder();
        $sqb->select('u.User_ID AS user_id, cc.cat_id')
            ->from('User', 'u')
            ->join('u', 'Message76', 'cc', 'u.ConnectCompany = cc.company_id');

        if ($companiesIds) {
            $sqb->andWhere('u.ConnectCompany IN (:companies_ids)');
            $params['companies_ids'] = $companiesIds;
            $paramsTypes['companies_ids'] = Connection::PARAM_INT_ARRAY;
        }

        $sqb->andWhere('EXISTS (SELECT dsc.id FROM demand_subscription_category AS dsc WHERE dsc.user_id = u.User_ID)');

        $conn->executeUpdate(
            'INSERT IGNORE INTO demand_subscription_category (user_id, category_id) '.$sqb->getSQL(),
            $params,
            $paramsTypes
        );
    }

    public function onDeleteCompanyCategory($companyId, $categoriesIds)
    {
        // не отписываем пользователей от категорий заявок
        return;
    }

    /**
     * @param array $companiesIds
     *
     * @return array [companyId => [categoryId, categoryId, ...]]
     */
    public function getCategoriesIdsForCompanies(array $companiesIds = array())
    {
        if (!$companiesIds) {
            return array();
        }

        $companyToCategories = array_fill_keys($companiesIds, array());

        $companyCategories = $this->_em->getRepository('MetalCompaniesBundle:CompanyCategory')
            ->createQueryBuilder('companyCategory')
            ->select('IDENTITY(companyCategory.company) AS companyId')
            ->addSelect('IDENTITY(companyCategory.category) AS categoryId')
            ->where('companyCategory.company IN (:companies)')
            ->andWhere('companyCategory.enabled = true')
            ->setParameter('companies', $companiesIds)
            ->getQuery()
            ->getArrayResult();

        $categoriesIds = array_column($companyCategories, 'categoryId');

        $categories = $this->_em->createQueryBuilder()
            ->from('MetalCategoriesBundle:CategoryClosure', 'cc')
            ->select('IDENTITY(cc.descendant) as descendant')
            ->addSelect('IDENTITY(cc.ancestor) as ancestor')
            ->where('cc.descendant IN (:categories_ids)')
            ->setParameter('categories_ids', $categoriesIds)
            ->getQuery()
            ->getResult();

        $categoriesToDescendant = array();
        foreach ($categories as $category) {
            $categoriesToDescendant[$category['descendant']][] = $category['ancestor'];
        }

        foreach ($companyCategories as $companyCategory) {
            if (!isset($companyToCategories[$companyCategory['companyId']])) {
                $companyToCategories[$companyCategory['companyId']] = array();
            }
            $companyToCategories[$companyCategory['companyId']] = array_merge($companyToCategories[$companyCategory['companyId']], $categoriesToDescendant[$companyCategory['categoryId']]);
        }

        return $companyToCategories;
    }

    public function disableCompanyCategories($companyId)
    {
        $this->_em->getConnection()->executeUpdate(
            "UPDATE Message76 AS companyCategory
              SET companyCategory.enabled = false
              WHERE companyCategory.company_id = :company_id AND companyCategory.is_automatically_added = false",
            array(
                'company_id' => $companyId
            ),
            array(
                'company_id' => \PDO::PARAM_INT
            )
        );
    }

    public function enableCompanyCategoriesByLimit($companyId, $limit)
    {
        $this->_em->getConnection()->executeUpdate(
            sprintf('UPDATE Message76 AS companyCategory
                SET companyCategory.enabled = true
                WHERE companyCategory.company_id = :company_id AND companyCategory.is_automatically_added = false %s',
                null !== $limit ? 'LIMIT ' . $limit : ''
            ),
            array(
                'company_id' => $companyId
            ),
            array(
                'company_id' => \PDO::PARAM_INT
            )
        );
    }
}
