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

class PhpEncoder extends BaseEncoder
{
    public function encode($data)
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

        $converted = [$this->options["prefix"]];
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
            $quotient = $uint32;
            foreach ([52200625, 614125, 7225, 85, 1] as $pow) {
                $reminder = $quotient % $pow;
                $quotient = (integer) ($quotient / $pow);
                $digits .= $this->options["characters"][$quotient];
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

        $converted[] = $this->options["suffix"];

        return implode($converted);
    }
}
