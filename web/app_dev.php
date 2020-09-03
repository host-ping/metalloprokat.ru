<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

register_shutdown_function(function() {
    $e = error_get_last();

//    $e && var_dump($e);
});

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.

if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1', '192.168.56.1', '192.168.56.101', '10.0.75.1'))
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file. Check '.basename(__FILE__).' for more information.');
}

if (function_exists('pinba_script_name_set')) {
    pinba_script_name_set($_SERVER['REQUEST_URI']);
}
if (function_exists('pinba_hostname_set')) {
    pinba_hostname_set($_SERVER['HTTP_HOST']);
}

$loader = require_once __DIR__.'/../app/autoload.php';
Debug::enable();

if (!isset($hostnamePackage)) {
    $hostnamePackage = 'metalloprokat';
    if (false !== strpos($_SERVER['HTTP_HOST'], 'metalloprokat-ua.dev')) {
        $hostnamePackage = 'metalloprokat-ua';
    }

    if (false !== strpos($_SERVER['HTTP_HOST'], 'product-ua.dev')) {
        $hostnamePackage = 'metalloprokat-ua';
    }

    if (false !== strpos($_SERVER['HTTP_HOST'], 'product-by.dev')) {
        $hostnamePackage = 'metalloprokat-by';
    }
    if (false !== strpos($_SERVER['HTTP_HOST'], 'metalspros.dev')) {
        $hostnamePackage = 'metalspros';
    }
    if (false !== strpos($_SERVER['HTTP_HOST'], '8-800-555-56-65.dev')) {
        $hostnamePackage = '8_800';
    }
}

$kernel = new AppKernel('dev', true, $hostnamePackage);
//$kernel = new AppCache($kernel);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
// workaround due to bug https://github.com/symfony/symfony/pull/10517
if (false !== $response->getContent()) {
    $response->headers->add(array(
            'Connection' => 'close',
            'Content-Length' => strlen($response->getContent()),
        ));
}
$response->send();
$kernel->terminate($request, $response);
