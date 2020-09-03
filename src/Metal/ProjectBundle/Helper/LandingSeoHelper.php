<?php

namespace Metal\ProjectBundle\Helper;

use Metal\CategoriesBundle\Entity\LandingPage;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LandingSeoHelper extends SeoHelper
{
    /**
     * @var \Metal\ProductsBundle\Helper\ProductsListSeoHelper
     */
    protected $categorySeoHelper;

    public function setContainer(ContainerInterface $container = null)
    {
        parent::setContainer($container);

        $this->categorySeoHelper = $container->get('brouzie.helper_factory')->get('MetalProductsBundle:ProductsListSeo');
    }

    public function getMetaTitleForLandingPages()
    {
        $template = 'Популярные категории в {{ territory_locative }} — {{ domain_title }}';

        return $this->renderStringTemplate($template);
    }

    public function getMetaTitleForLandingPage(LandingPage $landingPage)
    {
        $page = $this->getRequest()->query->get('page');

        if ($metaTitle = $landingPage->getMetadata()->getTitle()) {
            if ($page) {
                $metaTitle .= ' — Страница '.$page;
            }

            return $metaTitle;
        }

        return $this->categorySeoHelper->getMetaTitleForCategoryPage($landingPage->getFakeCategory());
    }

    public function getMetaDescriptionForLandingPage(LandingPage $landingPage)
    {
        $page = $this->getRequest()->query->get('page');
        if ($page) {
            return null;
        }

        if ($metaDescription = $landingPage->getMetadata()->getDescription()) {
            return $metaDescription;
        }

        return $this->categorySeoHelper->getMetaDescriptionForCategoryPage($landingPage->getFakeCategory());
    }

    public function getHeadTitleForLandingPage(LandingPage $landingPage)
    {
        if ($h1Title = $landingPage->getMetadata()->getH1Title()) {
            return $h1Title;
        }

        return $this->categorySeoHelper->getHeadTitleForCategoryPage($landingPage->getFakeCategory());
    }
}
