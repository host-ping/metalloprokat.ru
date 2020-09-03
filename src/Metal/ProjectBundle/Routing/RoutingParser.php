<?php

namespace Metal\ProjectBundle\Routing;

use Doctrine\Common\Cache\Cache;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouterInterface;

class RoutingParser
{
    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(RouterInterface $router, Cache $cache)
    {
        $this->cache = $cache;
        $this->router = $router;
    }

    /**
     * @param $routeName
     *
     * @return array
     */
    public function getVariablesForRoute($routeName)
    {
        $variables = $this->cache->fetch($routeName);

        if (is_array($variables)) {
            return $variables;
        }

        $route = $this->router->getRouteCollection()->get($routeName);
        if (!$route instanceof Route) {
            throw new \InvalidArgumentException('Wrong route name');
        }

        $variables = $route->compile()->getVariables();
        $this->cache->save($routeName, $variables);

        return $variables;
    }
}
