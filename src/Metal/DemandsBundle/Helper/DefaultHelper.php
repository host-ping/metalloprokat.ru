<?php

namespace Metal\DemandsBundle\Helper;

use Brouzie\Bundle\HelpersBundle\Helper\HelperAbstract;

use Metal\CategoriesBundle\Entity\Category;
use Metal\DemandsBundle\Entity\Demand;
use Metal\DemandsBundle\Entity\ValueObject\ConsumerTypeProvider;
use Metal\ProjectBundle\Helper\UrlHelper;
use Metal\TerritorialBundle\Entity\Country;
use Metal\UsersBundle\Entity\User;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class DefaultHelper extends HelperAbstract
{
    /**
     * @var Category
     */
    private $currentCategory;

    private $currentSubdomain;

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);

        $request = $this->container->get('request_stack')->getMasterRequest();
        $this->currentCategory = $request->attributes->get('category');
        $this->currentSubdomain = $request->attributes->get('subdomain') ?: 'www';
    }

    public function getBestDemandItemForCategory(Demand $similarDemand, Category $preferredCategory)
    {
        $items = $similarDemand->getAttribute('demandItems');
        if ($items === null) {
            $items = $similarDemand->getDemandItems();
        }

        foreach ($items as $demandItem) {
            if ($demandItem->getCategoryId() == $preferredCategory->getId()) {
                return $demandItem;
            }
        }

        return reset($items);
    }

    public function generateDemandUrlOnCurrentSubdomain(Demand $demand, $absolute = false)
    {
        return $this->generateDemandUrl($demand, array('subdomain' => $this->currentSubdomain), $absolute);
    }

    public function generateDemandUrl(Demand $demand, array $parameters = array(), $absolute = false)
    {
        $parameters['id'] = $demand->getId();
        $parameters['category_slug'] = $this->getCategorySlug($demand);

        if (empty($parameters['subdomain'])) {
            $parameters['subdomain'] = $demand->getCity() ? $demand->getCity()->getSlugWithFallback() : $this->currentSubdomain;
        }

        if ($demand->getCountry() && $demand->getCountry()->getId() !== Country::COUNTRY_ID_RUSSIA) {
            $parameters['base_host'] = $demand->getCountry()->getBaseHost();
        }

        if ($city = $demand->getCity()) {
            $parameters['_secure'] = $city->getCountry()->getSecure();
        }

        $urlHelper = $this->container->get('brouzie.helper_factory')->get('MetalProjectBundle:Url');
        /* @var $urlHelper UrlHelper */

        return $urlHelper->generateUrl('MetalDemandsBundle:Demand:view', $parameters, $absolute);
    }

    protected function getCategorySlug(Demand $demand)
    {
        $categorySlug = '';
        if ($this->currentCategory) {
            $categories = $demand->getAttribute('categories');
            if ($categories === null) {
                $categories = $demand->getCategories();
            }

            foreach ($categories as $category) {
                if ($category->isChildOf($this->currentCategory)) {
                    $categorySlug = $category->getSlugCombined();
                    break;
                }
            }
        }

        if (!$categorySlug) {
            $categorySlug = $demand->getCategory()->getSlugCombined();
        }

        return $categorySlug;
    }

    public function trackDemandView(Demand $demand, User $user)
    {
        $em = $this->container->get('doctrine.orm.default_entity_manager');
        $demandViewRepository = $em->getRepository('MetalDemandsBundle:DemandView');

        if ($demandViewRepository->addView($demand, $user, $this->getRequest()->getClientIp())) {
            $sphinxy = $this->container->get('sphinxy.default_connection');
            $sphinxy->createQueryBuilder()
                ->update('demands')
                ->set('demand_views_count', $demand->getViewsCount() + 1)
                ->andWhere('id = :id')
                ->setParameter('id', $demand->getId())
                ->execute();
        }
    }

    public function prepareCriteriaForRequest(Request $request)
    {
        $query = $request->query;

        $city = $request->attributes->get('city');
        $region = $request->attributes->get('region');
        $country = $request->attributes->get('country');
        $category = $request->attributes->get('category');
        $category = $category ? $category->getId() : $query->get('category');

        $demandWholesale = $query->get('wholesale');
        $demandConsumerType= $query->get('consumers');
        $demandPeriodicity = $query->get('periodicity');

        $specification = array();
        if ($category) {
            $specification['categories_ids'] = $category;
        }
        if ($city) {
            $specification['city_id'] = $city->getId();
        } elseif ($region) {
            $specification['region_id'] = $region->getId();
        } else {
            //FIXME: тут по идее должна быть countries_ids
            $specification['country_id'] = $country->getId();
        }

        if ($demandPeriodicity && $demandPeriodicity !== 'all') {
            $specification['is_repetitive'] = $demandPeriodicity === 'permanent';
        }

        if ($demandWholesale && $demandWholesale != 0) {
            $specification['is_wholesale'] = $demandWholesale == '1';
        }

        if ($demandConsumerType === 'consumer') {
            $specification['author_type'] = ConsumerTypeProvider::CONSUMER;
        } elseif ($demandConsumerType === 'trader') {
            $specification['author_type'] = ConsumerTypeProvider::TRADER;
        }
        $periodFrom = null;
        $periodTo = null;

        list($periodFrom, $periodTo) = self::determinatePeriod($request);

        if ($periodFrom) {
            # $date = \DateTime::createFromFormat('d.m.Y', $periodFrom);
            $specification['date_from'] = $periodFrom->getTimestamp();
        }
        if ($periodTo) {
//            $date = \DateTime::createFromFormat('d.m.Y', $periodTo);
            $specification['date_to'] = $periodTo->getTimestamp();
        }
        $period = $query->get('period');
        switch ($period) {
            case 'day':
                $specification['date_from'] = strtotime('-1 day');
                break;
            case 'week':
                $specification['date_from'] = strtotime('-7 day');
                break;
            case 'month':
                $specification['date_from'] = strtotime('-1 month');
                break;
            case 'year':
                $specification['date_from'] = strtotime('-1 year');
                break;
            case 'all':
                break;
        }

        if ($query->get('q')) {
            $specification['match_title'] = $query->get('q');
        }

        if ($query->get('id')) {
            $criteria['id'] = $query->get('id');
        }

        $orderBy = array();
        $order = $query->get('order');
        if ($order === 'popularity') {
            $orderBy['demand_views_count'] = 'DESC';
        } else {
            $orderBy['created_at'] = 'DESC';
        }

        return array($specification, $orderBy);
    }

    /**
     * @param Request $request
     *
     * @return \DateTime[]
     */
    public static function determinatePeriod(Request $request)
    {
        $dateFrom = null;
        $dateTo = null;
        $dateFromStr = $request->query->get('date_from');
        $dateToStr = $request->query->get('date_to');

        switch ($request->query->get('period', 'all')) {
            case 'day':
                $dateFrom = new \DateTime('today');
                $dateTo = new \DateTime();
                break;

            case 'week':
                $dateFrom = new \DateTime('-1 week');
                $dateTo = new \DateTime();
                break;

            case 'month':
                $dateFrom = new \DateTime('-1 month');
                $dateTo = new \DateTime();
                break;

            case 'year':
                $dateFrom = new \DateTime('-1 year');
                $dateTo = new \DateTime();
                break;

            case 'all':
            default:
                break;
        }

        if ($dateFromStr) {
            $dateFrom = \DateTime::createFromFormat('d.m.Y', $dateFromStr);
        }

        if ($dateToStr) {
            $dateTo = \DateTime::createFromFormat('d.m.Y', $dateToStr);
        }

        if ($dateFrom instanceof \DateTime) {
            $dateFrom->modify('00:00:00');
        }

        if ($dateTo instanceof \DateTime) {
            $dateTo->modify('23:59:59');
        }

        return array($dateFrom, $dateTo);
    }

    public function getFilterParametersForCitiesList(Request $request)
    {
        $requestBag = $request->request;
        $whiteListParameters = array('periodicity', 'wholesale', 'consumers', 'category_slug');

        $params = array();
        $filterParameters = $requestBag->get('filter_parameters');
        foreach ($whiteListParameters as $param) {
            if (isset($filterParameters[$param])) {
                $params[$param] = $filterParameters[$param];
            }
        }

        if ($requestBag->has('category_slug')) {
            $params['category_slug'] = $requestBag->get('category_slug');
        }

        return $params;
    }

    public function getFakePerson()
    {
        $persons = ['Марина Ф',
            'Мария Л',
            'Татьяна Б',
            'Наталья С',
            'Петр Феликсович',
            'Андрей Дмитриевич',
            'Бояна Морозова',
            'Влад',
            'Секретарь отдела продаж',
            'Виолетта',
            'Генадии Валерьевич',
            'Святослав',
            'Азат',
            'Назир',
            'Уфук',
            'Pavel',
            'Кеосян',
            'Тоско'
        ];

        return $persons[array_rand($persons)];
    }

    public function screwPhone($phone)
    {
        $phone = preg_replace('/\D*/', '', $phone);
        return '+'.preg_replace('/\d\d$/',rand(0,9).rand(0,9), $phone);
    }

    public function screwEmail($email)
    {
        $part1 = preg_replace('/^(.*)@(.*)/', '$1', $email);
        $part2 = preg_replace('/^(.*)@(.*)/', '@$2', $email);
        return $part1.rand(0,9).chr(65 + rand(0, 25)).$part2;
    }
}
