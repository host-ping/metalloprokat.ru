<?php

namespace Spros\ProjectBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SitemapController extends Controller
{
    public function getSitemapAction($subdomain, $_format, $section = null)
    {
        $hostname = $this->container->getParameter('kernel.hostname_package');
        $file = $this->container->getParameter('kernel.root_dir').'/..';
        $file .= '/web/sitemaps/'.$hostname.'/'.$subdomain.'/';

        if ($section) {
            $file .= 'sitemap.'.$section.'.'.$_format;
        } else {
            $file .= 'sitemap.xml';
        }

        if (!is_file($file)) {
            throw $this->createNotFoundException(sprintf('Sitemap file "%s" not found', basename($file)));
        }

        return new BinaryFileResponse($file);
    }
}
