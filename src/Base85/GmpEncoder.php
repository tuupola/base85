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

final class GmpEncoder extends BaseEncoder
{
    public function encode($data)
    {
        $powers = [
            gmp_init("52200625", 10),
            gmp_init("614125", 10),
            gmp_init("7225", 10),
            gmp_init("85", 10),
            gmp_init("1", 10),
        ];

        if (is_integer($data)) {
            $data = pack("N", $data);
        };

        $padding = 0;
        if ($modulus = strlen($data) % 4) {
            $padding = 4 - $modulus;
            $data .= str_repeat("\0", $padding);
        }

        $converted = [];
        foreach (unpack("N*", $data) as $uint32) {
            /* Four spaces exception. */
            if ($this->options["compress.spaces"]) {
                if (0x20202020 === $uint32) {
                    $converted[] = "y";
                    continue;
                }
            }

            /* All zero data exception. */
            if ($this->options["compress.zeroes"]) {
                if (0x00000000 === $uint32) {
                    $converted[] = "z";
                    continue;
                }
            }

            $digits = "";
            $quotient = gmp_init($uint32, 10);

            foreach ($powers as $pow) {
                list($quotient, $reminder) = gmp_div_qr($quotient, $pow);
                $quotient = gmp_intval($quotient);
                $digits .= chr($quotient + 33);
                $quotient = $reminder;
            }

            $converted[] = $digits;
        }

        $last = count($converted) - 1;

        /* The z exception does not apply to the last block. */
        if ($this->options["compress.zeroes"] && "z" === $converted[$last]) {
            $converted[$last] = "!!!!!";
        }

        /* Remove any padding from the returned result. */
        if ($padding) {
            $converted[$last] = substr($converted[$last], 0, 5 - $padding);
        }

        return implode($converted);
    }
}
