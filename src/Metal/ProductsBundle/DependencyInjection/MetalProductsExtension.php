<?php

namespace Metal\ProductsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MetalProductsExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
        $loader->load('indexer.xml');
        $loader->load('widgets.xml');

        if ($container->getParameter('kernel.debug')) {
            $container
                ->getDefinition('metal_products.indexer.traceable_products_provider')
                ->setDecoratedService('metal_products.indexer.products_provider')
                ->replaceArgument(0, new Reference('metal_products.indexer.traceable_products_provider.inner'));
        }
    }
}
