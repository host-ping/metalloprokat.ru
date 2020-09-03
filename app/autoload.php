<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

error_reporting(error_reporting() & ~E_USER_DEPRECATED);

$memory_limit = return_bytes(ini_get('memory_limit'));
if ($memory_limit < (512 * 1024 * 1024)) {
    ini_set('memory_limit', '512M');
}

/**
 * Converts shorthand memory notation value to bytes
 * From http://php.net/manual/en/function.ini-get.php
 *
 * @param string $val Memory size shorthand notation string
 */
function return_bytes($val)
{
    $val = trim($val);
    $last = substr($val, -1);
    $val = (int)substr($val, 0, -1);

    switch (strtoupper($last)) {
        case 'G':
            $val *= 1024;
        case 'M':
            $val *= 1024;
        case 'K':
            $val *= 1024;
    }

    return $val;
}

function vd()
{
    $args = func_get_args();
    call_user_func_array('var_dump', $args);
    exit;
}

function clear_buffer()
{
    while (ob_get_level()) {
        ob_end_clean();
    }
}

function disable_fancy_vd()
{
    ini_set('xdebug.default_enable', false);
    ini_set('html_errors', false);
}

function vdc()
{
    clear_buffer();
    $args = func_get_args();
    call_user_func_array('var_dump', $args);
    exit;
}

function doctrine_dump($object, $maxDepth = 3)
{
    \Doctrine\Common\Util\Debug::dump($object, $maxDepth, false);
}

$loader = require __DIR__.'/../vendor/autoload.php';

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

mb_internal_encoding('UTF-8');
define('DOMPDF_ENABLE_AUTOLOAD', false);
require_once __DIR__.'/../vendor/dompdf/dompdf/dompdf_config.inc.php';

return $loader;
