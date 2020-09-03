<?php

namespace Metal\PrivateOfficeBundle\Controller;

use Metal\DemandsBundle\Entity\DemandSubscriptionCategory;
use Metal\DemandsBundle\Entity\DemandSubscriptionTerritorial;
use Metal\TerritorialBundle\Entity\TerritorialStructure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Doctrine\ORM\EntityManager;

class PrivateSubscriptionsManagementController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUPPLIER')")
     */
    public function updateCategoriesSubscriptionsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $user = $this->getUser();

        $afterChangeCategoriesIds = (array)$request->request->get('items_ids');
        $demandSubscriptionCategoryRepository = $em->getRepository('MetalDemandsBundle:DemandSubscriptionCategory');
        $beforeChangeCategoriesIds = $demandSubscriptionCategoryRepository->getCategoryIdsPerUser(array($user->getId()))[$user->getId()];

        $categoriesToDelete = array_diff($beforeChangeCategoriesIds, $afterChangeCategoriesIds);
        $categoriesToAdd = array_diff($afterChangeCategoriesIds, $beforeChangeCategoriesIds);

        if ($categoriesToDelete) {
            $demandSubscriptionCategoriesToDelete = $demandSubscriptionCategoryRepository->findBy(array('user' => $user, 'category' => $categoriesToDelete));

            foreach ($demandSubscriptionCategoriesToDelete as $demandSubscriptionCategoryToDelete) {
                $em->remove($demandSubscriptionCategoryToDelete);
            }
        }

        foreach ($categoriesToAdd as $categoryToAdd) {
            $demandSubscriptionCategory = new DemandSubscriptionCategory();
            $demandSubscriptionCategory->setCategory($em->getReference('MetalCategoriesBundle:Category', $categoryToAdd));
            $demandSubscriptionCategory->setUser($this->getUser());

            $em->persist($demandSubscriptionCategory);
        }

        $em->flush();

         return JsonResponse::create(array(
            'status' => 'success',
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER')")
     */
    public function updateTerritorySubscriptionsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */
        $user = $this->getUser();

        $afterChangeTerritorialIds = (array)$request->request->get('items_ids');

        $demandSubscriptionTerritorialRepository = $em->getRepository('MetalDemandsBundle:DemandSubscriptionTerritorial');
        $beforeChangeTerritorialIds = $demandSubscriptionTerritorialRepository->getSubscribedTerritorialIdsPerUser(array($user->getId()))[$user->getId()];

        $territoriesToDelete = array_diff($beforeChangeTerritorialIds, $afterChangeTerritorialIds);
        $territoriesToAdd = array_diff($afterChangeTerritorialIds, $beforeChangeTerritorialIds);

        if ($territoriesToDelete) {
            $demandSubscriptionTerritoriesToDelete = $demandSubscriptionTerritorialRepository->findBy(array('user' => $user, 'territorialStructure' => $territoriesToDelete));

            foreach ($demandSubscriptionTerritoriesToDelete as $demandSubscriptionTerritoryToDelete) {
                $em->remove($demandSubscriptionTerritoryToDelete);
            }
        }

        foreach ($territoriesToAdd as $territoryToAdd) {
            $territorialStructure = $em->find('MetalTerritorialBundle:TerritorialStructure', $territoryToAdd);
            /* @var $territorialStructure TerritorialStructure */

            $demandSubscriptionTerritorial = new DemandSubscriptionTerritorial();
            $demandSubscriptionTerritorial->setTerritorialStructure($territorialStructure);
            $demandSubscriptionTerritorial->setUser($this->getUser());

            if ($city = $territorialStructure->getCity()) {
                $demandSubscriptionTerritorial->setCity($city);
            }
            if ($region = $territorialStructure->getRegion()) {
                $demandSubscriptionTerritorial->setRegion($region);
            }
            if ($federalDistrict = $territorialStructure->getFederalDistrict()) {
                $demandSubscriptionTerritorial->setFederalDistrict($federalDistrict);
            }

            $em->persist($demandSubscriptionTerritorial);
        }

        $em->flush();

        return JsonResponse::create(array(
            'status' => 'success',
        ));
    }

    public function toggleSubscriptionStatusAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $subscriber = $em->getRepository('MetalNewsletterBundle:Subscriber')->findOneBy(array('user' => $this->getUser()));
        $subscriber->setSubscribedForDemands($request->request->get('checked'));

        $em->flush();

        return JsonResponse::create(array(
            'status' => 'success',
        ));
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER')")
     */
    public function categoriesSubscriptionByUserAction()
    {
        $userId = $this->getUser()->getId();
        $categories = $this->getDoctrine()->getManager()
            ->getRepository('MetalDemandsBundle:DemandSubscriptionCategory')
            ->getCategoryIdsPerUser(array($userId))[$userId];

        return JsonResponse::create($categories);
    }

    /**
     * @Security("has_role('ROLE_SUPPLIER')")
     */
    public function territoriesSubscriptionByUserAction()
    {
        $userId = $this->getUser()->getId();
        $territories = $this->getDoctrine()->getManager()
            ->getRepository('MetalDemandsBundle:DemandSubscriptionTerritorial')
            ->getSubscribedTerritorialIdsPerUser(array($userId))[$userId];

        return JsonResponse::create($territories);
    }
}
