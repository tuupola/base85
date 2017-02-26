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

abstract class BaseEncoder
{
    protected $options = [
        "characters" => Base85::ASCII85,
        "compress.spaces" => false,
        "compress.zeroes" => true,
        "prefix" => null,
        "suffix" => null,
    ];

    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, (array) $options);
    }

    abstract public function encode($data);

    public function decode($data, $integer = false)
    {
        /* Extract data between prefix and suffix. */
        if ($this->options["prefix"] && $this->options["suffix"]) {
            $prefix = preg_quote($this->options["prefix"]);
            $suffix = preg_quote($this->options["suffix"]);
            preg_match("/$prefix(.*)$suffix/", $data, $matches);
            $data = $matches[1];
        }

        if ($this->options["compress.zeroes"]) {
            $data = str_replace("z", "!!!!!", $data);
        }

        if ($this->options["compress.spaces"]) {
            $data = str_replace("y", "+<VdL", $data);
        }

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
                $accumulator = $accumulator * 85 + strpos($this->options["characters"], $char);
            }
            return pack("N", $accumulator);
        }, $digits);

        /* Remove any padding from the returned result. */
        $last = count($converted) - 1;
        if ($padding) {
            $converted[$last] = substr($converted[$last], 0, 4 - $padding);
        }

        if (true === $integer) {
            return array_values(unpack("N", implode($converted)))[0];
        }
        return implode($converted);
    }
}
