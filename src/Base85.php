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

final class Base85
{
    /* Adobe ASCII85. Only all zero data exception, ignore whitespace. */
    /* https://www.adobe.com/products/postscript/pdfs/PLRM.pdf */
    const ASCII85 = "!\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstu";

    /* https://rfc.zeromq.org/spec:32/Z85/ */
    const Z85 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz.-:+=^!/*?&<>()[]{}@%$#";

    /* https://tools.ietf.org/html/rfc1924 which is an Aprils fools joke. */
    const RFC1924 = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!#$%&()*+-;<=>?@^_`{|}~";

    private $options = [];
    private $encoder;

    public function __construct($options = [])
    {
        $this->options = array_merge($this->options, (array) $options);
        if (function_exists("gmp_init")) {
            $this->encoder = new Base85\GmpEncoder($this->options);
        }
        $this->encoder = new Base85\PhpEncoder($this->options);
    }

    public function encode($data)
    {
        return $this->encoder->encode($data);
    }

    public function decode($data, $integer = false)
    {
        return $this->encoder->decode($data, $integer);
    }
}
