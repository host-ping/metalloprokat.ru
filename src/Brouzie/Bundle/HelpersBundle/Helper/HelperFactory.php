<?php

namespace Brouzie\Bundle\HelpersBundle\Helper;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class HelperFactory
{
    protected $kernel;

    protected $helpersCollection = array();

    /**
     *
     * @param KernelInterface $kernel
     */
    public function __construct(KernelInterface $kernel)
    {
        $this->kernel = $kernel;
    }

    /**
     *
     * @param string $name NameOfBundle:HelperName
     *
     * @return object
     */
    public function get($name)
    {
        if (!strpos($name, ':')) {
            $name .= ':Default';
        }

        if (isset($this->helpersCollection[$name])) {
            return $this->helpersCollection[$name];
        }

        list($bundle, $helper) = explode(':', $name);

        $helperClass = $this->kernel->getBundle($bundle)->getNamespace() . '\\Helper\\' . $helper . 'Helper';
        $helperObj = new $helperClass();

        if ($helperObj instanceof ContainerAwareInterface) {
            $helperObj->setContainer($this->kernel->getContainer());
        }

        return $this->helpersCollection[$name] = $helperObj;
    }
}
