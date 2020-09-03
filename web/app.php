<?php

use Symfony\Component\ClassLoader\ApcClassLoader;
use Symfony\Component\HttpFoundation\Request;

$time = microtime(true);
$extraCookies = array(
    'profiler_env' => 'ioniatte2',
    'debug_mode' => 'gabryoka2',
    'shutdown_function' => 'ousterra2',
);
$cookie = @$_COOKIE['debug_key'];

if (false !== stripos($_SERVER['HTTP_USER_AGENT'], 'bingbot')) {
    die('bingbot');
}

if (false !== strpos($cookie, $extraCookies['shutdown_function'])) {
    register_shutdown_function(function () {
        $e = error_get_last();

        if ($e && $e['type'] != E_USER_DEPRECATED) {
            var_dump($e);
        }
    });
}

if (function_exists('pinba_script_name_set')) {
    pinba_script_name_set($_SERVER['REQUEST_URI']);
}
//if (function_exists('pinba_server_name_set')) {
//    pinba_server_name_set($_SERVER['HTTP_HOST']);
//}
if (function_exists('pinba_hostname_set')) {
    $pinbaServerName = 'unknown';
    switch (true) {
        case false !== strpos($_SERVER['HTTP_HOST'], 'metalloprokat.ru'):
        case false !== strpos($_SERVER['HTTP_HOST'], 'metalloprokat.net.ua'):
        case false !== strpos($_SERVER['HTTP_HOST'], 'metalaprakat.by'):
        case false !== strpos($_SERVER['HTTP_HOST'], 'metalloprokat.kz'):
        case false !== strpos($_SERVER['HTTP_HOST'], 'me1.ru'):
        case false !== strpos($_SERVER['HTTP_HOST'], 'metaltop.ru'):
            $pinbaServerName = '+metalloprokat.prod';
            break;

        case false !== strpos($_SERVER['HTTP_HOST'], 'metallspros.ru'):
        case false !== strpos($_SERVER['HTTP_HOST'], 'metalspros.ru'):
            $pinbaServerName = '+metalspros.prod';
            break;

        case false !== strpos($_SERVER['HTTP_HOST'], '8-800-555-56-65.ru'):
            $pinbaServerName = '+8-800.prod';
            break;

        case false !== strpos($_SERVER['HTTP_HOST'], 'product.ru'):
        case false !== strpos($_SERVER['HTTP_HOST'], 'product.co.ua'):
        case false !== strpos($_SERVER['HTTP_HOST'], 'pradukt.by'):
        case false !== strpos($_SERVER['HTTP_HOST'], 'product.kz'):
        case false !== strpos($_SERVER['HTTP_HOST'], 'produkt.kz'):
        case false !== strpos($_SERVER['HTTP_HOST'], 'pokupaev.ru'):
            $pinbaServerName = '+product.prod';
            break;
    }

    pinba_hostname_set($pinbaServerName);
} else {
//    $str = serialize(array('server' => $_SERVER, 'get' => $_GET, 'post' => $_POST));
//    file_put_contents(__DIR__.'/no-pinba.log', $str, FILE_APPEND);
}

if (function_exists('newrelic_add_custom_parameter')) {
    newrelic_add_custom_parameter('HTTP_HOST', $_SERVER['HTTP_HOST']);
    newrelic_add_custom_parameter('HTTP_REFERER', $_SERVER['HTTP_REFERER']);
    newrelic_add_custom_parameter('REQUEST_METHOD', $_SERVER['REQUEST_METHOD']);
    newrelic_add_custom_parameter('is_ajax', !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
}

/**
 * @var Composer\Autoload\ClassLoader $_loader
 */
$_loader = require __DIR__.'/../app/autoload.php';
// name variable $_loader instead of $loader for preventing it overwrite after bootstrap including
include_once __DIR__.'/../app/bootstrap.php.cache';

// Use APC for autoloading to improve performance, remove when updating to PHP7
if (extension_loaded('apc')) {
    $apcLoader = new ApcClassLoader('met_', $_loader);
    $apcLoader->register(true);
}

if (!isset($hostnamePackage)) {
    $hostnamePackage = 'metalloprokat';

    switch (true) {
        case false !== strpos($_SERVER['HTTP_HOST'], 'metalloprokat.net.ua'):
        case false !== strpos($_SERVER['HTTP_HOST'], 'product.co.ua'):
            $hostnamePackage = 'metalloprokat-ua';
            break;

        case false !== strpos($_SERVER['HTTP_HOST'], 'metalloprokat.kz'):
        case false !== strpos($_SERVER['HTTP_HOST'], 'product.kz'):
        case false !== strpos($_SERVER['HTTP_HOST'], 'produkt.kz'):
            $hostnamePackage = 'metalloprokat-kz';
            break;

        case false !== strpos($_SERVER['HTTP_HOST'], 'metalaprakat.by'):
        case false !== strpos($_SERVER['HTTP_HOST'], 'pradukt.by'):
            $hostnamePackage = 'metalloprokat-by';
            break;
    }
}

$debug = false !== strpos($cookie, $extraCookies['debug_mode']);
$env = false === strpos($cookie, $extraCookies['profiler_env']) ? 'prod' : 'profiler';
$kernel = new AppKernel($env, $debug, $hostnamePackage);

$kernel->loadClassCache();
if (!empty($useHttpCache)) {
    $kernel = new AppCache($kernel);
}
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->headers->set('X-Exec-Time', round(1000 * (microtime(true) - $time)).'ms');
if (false !== $response->getContent()) {
    $response->headers->add(array(
            'Connection' => 'close',
            'Content-Length' => strlen($response->getContent()),
        ));
}
ignore_user_abort(true);
set_time_limit(600);
$response->send();
$kernel->terminate($request, $response);
