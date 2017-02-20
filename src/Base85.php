<?php

/*
 * This file is part of the Base85 package
 *
 * Copyright (c) 2017 Mika Tuupola
 *
 * Licensed under the MIT license:
 *   http://www.opensource.org/licenses/mit-license.php
 *
 * Project home:
 *   https://github.com/tuupola/base85
 *
 */

namespace Tuupola;

class Base85
{
    public static function encode($data)
    {
        if (function_exists("gmp_init")) {
            return Base85\GmpEncoder::encode($data);
        }
        return Base85\PhpEncoder::encode($data);
    }

    public static function decode($data, $integer = false)
    {
        if (function_exists("gmp_init")) {
            return Base85\GmpEncoder::decode($data, $integer);
        }
        return Base85\PhpEncoder::decode($data, $integer);
    }
}
