<?php

$ts = microtime(true);

set_time_limit(0);
require_once __DIR__.'/autoload.php';

var_dump(round(microtime(true) - $ts, 6));
$ts = microtime(true);

$client = new \Predis\Client(new \Predis\Connection\ConnectionParameters(array('read_write_timeout' => NULL, 'iterable_multibulk' => false, 'profile' => '2.4', 'prefix' => NULL, 'replication' => false, 'async_connect' => false, 'timeout' => 5, 'persistent' => false, 'exceptions' => true, 'logging' => false, 'alias' => 'cache', 'scheme' => 'tcp', 'host' => 'redis1.katushkin.local', 'port' => 6379, 'database' => 2, 'password' => 'aw7ae3toox7B', 'weight' => NULL)), new \Predis\Option\ClientOptions(array('read_write_timeout' => NULL, 'iterable_multibulk' => false, 'profile' => new \Predis\Profile\ServerVersion24(), 'prefix' => NULL, 'replication' => false, 'async_connect' => false, 'timeout' => 5, 'persistent' => false, 'exceptions' => true)));

var_dump(round(microtime(true) - $ts, 6));
$ts = microtime(true);

$client->connect();

var_dump(round(microtime(true) - $ts, 6));
$ts = microtime(true);
