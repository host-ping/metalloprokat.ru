<?php

namespace Brouzie\Bundle\HelpersBundle\Twig\Extension;

use Brouzie\Bundle\HelpersBundle\Helper\HelperFactory;

class HelperFactoryExtension extends \Twig_Extension
{
    protected $helperFactory;

    public function __construct(HelperFactory $helperFactory)
    {
        $this->helperFactory = $helperFactory;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('brouzie_helper', array($this, 'getHelper')),
        );
    }

    public function getHelper($name)
    {
        return $this->helperFactory->get($name);
    }
}
