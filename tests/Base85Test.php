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
use Tuupola\Base85Proxy;
use PHPUnit\Framework\TestCase;

class Base85Test extends TestCase
{
    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldEncodeAndDecodeRandomBytes()
    {
        $data = random_bytes(128);

        $encoded = (new PhpEncoder)->encode($data);
        $decoded = (new PhpEncoder)->decode($encoded);
        $encoded2 = (new GmpEncoder)->encode($data);
        $decoded2 = (new GmpEncoder)->decode($encoded);
        $encoded3 = Base85Proxy::encode($data);
        $decoded3 = Base85Proxy::decode($encoded3);
        $encoded4 = (new Base85)->encode($data);
        $decoded4 = (new Base85)->decode($encoded4);

        $this->assertEquals($encoded, $encoded2);
        $this->assertEquals($encoded, $encoded3);
        $this->assertEquals($encoded, $encoded4);

        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);
        $this->assertEquals($data, $decoded4);
    }

    public function testShouldEncodeAndDecodeIntegers()
    {
        $data = 4294967295;

        $encoded = (new PhpEncoder)->encode($data);
        $decoded = (new PhpEncoder)->decode($encoded, true);
        $encoded2 = (new GmpEncoder)->encode($data);
        $decoded2 = (new GmpEncoder)->decode($encoded2, true);
        $encoded3 = Base85Proxy::encode($data);
        $decoded3 = Base85Proxy::decode($encoded3, true);
        $encoded4 = (new Base85)->encode($data);
        $decoded4 = (new Base85)->decode($encoded4, true);

        $this->assertEquals($encoded, $encoded2);
        $this->assertEquals($encoded, $encoded3);
        $this->assertEquals($encoded, $encoded4);

        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);
        $this->assertEquals($data, $decoded4);
    }

    public function testShouldEncodeAndDecodeWithLeadingZero()
    {
        $data = hex2bin("07d8e31da269bf28");

        $encoded = (new PhpEncoder)->encode($data);
        $decoded = (new PhpEncoder)->decode($encoded);
        $encoded2 = (new GmpEncoder)->encode($data);
        $decoded2 = (new GmpEncoder)->decode($encoded2);
        $encoded3 = Base85Proxy::encode($data);
        $decoded3 = Base85Proxy::decode($encoded3);
        $encoded4 = (new Base85)->encode($data);
        $decoded4 = (new Base85)->decode($encoded4);

        $this->assertEquals($encoded, $encoded2);
        $this->assertEquals($encoded, $encoded3);
        $this->assertEquals($encoded, $encoded4);

        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);
        $this->assertEquals($data, $decoded3);
        $this->assertEquals($data, $decoded4);
    }

    public function testShouldHandleFourSpacesException()
    {
        $this->assertEquals((new PhpEncoder(["compress.spaces" => true]))->encode("    "), "y");
        $this->assertEquals((new GmpEncoder(["compress.spaces" => true]))->encode("    "), "y");
        $this->assertEquals((new PhpEncoder(["compress.spaces" => true]))->decode("y"), "    ");
        $this->assertEquals((new GmpEncoder(["compress.spaces" => true]))->decode("y"), "    ");

        $encoded = (new PhpEncoder(["compress.spaces" => true]))->encode("Hiya    world!");
        $encoded2 = (new GmpEncoder(["compress.spaces" => true]))->encode("Hiya    world!");

        $this->assertEquals($encoded, $encoded2);

        $this->assertEquals((new PhpEncoder(["compress.spaces" => true]))->decode($encoded), "Hiya    world!");
        $this->assertEquals((new GmpEncoder(["compress.spaces" => true]))->decode($encoded2), "Hiya    world!");
    }

    public function testShouldHandleAllZeroDataException()
    {
        $this->assertEquals((new PhpEncoder)->encode("\0\0"), "!!!");
        $this->assertEquals((new GmpEncoder)->encode("\0\0"), "!!!");
        $this->assertEquals((new PhpEncoder)->decode("!!!"), "\0\0");
        $this->assertEquals((new GmpEncoder)->decode("!!!"), "\0\0");
        $this->assertEquals((new PhpEncoder)->encode("\0\0\0\0"), "!!!!!");
        $this->assertEquals((new GmpEncoder)->encode("\0\0\0\0"), "!!!!!");
        $this->assertEquals((new PhpEncoder)->decode("!!!!!"), "\0\0\0\0");
        $this->assertEquals((new GmpEncoder)->decode("!!!!!"), "\0\0\0\0");

        $this->assertEquals((new PhpEncoder)->encode("aaaa\0\0\0\0"), "@:<SQ!!!!!");
        $this->assertEquals((new GmpEncoder)->encode("aaaa\0\0\0\0"), "@:<SQ!!!!!");
        $this->assertEquals((new PhpEncoder)->encode("\0\0\0\0aaaa"), "z@:<SQ");
        $this->assertEquals((new GmpEncoder)->encode("\0\0\0\0aaaa"), "z@:<SQ");
        $this->assertEquals((new PhpEncoder)->decode("@:<SQ!!!!!"), "aaaa\0\0\0\0");
        $this->assertEquals((new GmpEncoder)->decode("@:<SQ!!!!!"), "aaaa\0\0\0\0");
        $this->assertEquals((new PhpEncoder)->encode("aaaa\0\0\0\0bb"), "@:<SQz@U]");
        $this->assertEquals((new GmpEncoder)->encode("aaaa\0\0\0\0bb"), "@:<SQz@U]");

        $this->assertEquals((new PhpEncoder)->decode("@:<SQz@U]"), "aaaa\0\0\0\0bb");
        $this->assertEquals((new GmpEncoder)->decode("@:<SQz@U]"), "aaaa\0\0\0\0bb");
    }

    public function testShouldHandleAdobeAscii85Mode()
    {
        $options = [
            "characters" => Base85::ASCII85,
            "compress.spaces" => false,
            "compress.zeroes" => true,
            "prefix" => "<~",
            "suffix" => "~>"
        ];

        Base85Proxy::$options = $options;

        $phpAdobe85 = new PhpEncoder($options);
        $gmpAdobe85 = new GmpEncoder($options);

        $encoded = $phpAdobe85->encode("Not sure.");
        $encoded2 = $gmpAdobe85->encode("Not sure.");
        $encoded3 = Base85Proxy::encode("Not sure.", $options);

        $this->assertEquals($encoded, "<~:2b4sF*2M7/c~>");
        $this->assertEquals($encoded, $encoded2);
        $this->assertEquals($encoded, $encoded3);

        $data = "randomjunk<~:2b4sF*2M7/c~>randomjunk";
        $this->assertEquals($phpAdobe85->decode($data), "Not sure.");
        $this->assertEquals($phpAdobe85->decode($data), "Not sure.");
        $this->assertEquals(Base85Proxy::decode($data), "Not sure.");
    }

    public function testShouldAutoSelectEncoder()
    {
        $data = random_bytes(128);
        $encoded = (new Base85)->encode($data);
        $decoded = (new Base85)->decode($encoded);

        $this->assertEquals($data, $decoded);
    }
}
