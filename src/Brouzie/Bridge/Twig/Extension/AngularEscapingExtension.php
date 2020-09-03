<?php

namespace Brouzie\Bridge\Twig\Extension;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\CoreExtension;
use Twig\Extension\InitRuntimeInterface;

class AngularEscapingExtension extends AbstractExtension implements InitRuntimeInterface
{
    public function initRuntime(\Twig_Environment $environment)
    {
        $environment->getExtension(CoreExtension::class)->setEscaper('angular', array($this, 'angularEscaping'));
    }

    public function angularEscaping(Environment $env, $string, $charset)
    {
        $string = str_replace("'", "\\'", $string);

        //TODO: сейчас вынуждены добавлять еще и raw в twig, хотелось бы от этого избавиться https://github.com/twigphp/Twig/issues/1689
        return twig_escape_filter($env, $string, 'html_attr', $charset);
    }
}
