<?php

namespace Metal\PrivateOfficeBundle\Widget;

use Brouzie\Bundle\WidgetsBundle\Widget\WidgetAbstract;
use Brouzie\WidgetsBundle\Widget\ConditionallyRenderedWidget;
use Metal\ProductsBundle\Entity\Product;
use Metal\UsersBundle\Entity\User;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductsWithoutCategoryNoticeWidget extends WidgetAbstract implements ConditionallyRenderedWidget
{
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefaults(
                array(
                    'check_custom_category' => false,
                )
            );
    }

    public function getParametersToRender()
    {
        $tokenStorage = $this->container->get('security.token_storage');
        $user = $tokenStorage->getToken()->getUser();
        /* @var $user User */

        $em = $this->container->get('doctrine.orm.default_entity_manager');
        $productRepository = $em->getRepository('MetalProductsBundle:Product');

        $defaultCategory = null;
        if ($this->options['check_custom_category']) {
            $productsCount = $productRepository->getProductsCountInCategory($user->getCompany(), array('customCategory' => null));
        } else {
            $categoryDetector = $this->container->get('metal.categories.category_matcher');
            $defaultCategory = $em->getRepository('MetalCategoriesBundle:Category')->find($categoryDetector::DEFAULT_CATEGORY_ID);
            $productsCount = $productRepository->getProductsCountInCategory($user->getCompany(), array('category' => $defaultCategory));
        }

        return array(
            'productsCount' => $productsCount,
            'defaultCategory' => $defaultCategory
        );
    }

    public function shouldBeRendered()
    {
        $authorizationChecker = $this->container->get('security.authorization_checker');

        $isGranted = $authorizationChecker->isGranted('ROLE_SUPPLIER') && $authorizationChecker->isGranted('ROLE_APPROVED_USER');
        if ($this->options['check_custom_category']) {
            $tokenStorage = $this->container->get('security.token_storage');
            $user = $tokenStorage->getToken()->getUser();
            /* @var $user User */

            return $isGranted && $user->getCompany()->getMinisiteConfig()->getHasCustomCategory();
        }

        return $isGranted;
    }
}
