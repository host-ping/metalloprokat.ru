<?php
$dateIn = '27.10.2014';
$tz = 'Europe/Moscow';
date_default_timezone_set($tz);
$intlDateFormatter = new \IntlDateFormatter('ru_RU', 2, -1, $tz, 1, 'dd.MM.yyyy');
$timestamp = $intlDateFormatter->parse($dateIn);

var_dump($intlDateFormatter->format($timestamp));
var_dump(date('d.m.Y', $timestamp));
