#!/usr/bin/php
<?php
$processUser = posix_getpwuid(posix_geteuid());
$name = $processUser['dir'].'/mail/'.date('Y-m-d H:i:s').'-'.microtime(true).'.eml';
if (!is_dir($dir = dirname($name))) {
    mkdir($dir, 0777, true);
}
$fh = fopen($name, 'w');
if (!$fh) {
    die(1);
}
while ($buf = fread(STDIN, 8192)) {
    fwrite($fh, $buf);
}
fclose($fh);
