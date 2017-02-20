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

namespace Tuupola\Base85;

use Tuupola\Base85;

class Encoder
{
    public function encode($data)
    {
        return Base85::encode($data);
    }

    public function decode($data, $integer = false)
    {
        return Base85::decode($data, $integer);
    }
}
