<?php

namespace Metal\DemandsBundle\DataFetching;

use Doctrine\ORM\EntityManagerInterface;
use Metal\DemandsBundle\DataFetching\Spec\DemandLoadingSpec;
use Metal\DemandsBundle\Entity\Demand;
use Metal\ProjectBundle\DataFetching\ConcreteEntityLoader;
use Metal\ProjectBundle\DataFetching\Spec\LoadingSpec;
use Metal\ProjectBundle\DataFetching\UnsupportedSpecException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class DemandsEntityLoader implements ConcreteEntityLoader
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    public function __construct(EntityManagerInterface $em, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function getEntitiesByRows(\Traversable $rows, LoadingSpec $options = null)
    {
        if (null === $options) {
            $options = new DemandLoadingSpec();
        } elseif (!$options instanceof DemandLoadingSpec) {
            throw UnsupportedSpecException::create(DemandLoadingSpec::class, $options);
        }

        $demandsIds = array_column(iterator_to_array($rows), 'id');

        if (!$demandsIds) {
            return array();
        }

        $specification = array(
            'id' => $demandsIds,
            'index_by_id' => true,
            'preload_administrative_center' => true,
        );

        $demands = $this->em->getRepository('MetalDemandsBundle:Demand')
            ->getDemandsQbBySpecification($specification)
            ->getQuery()
            ->getResult();
        /* @var $demands Demand[] */

        $loadedDemands = array();
        foreach ($demandsIds as $demandId) {
            if (isset($demands[$demandId])) {
                $loadedDemands[] = $demands[$demandId];
            }
        }

        if ($options->attachDemandItem) {
            $demandItemRepository = $this->em->getRepository('MetalDemandsBundle:DemandItem');
            $category = null;
            if ($options->categoryId) {
                $category = $this->em->getRepository('MetalCategoriesBundle:Category')->find($options->categoryId);
            }
            $demandItemRepository->attachDemandItems($demands, $category);
        }

        if ($options->attachCategories) {
            $demandCategoryRepository = $this->em->getRepository('MetalDemandsBundle:DemandCategory');
            $demandCategoryRepository->attachToDemands($demands);
        }

        if ($options->attachFavorite) {
            $user = null;
            if ($this->authorizationChecker->isGranted('ROLE_USER')) {
                $user = $this->tokenStorage->getToken()->getUser();
            }
            $this->em->getRepository('MetalDemandsBundle:Demand')->attachDemandIsInFavorite($loadedDemands, $user);
        }

        if ($options->attachDemandFiles) {
            $this->em->getRepository('MetalDemandsBundle:DemandFile')->attachDemandFiles($loadedDemands);
        }

        if ($options->attachCitiesAndRegions) {
            $this->em->getRepository('MetalDemandsBundle:Demand')->attachCitiesToDemands($loadedDemands, true);
        }

        return $loadedDemands;
    }
}
