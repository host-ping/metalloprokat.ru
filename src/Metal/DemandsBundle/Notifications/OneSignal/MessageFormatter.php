<?php

namespace Metal\DemandsBundle\Notifications\OneSignal;

use Metal\DemandsBundle\Notifications\Model\DemandInfo;

class MessageFormatter
{
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    public function getMessage(DemandInfo $demandInfo): string
    {
        return $this->twig->render(
            '@MetalDemands/notifications/onesignal/new_demand.md.twig',
            [
                'demandInfo' => $demandInfo
            ]
        );
    }

    public function getTitle(DemandInfo $demandInfo): string
    {
        return 'Заявка на покупку #'.$demandInfo->getId().' ('.$demandInfo->getCityTitle().')';
    }

    public function getCategoryFilters(DemandInfo $demandInfo): array
    {
        $filters = array();
        $categories = $demandInfo->getCategories();
        foreach ($categories as $key => $val) {
            $filters[] = array("field" => "tag", "key" => "category", "relation" => "=", "value" => $val->getId());
            if ($key < count($categories) - 1) {
                $filters[] = array("operator" => "OR");
            }
        }

        return $filters;
    }
}
