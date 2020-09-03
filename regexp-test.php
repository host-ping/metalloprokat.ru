<?php

//$companyAddress = '620014, Россия, г. Екатеринбург, пр. Ленина, д. 24/8, оф. 411';
//$companyAddress = trim(preg_replace('/(^|[^а-яА-Я0-9])(г|город)($|\W)/ui', ' ', $companyAddress));

//die($companyAddress);

$string = 'Шина алюминиевая  АД31Т ГОСТ 15176-89';

$fractions = array(
	'/\xBC/u' => '1/4',
	'/\xBD/u' => '1/2',
	'/\xBE/u' => '3/4',
	'/\x2153/u' => '1/3',
	'/\x2154/u' => '2/3',
	'/\x2155/u' => '1/5',
	'/\x2156/u' => '2/5',
	'/\x2157/u' => '3/5',
	'/\x2158/u' => '4/5',
	'/\x2159/u' => '1/6',
	'/\x215A/u' => '5/6',
	'/\x215B/u' => '1/8',
	'/\x215C/u' => '3/8',
	'/\x215D/u' => '5/8',
	'/\x215E/u' => '7/8'
);

$string = preg_replace(array_keys($fractions), array_values($fractions), $string);
$string = preg_replace('/[ \xC2\xA0\n\r\t\v]+/ui', ' ', $string);
//delete 'Æ Δ Ω Ç' .....
$string = preg_replace('/[^а-яa-z0-9\-\(\)\s ~@\"\';:\?.,#%№\$\/\\\\&\*\-_\+=ё]/ui', '', $string);
$string = trim($string);

die($string);
