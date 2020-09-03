<?php

namespace Metal\ProjectBundle\Twig;

use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class HttpExtension extends AbstractExtension implements GlobalsInterface
{
    private $hostnamesMap;

    private $baseHost;

    public function __construct(array $hostnamesMap, string $baseHost)
    {
        $this->hostnamesMap = $hostnamesMap;
        $this->baseHost = $baseHost;
    }

    public function getGlobals()
    {
        return array(
            'http_prefix' => $this->hostnamesMap[$this->baseHost]['host_prefix'],
        );
    }
}
