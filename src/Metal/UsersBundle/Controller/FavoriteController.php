<?php

namespace Metal\UsersBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\UsersBundle\Entity\Favorite;
use Metal\UsersBundle\Entity\FavoriteCompany;
use Metal\UsersBundle\Entity\User;
use Metal\UsersBundle\Repository\UserCounterRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class FavoriteController extends Controller
{
    /**
     * @Security("has_role('ROLE_USER') and user.isAllowedAddInFavorite()")
     */
    public function toggleFavoriteAction($id, $object)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $favoriteRepository = $em->getRepository('MetalUsersBundle:Favorite');
        $favoriteCompanyRepository = $em->getRepository('MetalUsersBundle:FavoriteCompany');

        $user = $this->getUser();
        /* @var $user User */

        $userCounterRepository = $em->getRepository('MetalUsersBundle:UserCounter');
        /* @var $userCounterRepository UserCounterRepository */
        switch ($object) {

            case 'company':
                $favoriteCompany = $favoriteCompanyRepository->findOneBy(
                    array('company' => $id, 'user' => $user->getId())
                );

                if (!$favoriteCompany) {
                    $company = $em->find('MetalCompaniesBundle:Company', $id);
                    $favoriteCompany = new FavoriteCompany();
                    $favoriteCompany->setUser($user);
                    $favoriteCompany->setCompany($company);
                    $em->persist($favoriteCompany);
                    $userCounterRepository->changeCounter($user, 'favoriteCompaniesCount', true);
                } else {
                    $em->remove($favoriteCompany);
                    $userCounterRepository->changeCounter($user, 'favoriteCompaniesCount', false);
                }
                $em->flush();

                return JsonResponse::create(
                    array(
                        'status' => 'success'
                    )
                );
            case 'demand':
                $favorite = $favoriteRepository->findOneBy(array('demand' => $id, 'user' => $user->getId()));
                break;
            case 'product':
                $favorite = $favoriteRepository->findOneBy(array('product' => $id, 'user' => $user->getId()));
                break;
            default:
                throw $this->createNotFoundException(sprintf('Bad object "%s"', $object));
                break;
        }

        if (!$favorite) {
            $favorite = new Favorite();
            switch ($object) {
                case 'demand':
                    $demand = $em->getRepository('MetalDemandsBundle:Demand')->find($id);
                    $favorite->setDemand($demand);
                    $userCounterRepository->changeCounter($user, 'favoriteDemandsCount', true);
                    break;
                case 'product' :
                    $product = $em->getRepository('MetalProductsBundle:Product')->find($id);

                    $favorite->setProduct($product);
                    $company = $product->getCompany();
                    // запрос в favoriteCompany на основании комп и юзера.
                    $favoriteCompany = $favoriteCompanyRepository->findOneBy(
                        array('company' => $company->getId(), 'user' => $user->getId())
                    );

                    if (!$favoriteCompany) {
                        $favoriteCompany = new FavoriteCompany();
                        $favoriteCompany->setUser($user);
                        $favoriteCompany->setCompany($company);
                        $em->persist($favoriteCompany);
                        $userCounterRepository->changeCounter($user, 'favoriteCompaniesCount', true);
                    }
                    $favorite->setFavoriteCompany($favoriteCompany);
                    $userCounterRepository->changeCounter($user, 'favoriteProductsCount', true);
                    break;
            }
            $favorite->setUser($user);

            $em->persist($favorite);
        } else {
            switch ($object) {
                case 'demand':
                    $userCounterRepository->changeCounter($user, 'favoriteDemandsCount', false);
                    break;
                case 'product' :
                    $userCounterRepository->changeCounter($user, 'favoriteProductsCount', false);
                    break;
            }
            $em->remove($favorite);
        }

        $em->flush();

        return JsonResponse::create(
            array(
                'status' => 'success'
            )
        );
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function addCommentAction(Request $request, $id, $object)
    {
        if ($object === 'favorite') {
            $entityName = 'MetalUsersBundle:Favorite';
        } elseif ($object === 'favoriteCompany') {
            $entityName = 'MetalUsersBundle:FavoriteCompany';
        } else {
            throw $this->createNotFoundException('Bad object');
        }

        $em = $this->getDoctrine()->getManager();

        $repository = $em->getRepository($entityName);

        $user = $this->getUser();
        /* @var $user User */
        //TODO: paramconverter
        $favorite = $repository->find($id);

        if (!$favorite) {
            throw $this->createNotFoundException('Favorite no in db');
        }
        //TODO: перенести в аннотацию security
        if ($user->getId() != $favorite->getUser()->getId()) {
            throw $this->createNotFoundException('Not user favorite ');
        }

        // add text comment in fav.
        $form = $this->createFormBuilder($favorite, array('csrf_protection' => false))
            ->add('comment', 'text')
            ->getForm();

        $form->handleRequest($request);
        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(
                array(
                    'errors' => $errors,
                )
            );
        }

        $favorite->setCommentUpdatedAt(new \DateTime());
        $em->persist($favorite);
        $em->flush();

        return JsonResponse::create(
            array(
                'status' => 'success',
                'text' => $favorite->getComment(),
                'date' => $this->get('brouzie.helper_factory')->get(
                    'MetalProjectBundle:Formatting'
                )->formatDateTime($favorite->getCommentUpdatedAt()),
            )
        );
    }
}
