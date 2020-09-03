<?php

namespace Metal\GrabbersBundle\DependencyInjection;

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
class MetalGrabbersExtension extends Extension
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

        $grabbers = array();
        foreach ($container->findTaggedServiceIds('metal.demands_grabber') as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                if ($attributes['project_family'] == $container->getParameter('project.family')) {
                    $grabbers[] = new Reference($id);
                }
            }
        }
        $container->findDefinition('metal.grabbers.graber_manager')->replaceArgument(0, $grabbers);
    }
}
