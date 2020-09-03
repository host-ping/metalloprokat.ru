<?php

require_once __DIR__.'/AppKernel.php';

use Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache;

class AppCache extends HttpCache
{
    protected function getOptions()
    {
        return array(
            'default_ttl'            => 0,
            //'private_headers'        => array('Authorization'),
            //'allow_reload'           => false,
            //'allow_revalidate'       => false,
            //'stale_while_revalidate' => 2,
            //'stale_if_error'         => 60,
        );
    }
}
