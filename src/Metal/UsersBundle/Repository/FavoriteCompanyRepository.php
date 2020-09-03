<?php

namespace Metal\UsersBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Metal\TerritorialBundle\Entity\Country;
use Metal\UsersBundle\Entity\Favorite;
use Metal\UsersBundle\Entity\FavoriteCompany;

class FavoriteCompanyRepository extends EntityRepository
{
    /**
     * @param FavoriteCompany[] $favoriteCompanies
     */
    public function attachProductsCount(array $favoriteCompanies)
    {
        if (!count($favoriteCompanies)) {
            return;
        }

        $directedFavoriteCompanies = array();
        foreach ($favoriteCompanies as $favoriteCompany) {
            $directedFavoriteCompanies[$favoriteCompany->getId()] = $favoriteCompany;
            $favoriteCompany->setAttribute('productsCount', 0);
        }

        $countersProductCompany = $this->_em->createQueryBuilder()
            ->from('MetalUsersBundle:Favorite', 'f')
            ->select('IDENTITY(f.favoriteCompany) AS favoriteCompanyId, COUNT(f.id) AS productsCount')
            ->andWhere('f.favoriteCompany IN (:companies_ids)')
            ->groupBy('f.favoriteCompany')
            ->setParameter('companies_ids', array_keys($directedFavoriteCompanies))
            ->getQuery()
            ->getArrayResult();

        foreach ($countersProductCompany as $countProductCompany) {
            $directedFavoriteCompanies[$countProductCompany['favoriteCompanyId']]
                ->setAttribute('productsCount', $countProductCompany['productsCount']);
        }
    }

    /**
     * @param FavoriteCompany[] $favoriteCompanies
     * @param Country|null $country
     */
    public function attachProducts(array $favoriteCompanies, Country $country = null)
    {
        if (!count($favoriteCompanies)) {
            return;
        }

        $directedFavoriteCompanies = array();
        foreach ($favoriteCompanies as $favoriteCompany) {
            $directedFavoriteCompanies[$favoriteCompany->getId()] = $favoriteCompany;
        }

        $companyProducts =
            $this->_em->createQueryBuilder()
                ->select('f AS favorite, IDENTITY(f.favoriteCompany) AS favoriteCompanyId')
                ->from('MetalUsersBundle:Favorite', 'f')
                ->leftJoin('f.product', 'fp')
                ->addSelect('fp')
                ->andWhere('f.product IS NOT NULL')
                ->andWhere('f.favoriteCompany IN (:companies_ids)')
                ->addOrderBy('f.createdAt', 'DESC')
                ->setParameter('companies_ids', array_keys($directedFavoriteCompanies))
                ->getQuery()
                ->getResult();

        $tmpArray = array();
        $productsToAttachNormalizedPrice = array();
        foreach ($companyProducts as $productCompany) {
            $id = $productCompany['favoriteCompanyId'];
            if (!isset($tmpArray[$id])) {
                $tmpArray[$id] = array();
            }

            $tmpArray[$id][] = $productCompany['favorite'];
            $productsToAttachNormalizedPrice[] = $productCompany['favorite']->getProduct();
        }

        foreach ($tmpArray as $id => $favorites) {
            $directedFavoriteCompanies[$id]
                ->setAttribute('favorites', $favorites);
        }
        $this->_em->getRepository('MetalProductsBundle:Product')->attachNormalizedPrice(
            $productsToAttachNormalizedPrice,
            $country
        );
    }
}
