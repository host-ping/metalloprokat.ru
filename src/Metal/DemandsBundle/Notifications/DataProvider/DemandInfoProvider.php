<?php

namespace Metal\DemandsBundle\Notifications\DataProvider;

use Doctrine\ORM\EntityManagerInterface;
use Metal\DemandsBundle\Entity\AbstractDemand;
use Metal\DemandsBundle\Notifications\Model\DemandInfo;
use Metal\DemandsBundle\Notifications\Model\DemandInfoCategory;
use Metal\DemandsBundle\Notifications\Model\DemandInfoItem;
use Metal\TerritorialBundle\Entity\City;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DemandInfoProvider
{
    private $em;

    private $urlGenerator;

    private $from;

    public function __construct(EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, string $from)
    {
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
        $this->from = $from;
    }

    public function get(int $demandId): DemandInfo
    {
        $demand = $this->em->getRepository(AbstractDemand::class)->find($demandId);

        /** @var City $city */
        $city = $demand->getCity();
        $subdomain = $city->getSlugWithFallback() ?: 'www';
        $categorySlug = $demand->getCategory()->getSlugCombined();
        $viewUrl = $this->getViewUrl($demandId, $categorySlug, $subdomain);

        $regionTitle = null;
        if ($city->getRegion()) {
            $regionTitle = $city->getRegion()->getTitle();
        }

        return new DemandInfo(
            $demandId,
            $city->getTitle(),
            $regionTitle,
            $viewUrl,
            $demand->isPublic(),
            $demand->getDemandPeriodicity(),
            $demand->getConsumerType(),
            $this->getDemandItems($demand),
            $this->getDemandCategories($demand)
        );
    }

    /**
     * @return DemandInfoItem[]
     */
    private function getDemandItems(AbstractDemand $demand): array
    {
        $items = [];
        $position = 1;

        foreach ($demand->getDemandItems() as $demandItem) {
            $items[] = new DemandInfoItem(
                $position,
                $demandItem->getTitle(),
                $demandItem->getVolume(),
                $demandItem->getVolumeType()
            );

            $position++;
        }

        return $items;
    }

    /**
     * @return DemandInfoCategory[]
     */
    private function getDemandCategories(AbstractDemand $demand): array
    {
        $categories = new \SplObjectStorage();
        foreach ($demand->getCategories() as $category) {
            $categories->attach($category);
            if ($category->getParent()) {
                $categories->attach($category->getParent());
            }
        }

        $demandCategories = [];
        foreach ($categories as $category) {
            $demandCategories[] = new DemandInfoCategory(
                $category->getId(),
                $category->getTitle()
            );
        }

        return $demandCategories;
    }

    private function getViewUrl(int $id, string $categorySlug, string $subdomain): string
    {
        return $this->urlGenerator->generate(
            'MetalDemandsBundle:Demand:view',
            [
                'id' => $id,
                'category_slug' => $categorySlug,
                'subdomain' => $subdomain,
                'from' => $this->from,
            ],
            UrlGeneratorInterface::ABSOLUTE_URL
        );
    }
}
