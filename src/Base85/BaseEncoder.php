<?php

declare(strict_types = 1);

/*

Copyright (c) 2017-2021 Mika Tuupola

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

*/

/**
 * @see       https://github.com/tuupola/base85
 * @license   https://opensource.org/licenses/mit-license.php
 */

namespace Tuupola\Base85;

use InvalidArgumentException;
use Tuupola\Base85;

abstract class BaseEncoder
{
    /**
     * @var array
     */
    protected $options = [
        "characters" => Base85::ASCII85,
        "compress.spaces" => false,
        "compress.zeroes" => true,
        "prefix" => null,
        "suffix" => null,
    ];

    public function __construct(array $options = [])
    {
        $this->options = array_merge($this->options, (array) $options);

        $uniques = count_chars($this->options["characters"], 3);
        /** @phpstan-ignore-next-line */
        if (85 !== strlen($uniques) || 85 !== strlen($this->options["characters"])) {
            throw new InvalidArgumentException("Character set must contain 85 unique characters");
        }
    }

    private function prepareData(string $data): array
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

        /* If the data contains characters that aren't in the character set. */
        if (strlen($data) !== strspn($data, $this->options["characters"])) {
            $valid = str_split($this->options["characters"]);
            $invalid = str_replace($valid, "", $data);
            $invalid = count_chars($invalid, 3);
            throw new InvalidArgumentException(
                /** @phpstan-ignore-next-line */
                "Data contains invalid characters \"{$invalid}\""
            );
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
            foreach ((array)unpack("C*", $value) as $char) {
                $accumulator = $accumulator * 85 + strpos($this->options["characters"], chr($char));
            }
            return pack("N", $accumulator);
        }, $digits);

        /* Remove any padding from the returned result. */
        $last = count($converted) - 1;
        if ($padding) {
            $converted[$last] = substr($converted[$last], 0, 4 - $padding);
        }

        return $converted;
    }

    /**
     * Encode given data to a base85 string
     */
    abstract public function encode(string $data): string;

    /**
     * Decode given a base85 string back to data
     */
    public function decode(string $data): string
    {
        $converted = $this->prepareData($data);
        return implode($converted);
    }

    /**
     * Encode given integer to a base85 string
     */
    public function encodeInteger(int $data): string
    {
        /* Convert integer to string. */
        if (8 === PHP_INT_SIZE) {
            $data = pack("J", $data);
        } else {
            $data = pack("N", $data);
        }

        return $this->encode($data);
    }

    /**
     * Decode given base85 string back to an integer
     */
    public function decodeInteger(string $data): int
    {
        $converted = $this->prepareData($data);

        if (8 === PHP_INT_SIZE) {
            return array_values((array)unpack("J", implode($converted)))[0];
        } else {
            return array_values((array)unpack("N", implode($converted)))[0];
        }
    }
}
