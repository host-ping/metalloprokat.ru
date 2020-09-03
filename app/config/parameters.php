<?php

/** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
$container->setParameter('project.logo', $container->getParameter('project.logos')['default']);
