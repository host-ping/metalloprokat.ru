<?php

namespace Metal\ProjectBundle\Util;

class RandomGenerator
{
    public static function generateRandomCode($length = 16)
    {
        return substr(sha1(microtime(true).mt_rand(0, 999)), 0, $length);
    }
}
