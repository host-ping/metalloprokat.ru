<?php

namespace Metal\ProjectBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CreateSecondRouterCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $secondaryRouterDefinition = clone $container->getDefinition('router.default');
        $secondaryRouterDefinition->replaceArgument(1, '%kernel.root_dir%/config/routing/routing_%hostname_package_routing%_secondary.yml');

        $options = $secondaryRouterDefinition->getArgument(2);
        $options['generator_cache_class'] = '%router.cache_class_prefix%SecondaryUrlGenerator';
        $options['matcher_cache_class'] = '%router.cache_class_prefix%SecondaryUrlMatcher';
        $secondaryRouterDefinition->replaceArgument(2, $options);

        $container->setDefinition('router.secondary', $secondaryRouterDefinition);
    }
}
