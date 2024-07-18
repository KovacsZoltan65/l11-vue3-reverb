<?php

namespace App\Support;

use Illuminate\Support\Str;

class Generators
{
    public static function generateRandomCode()
    {
        $code = Str::random(10) . time();
        
        return $code;
    }
}