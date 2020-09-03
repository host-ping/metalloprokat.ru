<?php

namespace Metal\UsersBundle\Service;

use Doctrine\ORM\EntityManager;
use Metal\UsersBundle\Entity\FavoriteCompany;

class FavoriteService
{

    /**
     * @var FavoriteCompany
     */
    private $favoriteCompanyRepository;

    /**
     * @var \Metal\UsersBundle\Repository\UserCounterRepository
     */
    private $userCounterRepository;

    /**
     * @var EntityManager
     */
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->favoriteCompanyRepository = $em->getRepository('MetalUsersBundle:FavoriteCompany');
        $this->userCounterRepository = $em->getRepository('MetalUsersBundle:UserCounter');

    }

    public function addCompanyToFavorite($user, $company)
    {
        $favoriteCompany = $this->favoriteCompanyRepository->findOneBy(array(
            'company' => $company,
            'user' => $user,
        ));

        if ($favoriteCompany) {
            return;
        }

        $favoriteCompany = new FavoriteCompany();
        $favoriteCompany->setCompany($company);
        $favoriteCompany->setUser($user);

        $this->em->persist($favoriteCompany);
        $this->em->flush();

        $this->userCounterRepository->changeCounter($user, 'favoriteCompaniesCount', true);
    }
}
