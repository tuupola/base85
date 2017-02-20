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

class PhpEncoder
{
    public static function encode($data)
    {
        /* If we got integer convert it to string. */
        if (is_integer($data)) {
            $data = pack("N", $data);
        };

        $padding = 0;
        if ($modulus = strlen($data) % 4) {
            $padding = 4 - $modulus;
        }
        $data .= str_repeat("\0", $padding);

        $converted = [];
        foreach (unpack("N*", $data) as $uint32) {
            /* Four spaces exception. */
            if (0x20202020 === $uint32) {
                $converted[] = "y";
                continue;
            }

            /* All zero data exception. */
            if (0x00000000 === $uint32) {
                $converted[] = "z";
                continue;
            }

            $digits = "";
            $quotient = $uint32;
            foreach ([52200625, 614125, 7225, 85, 1] as $pow) {
                $reminder = $quotient % $pow;
                $quotient = (integer) ($quotient / $pow);
                $digits .= chr($quotient + 33);
                $quotient = $reminder;
            }

            $converted[] = $digits;
        }

        $last = count($converted) - 1;

        /* The z exception does not apply to the last block. */
        if ("z" === $converted[$last]) {
            $converted[$last] = "!!!!!";
        }

        /* Remove any padding from the returned result. */
        if ($padding) {
            $converted[$last] = substr($converted[$last], 0, 5 - $padding);
        }

        return implode($converted);
    }

    public static function decode($data, $integer = false)
    {
        /* Uncompress all zero and four spaces exceptions. */
        $data = str_replace("z", "!!!!!", $data);
        $data = str_replace("y", "+<VdL", $data);

        $padding = 0;
        if ($modulus = strlen($data) % 5) {
            $padding = 5 - $modulus;
            $data .= str_repeat("u", $padding);
        }

        /* From group of five base85 characters convert back to uint32. */
        $digits =  str_split($data, 5);
        $converted = array_map(function ($value) {
            $accumulator = 0;
            foreach (unpack("C*", $value) as $char) {
                $accumulator = $accumulator * 85 + $char - 33;
            }
            return pack("N", $accumulator);
        }, $digits);

        /* Remove any padding from the returned result. */
        $last = count($converted) - 1;
        if ($padding) {
            $converted[$last] = substr($converted[$last], 0, 4 - $padding);
        }

        if ($integer) {
            return array_values(unpack("N", implode($converted)))[0];
        }
        return implode($converted);
    }
}
