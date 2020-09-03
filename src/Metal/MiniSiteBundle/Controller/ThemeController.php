<?php

namespace Metal\MiniSiteBundle\Controller;

use Metal\CompaniesBundle\Entity\Company;
use Metal\MiniSiteBundle\Entity\MiniSiteConfig;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class ThemeController extends Controller
{
    public function renderCssAction(Company $company)
    {
        $minisiteConfig = $company->getMinisiteConfig();
        $miniSiteColorsHelper = $this->container->get('brouzie.helper_factory')->get('MetalMiniSiteBundle:ValueObject');
        $backgroundColors = $miniSiteColorsHelper->getAllBackgroundColors();
        $colors = $backgroundColors[$minisiteConfig->getBackgroundColor()];
        $colors['primary'] = $minisiteConfig->getPrimaryColor();
        $colors['secondary'] = $minisiteConfig->getSecondaryColor();
        $colors['background'] = $minisiteConfig->getBackgroundColor();

        $content = $this->get('metal.mini_site.service.mini_site_css_compiler')->compileCss($colors, $minisiteConfig->getCompany()->getId());
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/css');

        return $response;
    }

    public function renderDefaultCssAction()
    {
        $miniSiteColorsHelper = $this->container->get('brouzie.helper_factory')->get('MetalMiniSiteBundle:ValueObject');
        $colors = $miniSiteColorsHelper->getDefaultColors();
        $colors['primary'] = MiniSiteConfig::DEFAULT_PRIMARY_COLOR;
        $colors['secondary'] = MiniSiteConfig::DEFAULT_SECONDARY_COLOR;
        $colors['background'] = MiniSiteConfig::DEFAULT_BACKGROUND_COLOR;

        $content = $this->get('metal.mini_site.service.mini_site_css_compiler')->compileCss($colors, 'default');
        $response = new Response($content);
        $response->headers->set('Content-Type', 'text/css');

        return $response;
    }
}
