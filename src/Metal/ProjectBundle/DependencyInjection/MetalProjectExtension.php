<?php

namespace Metal\ProjectBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\DelegatingLoader;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class MetalProjectExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('cache.xml');
        $loader->load('listeners.xml');
        $loader->load('services.xml');
        $loader->load('twig.xml');
        $loader->load('routing.xml');
        $loader->load('widgets.xml');

        $container
            ->getDefinition('metal_project.routing.routing_parser')
            ->replaceArgument(1, new Reference($config['routing_parser_cache_provider']));

        $container
            ->getDefinition('metal_project.cache.adapter.tag_aware')
            ->replaceArgument(0, new Reference($config['cache_app_service']));

        if ($container->hasParameter('minisite_hostnames_pattern')) {
            $container
                ->getDefinition('metal.project.page_not_found_listener')
                ->replaceArgument(2, '%minisite_hostnames_pattern%')
                ->addTag(
                    'kernel.event_listener',
                    array('event' => 'kernel.exception', 'method' => 'onKernelException', 'priority' => 39)
                );
        }

        if ($listenersFile = $container->getParameter('listeners_declaration_file')) {
            $locator = new FileLocator($container->getParameter('kernel.root_dir').'/config');
            $resolver = new LoaderResolver(
                array(
                    new Loader\XmlFileLoader($container, $locator),
                    new Loader\YamlFileLoader($container, $locator),
                )
            );

            $loader = new DelegatingLoader($resolver, $locator);
            $loader->load($listenersFile);
        }
    }
}
