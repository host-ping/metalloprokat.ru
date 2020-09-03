<?php

namespace Metal\DemandsBundle\Controller;

use DeepCopy\DeepCopy;
use DeepCopy\Filter\Doctrine\DoctrineEmptyCollectionFilter;
use DeepCopy\Filter\KeepFilter;
use DeepCopy\Filter\SetNullFilter;
use DeepCopy\Matcher\PropertyMatcher;
use Doctrine\ORM\EntityManager;
use Metal\DemandsBundle\ChangeSet\DemandsBatchEditChangeSet;
use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\DemandsBundle\Entity\Demand;
use Metal\DemandsBundle\Entity\DemandView;
use Metal\DemandsBundle\Entity\PrivateDemand;
use Metal\ProjectBundle\Entity\ClientIp;
use Metal\ProjectBundle\Entity\ValueObject\SiteSourceTypeProvider;
use Metal\ProjectBundle\Helper\FormattingHelper;
use Metal\UsersBundle\Entity\User;
use Sonata\AdminBundle\Controller\CRUDController;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DemandAdminController extends CRUDController
{
    public function showAction($id = null)
    {
        $request = $this->get('request_stack')->getCurrentRequest();
        $id = $this->get('request_stack')->getMasterRequest()->get($this->admin->getIdParameter());
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $object = $this->admin->getObject($id);

        if ($request->isMethod('POST')) {
            if ($request->request->get('delete')) {
                $object->setDeleted();
                $this->addFlash('sonata_flash_info', 'Заявка помечена как удаленная');
                $em->flush();
            }

            if ($request->request->get('reopen')) {
                $object->setDeleted(false);
                $this->addFlash('success', 'Заявка возвращена в обработку');
                $em->flush();
            }
        }

        return $this->render(
            $this->admin->getTemplate('show'),
            array(
                'object' => $object,
                'action' => 'show',
                'elements' => $this->admin->getShow(),
            )
        );
    }

    public function viewHistoryAction(Request $request)
    {
        $id = $request->attributes->get($this->admin->getIdParameter());

        $demand = $this->admin->getObject($id);
        /* @var $object Demand */

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $demandViews = $em->getRepository('MetalDemandsBundle:DemandView')
            ->createQueryBuilder('dv')
            ->join('dv.user', 'u')
            ->addSelect('u')
            ->leftJoin('u.company', 'c')
            ->addSelect('c')
            ->andWhere('dv.demand = :demand_id')
            ->setParameter('demand_id', $demand->getId())
            ->orderBy('dv.viewedAt', 'DESC')
            ->getQuery()
            ->getResult();
        /* @var $demandViews DemandView[]  */

        $usersIps = array();
        foreach ($demandViews as $demandView) {
            $usersIps[$demandView->getUser()->getId()] = array();
        }

        if ($usersIps) {
            $clientIpResults = $em->createQueryBuilder()
                ->select('clientIp')
                ->from('MetalProjectBundle:ClientIp', 'clientIp')
                ->where('clientIp.user IN (:userIds)')
                ->setParameter('userIds', array_keys($usersIps))
                ->orderBy('clientIp.createdAt', 'DESC')
                ->getQuery()
                ->getResult();
            /* @var $clientIpResults ClientIp[] */

            foreach ($clientIpResults as $clientIpResult) {
                $usersIps[$clientIpResult->getUser()->getId()][] = $clientIpResult;
            }
        }

        return $this->render(
            'MetalDemandsBundle:DemandAdmin:demandView.html.twig',
            array(
                'demand' => $demand,
                'demandViews' => $demandViews,
                'usersIps' => $usersIps,
                'action' => null,
                'object' => null,
            )
        );
    }

    public function simpleListAction()
    {
        $filters = array(
            '_sort_order' => 'DESC',
            '_sort_by' => 'createdAt',
            '_page' => 1,
            '_per_page' => 50,
            'private' => array('value' => AbstractDemand::TYPE_PUBLIC),
            'moderatedAt' => array(
                'value' => 'n',
            ),
            'deletedAt' => array(
                'value' => 'n',
            ),
        );

        if (false === $this->admin->isGranted('LIST')) {
            throw new AccessDeniedException();
        }

        $this->admin->setPersistFilters(false);
        $this->admin->setFilterParameters($filters);
        $this->admin->setTemplate('simple_list', 'MetalProjectBundle:Admin:simpleList.html.twig');

        $datagrid = $this->admin->getDatagrid();
        $formView = $datagrid->getForm()->createView();

        $this->get('twig')->getExtension('form')->renderer->setTheme($formView, $this->admin->getFilterTheme());

        return $this->render(
            $this->admin->getTemplate('simple_list'),
            array(
                'action' => 'list',
                'form' => $formView,
                'datagrid' => $datagrid,
                'csrf_token' => $this->getCsrfToken('sonata.batch'),
            )
        );
    }

    public function batchActionDeleteDemands(ProxyQueryInterface $selectedModelQuery)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $demands = $selectedModelQuery->execute();
        $demandsIds = array();
        $companies = array();
        foreach ($demands as $demand) {
            /* @var $demand AbstractDemand */
            $demand->setDeleted();
            $demand->setUpdatedBy($this->getUser());
            $demand->setUpdatedAt(new \DateTime());
            $demandsIds['removeIndex'][] = $demand->getId();

            if ($demand instanceof PrivateDemand) {
                $companies[$demand->getCompany()->getId()] = $demand->getCompany();
            }
        }

        $em->flush();

        $this->get('sonata.notification.backend')->createAndPublish('admin_demand', $demandsIds);

        $this->addFlash('sonata_flash_success', 'Заявки отмечены как удаленные');

        $em->getRepository('MetalCompaniesBundle:CompanyCounter')->updateViewDemandCounter($companies);

        $name = $this->getRequest()->request->get('from') ? 'simple_list' : 'list';

        $url = $this->admin->generateUrl($name, array('filter' => $this->admin->getFilterParameters()));

        return $this->redirect($url);
    }

    public function batchActionReDetectionCategory(ProxyQueryInterface $selectedModelQuery)
    {
        $em = $this->getDoctrine()->getManager();
        $categoryDetector = $this->container->get('metal.categories.category_matcher');
        /* @var $em EntityManager */
        $categoryRepository = $em->getRepository('MetalCategoriesBundle:Category');
        $demands = $selectedModelQuery->execute();
        /* @var $demand AbstractDemand */
        $demandItemsToChangeAttributesValues = array();
        foreach ($demands as $demand) {
            $demandItems = $demand->getDemandItems();
            $demandCategories = array();
            foreach ($demandItems as $demandItem) {

                $category = $categoryDetector->getCategoryByTitle($demandItem->getTitle());
                if (!isset($demandCategories[$category->getId()])) {
                    $demandCategories[$category->getId()] = 0;
                }

                $demandCategories[$category->getId()]++;
                $demandItem->setCategory($category);
                if (!$demandItem->getIsLockedAttributeValues()) {
                    $demandItemsToChangeAttributesValues[] = $demandItem->getId();
                }
            }

            arsort($demandCategories);
            $demand->setCategory($categoryRepository->find(current(array_keys($demandCategories))));
            $demand->updateCategories();
        }

        $this->addFlash('sonata_flash_success', 'Переопределены категории заявкам');
        $em->flush();

        $demandsChangeSet = new DemandsBatchEditChangeSet();
        $demandsChangeSet->demandItemsToChangeAttributesValues = $demandItemsToChangeAttributesValues;

        $this->container->get('sonata.notification.backend')
            ->createAndPublish('admin_demand', array('changeset' => $demandsChangeSet));

        return $this->redirect(
            $this->admin->generateUrl('list', array('filter' => $this->admin->getFilterParameters()))
        );
    }

    public function viewAllDemandsStatisticsAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        $demandsStatisticByAllTime = $em->getConnection()->fetchAll(
            '
            SELECT count(d.id) AS demandsCount, d.user_id AS userId, CONCAT_WS(" ", u.ForumName, u.LastName) AS fullName,
            YEAR(d.created_at) AS year, MONTH(d.created_at) AS month
            FROM demand d
            JOIN User u ON d.user_id = u.User_ID
                AND d.moderated_at IS NOT NULL
                AND d.source_type = :source_type
                AND u.additional_role_id > 0
            GROUP BY d.user_id, year, month
            ORDER BY year DESC, month DESC, fullName ASC
            ',
            array(
                'user_ids' => User::PERMISSION_GROUP_MODERATOR,
                'source_type' => SiteSourceTypeProvider::SOURCE_ADMIN,
            )
        );

        $formattingHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Formatting');
        /* @var $formattingHelper FormattingHelper */
        $structureDemandsStatisticByAllTime = array();
        foreach ($demandsStatisticByAllTime as $demandStatistic) {
            $date = new \DateTime('01-'.$demandStatistic['month'].'-'.$demandStatistic['year']);
            $dateFrom = clone $date;
            $dateTo = $date->modify('last day of this month')->modify('+1 day');
            $demandStatistic['month'] = $formattingHelper->getMonthLocalized($dateFrom, 'normal');
            $demandStatistic['dateFrom'] = $dateFrom->format('d.m.Y');
            $demandStatistic['dateTo'] = $dateTo->format('d.m.Y');
            $structureDemandsStatisticByAllTime[$demandStatistic['year']][$demandStatistic['month']][] = $demandStatistic;
        }

        return $this->render(
            'MetalDemandsBundle:DemandAdmin:showAllDemandsCreateStatistic.html.twig',
            array(
                'action' => null,
                'object' => null,
                'demandsStatisticByAllTime' => $structureDemandsStatisticByAllTime,
            )
        );
    }

    public function redirectTo($object)
    {
        $response = parent::redirectTo($object);
        $request = $this->admin->getRequest();
        $uniqId = $request->request->get($request->query->get('uniqid'));

        if (isset($uniqId['from']) && $uniqId['from'] && $request->get('btn_update_and_list') !== null) {
            return $this->redirect($this->admin->generateUrl('simple_list'));
        }

        if ($object instanceof AbstractDemand) {
            $response->setTargetUrl($response->getTargetUrl().'#scroll-to-'.$object->getId());
        }

        return $response;
    }

    public function copyDemandAction(Request $request)
    {
        $id = $request->attributes->get($this->admin->getIdParameter());
        $demand = $this->admin->getObject($id);
        /* @var $demand AbstractDemand */

        if (!$this->isCsrfTokenValid('copy_demand', $request->query->get('_csrf'))) {
            $this->addFlash('error', 'Ошибка CSRF. Обновите страницу и попробуйте снова.');

            return $this->redirect($this->admin->generateUrl('edit', array('id' => $demand->getId())));
        }

        $em = $this->getDoctrine()->getManager();
        /* @var $em EntityManager */

        if ($demand instanceof PrivateDemand) {
            $this->addFlash('error', 'Можно копировать только публичные заявки.');

            return $this->redirect($this->admin->generateUrl('edit', array('id' => $demand->getId())));
        }

        $copier = new DeepCopy();
        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(AbstractDemand::class, 'id'));
        $copier->addFilter(new KeepFilter(), new PropertyMatcher(AbstractDemand::class, 'user'));
        $copier->addFilter(new KeepFilter(), new PropertyMatcher(AbstractDemand::class, 'updatedBy'));
        $copier->addFilter(new KeepFilter(), new PropertyMatcher(AbstractDemand::class, 'city'));
        $copier->addFilter(new KeepFilter(), new PropertyMatcher(AbstractDemand::class, 'country'));
        $copier->addFilter(new KeepFilter(), new PropertyMatcher(AbstractDemand::class, 'possibleUser'));
        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(AbstractDemand::class, 'category'));
        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(AbstractDemand::class, 'fromDemand'));
        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(AbstractDemand::class, 'fromCallback'));
        $copier->addFilter(new SetNullFilter(), new PropertyMatcher(AbstractDemand::class, 'parsedDemand'));

        $copier->addFilter(
            new DoctrineEmptyCollectionFilter(),
            new PropertyMatcher(AbstractDemand::class, 'demandItems')
        );
        $copier->addFilter(
            new DoctrineEmptyCollectionFilter(),
            new PropertyMatcher(AbstractDemand::class, 'demandCategories')
        );
        $copier->addFilter(
            new DoctrineEmptyCollectionFilter(),
            new PropertyMatcher(AbstractDemand::class, 'demandFiles')
        );

        $copiedDemand = $this->container->get('metal.demands.management_services')->copyDemand($demand);

        /* @var $copiedDemand Demand */
        $copiedDemand->setUser($this->getUser());
        $copiedDemand->setDeleted(false);
        $copiedDemand->setModerated(false);
        $copiedDemand->setCreatedAt(new \DateTime());
        $copiedDemand->setUpdatedAt(new \DateTime());
        $copiedDemand->setFakeUpdatedAt(new \DateTime());

        $em->persist($copiedDemand);
        $em->flush($copiedDemand);

        $this->addFlash('success', 'Заявка скопирована. Позиции и информация о модерации обнулены.');

        return $this->redirect($this->admin->generateUrl('edit', array('id' => $copiedDemand->getId())));
    }
}
