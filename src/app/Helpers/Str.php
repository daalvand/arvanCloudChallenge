<?php

namespace App\Helpers;

use RuntimeException;

class Str
{
    //generate unique string
    public static function unique(int $length = 16)
    {
    if (function_exists("random_bytes")) {
        $bytes = random_bytes(ceil($length / 2));
    } elseif (function_exists("openssl_random_pseudo_bytes")) {
        $bytes = openssl_random_pseudo_bytes(ceil($length / 2));
    } else {
        throw new RuntimeException("no cryptographically secure random function available");
    }
    return substr(bin2hex($bytes), 0, $length);
    }
}
