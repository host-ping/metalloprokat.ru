<?php

namespace Metal\DemandsBundle\Controller;

use Doctrine\ORM\EntityManager;
use Metal\CategoriesBundle\Entity\Category;
use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\DemandsBundle\Entity\Demand;
use Metal\DemandsBundle\Entity\DemandAnswer;
use Metal\DemandsBundle\Form\DemandAnswerType;
use Metal\DemandsBundle\Helper\DefaultHelper;
use Metal\DemandsBundle\Widget\SimilarDemandsWidget;
use Metal\ProjectBundle\Exception\FormValidationException;
use Metal\TerritorialBundle\Entity\City;
use Metal\TerritorialBundle\Entity\Region;
use Metal\UsersBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class DemandController extends Controller
{
    /**
     * @ParamConverter("demand", class="MetalDemandsBundle:Demand", options={"repository_method"="loadDemand"})
     */
    public function viewAction(Request $request, Demand $demand, Category $category, City $city = null, Region $region = null)
    {
        if (!in_array($category->getId(), $demand->getCategoriesIds())) {
            return $this->redirectToRoute(
                'MetalDemandsBundle:Demand:view',
                array(
                    'id' => $demand->getId(),
                    'subdomain' => $demand->getCity()->getSlugWithFallback(),
                    'category_slug' => $demand->getCategory()->getSlugCombined(),
                ),
                301
            );
        }

        $subdomain = $request->attributes->get('subdomain');
        if ($city && $subdomain !== $demand->getCity()->getSlugWithFallback()) {
            return $this->redirectToRoute(
                'MetalDemandsBundle:Demand:view',
                array(
                    'subdomain' => $demand->getCity()->getSlugWithFallback(),
                    'category_slug' => $category->getSlugCombined(),
                    'id' => $demand->getId(),
                ),
                301
            );
        }

        if ($region && $subdomain !== $demand->getCity()->getRegion()->getSlug()) {
            return $this->redirectToRoute(
                'MetalDemandsBundle:Demand:view',
                array(
                    'subdomain' => $demand->getCity()->getRegion()->getSlug(),
                    'category_slug' => $category->getSlugCombined(),
                    'id' => $demand->getId(),
                ),
                301
            );
        }

        $doctrine = $this->getDoctrine();
        $demandItemRepository = $doctrine->getRepository('MetalDemandsBundle:DemandItem');

        if ($request->isXmlHttpRequest()) {
            $widgetManager = $this->get('brouzie_widgets.widget_manager');
            $similarDemandsWidget = $widgetManager
                ->createWidget(
                    'MetalDemandsBundle:SimilarDemands',
                    array(
                        'demand' => $demand,
                        'category' => $category,
                        'city' => $city,
                        'page' => $request->query->get('page', 1),
                    )
                );
            /* @var $similarDemandsWidget SimilarDemandsWidget */

            $demandItemRepository->attachDemandItems(array($demand));

            $response = JsonResponse::create(
                array(
                    'page.title' => $this->get('brouzie.helper_factory')->get('MetalProjectBundle:Seo')->getMetaTitleForDemandPage($demand),
                    'page.similar_demands_list' => $widgetManager->renderWidget($similarDemandsWidget)
                )
            );

            $response->headers->addCacheControlDirective('no-store', true);

            return $response;
        }

        $demandRepository = $doctrine->getRepository('MetalDemandsBundle:Demand');
        $favoriteRepository = $doctrine->getRepository('MetalUsersBundle:Favorite');
        $demandFileRepository = $doctrine->getRepository('MetalDemandsBundle:DemandFile');

        $user = null;
        $favorite = null;
        if ($this->isGranted('VIEW_DEMAND_CONTACTS', $demand) ) {
            $user = $this->getUser();
            if ($user instanceof User) {
                $demandHelper = $this->container->get('brouzie.helper_factory')->get('MetalDemandsBundle');
                $company = $user->getCompany();
                if ($company->getId() == 2052995) {
                    $demand->setPerson($demandHelper->getFakePerson());
                    $demand->setPhone($demandHelper->screwPhone($demand->getPhone()));
                    $demand->setEmail($demandHelper->screwEmail($demand->getEmail()));
                }
                $demandHelper->trackDemandView($demand, $user);
                $favorite = $favoriteRepository->findOneBy(array('demand' => $demand->getId(), 'user' => $user->getId()));
            }
        }

        $demandRepository->attachDemandIsInFavorite(array($demand), $user);
        $demandItemRepository->attachDemandItems(array($demand), $category);
        $demandFileRepository->attachDemandFiles(array($demand));

        return $this->render('@MetalDemands/Demand/view.html.twig', array(
            'demand' => $demand,
            'favorite' => $favorite,
        ));
    }

    /**
     * @ParamConverter("demand", class="MetalDemandsBundle:Demand")
     */
    public function viewOutsideAction(Demand $demand)
    {
        return $this->redirectToRoute(
            'MetalDemandsBundle:Demand:view',
            array(
                'id' => $demand->getId(),
                'subdomain' => $demand->getCity()->getSlugWithFallback(),
                'category_slug' => $demand->getCategory()->getSlugCombined(),
            ),
            301
        );
    }

    /**
     * @ParamConverter("demand", class="MetalDemandsBundle:AbstractDemand")
     * @Security("is_granted('DEMAND_ANSWER', demand)")
     */
    public function answerAction(Request $request, AbstractDemand $demand)
    {
        // NB! этот экшн используется и в личном кабинете
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $demandAnswer = new DemandAnswer();
        $options = array(
            'city_repository' => $this->container->get('doctrine')->getRepository('MetalTerritorialBundle:City'),
            'is_authenticated' => $user !== null,
            'validation_groups' => array(
                $user !== null ? 'authenticated' : 'anonymous',
            )
        );

        $form = $this->createForm(new DemandAnswerType(), $demandAnswer, $options);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($form);

            return JsonResponse::create(array(
                'errors' => $errors,
            ));
        }

        if ($user) {
            $demandAnswer->setCity($user->getCityWithFallback());
        }
        $demandAnswer->setUser($user);
        $demandAnswer->setDemand($demand);
        $em->persist($demandAnswer);
        $em->flush();

        $em->createQuery('UPDATE MetalDemandsBundle:AbstractDemand d SET d.answersCount = d.answersCount + 1 WHERE d.id = :id')
            ->setParameter('id', $demand->getId())
            ->execute();

        $mailer = $this->get('metal.newsletter.mailer');
        $replyTo = array($demand->getFixedEmail() => $demand->getFixedCompanyTitle());

        try {
            $mailer->sendMessage(
                'MetalDemandsBundle::emails/demand_answer.html.twig',
                array($demand->getFixedEmail() => $demand->getFixedUserTitle()),
                array(
                    'demand'       => $demand,
                    'demandAnswer' => $demandAnswer,
                    'country'      => $demand->getCity()->getCountry()
                ),
                null,
                $replyTo
            );
        } catch (\Swift_RfcComplianceException $e) {

        }

        return JsonResponse::create(array(
            'status' => 'success'
        ));
    }

    /**
     * @ParamConverter("demand", class="MetalDemandsBundle:Demand")
     */
    public function showAction(Demand $demand)
    {
        if ($this->isGranted('VIEW_DEMAND_CONTACTS', $demand)) {
            $user = $this->getUser();
            if ($user instanceof User) {
                $demandHelper = $this->container->get('brouzie.helper_factory')->get('MetalDemandsBundle');
                $company = $user->getCompany();
                if ($company->getId() == 2052995) {
                    $demand->setPerson($demandHelper->getFakePerson());
                    $demand->setPhone($demandHelper->screwPhone($demand->getPhone()));
                    $demand->setEmail($demandHelper->screwEmail($demand->getEmail()));
                }
                /* @var $demandHelper DefaultHelper */
                $demandHelper->trackDemandView($demand, $user);
            }
        }

        return $this->render('@MetalDemands/Demands/contacts_in_list.html.twig', array(
            'demand' => $demand
        ));
    }

    public function saveFormAction(Request $request, $is_private)
    {
        $user = $this->getUser();

        try {
            $demand = $this->get('metal.demands.management_services')->createDemandFromRequest($request, $is_private, $user);
        } catch (FormValidationException $e) {
            $errors = $this->get('metal.project.form_helper')->getFormErrorMessages($e->getForm());

            return JsonResponse::create(array(
                'errors' => $errors
                 // set Content-Type header for IE
            ), 200, array('Content-Type' => 'text/plain'));
        }

        $companyAddInFavoriteFlag = $request->request->get('company_add_in_favorite');
        if ($companyAddInFavoriteFlag && $user) {
            $this->get('metal.users.favorite_services')->addCompanyToFavorite($user, $demand->getCompany());
        }

        // http://projects.brouzie.com/browse/MET-2127  Всегда создаем копию приватной заявки - отменили
        $createPublicDemand = $request->request->get('create_public_demand');
        #$createPublicDemand = $is_private ? true : false;
        if ($createPublicDemand) {
            $this->get('metal.demands.management_services')->createDemandFromPrivateDemand($demand);
        }

        $response = array(
            'status' => 'success'
        );

        return JsonResponse::create(
            $response,
            200,
            // set Content-Type header for IE
            array('Content-Type' => 'text/plain')
        );
    }

    public function confirmDemandFromPossibleUserAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager  */
        $code = $request->query->get('code');
        $id = $request->query->get('id');

        if ($demand = $em->getRepository('MetalDemandsBundle:AbstractDemand')->find($id)) {
            if ($demand->getConfirmationCode() == $code) {
                $demand->setUser($demand->getPossibleUser());
                $demand->setPossibleUser(null);
                $demand->setConfirmationCode(null);
                $em->flush();
            } else {
                die('Неверный код подтверждения');
            }
        } else {
            die('Заявка не найдена');
        }

        return $this->redirectToRoute('MetalProjectBundle:Default:index');
    }

    /**
     * @ParamConverter("demand", class="MetalDemandsBundle:Demand")
     */
    public function redirectFromOldDemandViewAction(Demand $demand)
    {
        return $this->redirectToRoute(
            'MetalDemandsBundle:Demand:view',
            array(
                'id' => $demand->getId(),
                'subdomain' => $demand->getCity()->getSlugWithFallback(),
                'category_slug' => $demand->getCategory()->getSlugCombined(),
            )
        );
    }
}
