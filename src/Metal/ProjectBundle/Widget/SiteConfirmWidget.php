<?php

namespace Metal\ProjectBundle\Widget;

use Brouzie\WidgetsBundle\Widget\ContentWidget;
use Metal\ProjectBundle\Entity\Site;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

class SiteConfirmWidget extends ContentWidget implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public function getContent()
    {
        $html = '';
        $site = $this->container
            ->get('doctrine')
            ->getManager()
            ->getRepository('MetalProjectBundle:Site')
            ->findOneBy(array('hostname' => $this->container->get('request_stack')->getMasterRequest()->getHost()));
        /* @var $site Site */

        if ($site && $site->getYandexCode()) {
            $html .= sprintf('<meta name="yandex-verification" content="%s" />', $site->getYandexCode());
        }

        if ($site && $site->getGoogleCode()) {
            $html .= sprintf('<meta name="google-site-verification" content="%s" />', $site->getGoogleCode());
        }

        return $html;
    }
}
