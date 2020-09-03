<?php

namespace Metal\TerritorialBundle\Widget;

use Brouzie\WidgetsBundle\Cache\CacheProfile;
use Brouzie\WidgetsBundle\Widget\CacheableWidget;
use Brouzie\WidgetsBundle\Widget\TwigWidget;
use Metal\ProjectBundle\DataFetching\DataFetcher;
use Metal\TerritorialBundle\Entity\Country;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CountriesWidget extends TwigWidget implements ContainerAwareInterface, CacheableWidget
{
    use ContainerAwareTrait;

    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setRequired('country')
            ->setAllowedTypes('country', Country::class);
    }

    public function getContext()
    {
        return array(
            'countries' => $this->container
                ->get('doctrine.orm.default_entity_manager')
                ->getRepository('MetalTerritorialBundle:Country')
                ->getEnabledCountries(),
        );
    }

    public function getCacheProfile()
    {
        return new CacheProfile(
            array(
                'country_id' => $this->options['country']->getId(),
            ),
            DataFetcher::TTL_INFINITY
        );
    }
}
