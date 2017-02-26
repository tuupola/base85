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

class Base85Test extends \PHPUnit_Framework_TestCase
{

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    public function testShouldEncodeAndDecodeRandomBytes()
    {
        $data = random_bytes(128);
        $encoded = (new PhpEncoder)->encode($data);
        $encoded2 = (new GmpEncoder)->encode($data);
        $decoded = (new PhpEncoder)->decode($encoded);
        $decoded2 = (new GmpEncoder)->decode($encoded2);

        $this->assertEquals($encoded, $encoded2);

        $this->assertEquals($decoded2, $decoded);
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);

        $base85 = new Base85;
        $encoded4 = $base85->encode($data);
        $decoded4 = $base85->decode($encoded4);
        $this->assertEquals($data, $decoded4);
    }

    public function testShouldEncodeAndDecodeIntegers()
    {
        $data = 4294967295;

        $encoded = (new PhpEncoder)->encode($data);
        $encoded2 = (new GmpEncoder)->encode($data);
        $decoded = (new PhpEncoder)->decode($encoded, true);
        $decoded2 = (new GmpEncoder)->decode($encoded2, true);

        $this->assertEquals($decoded2, $decoded);
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);

        $base85 = new Base85;
        $encoded4 = $base85->encode($data);
        $decoded4 = $base85->decode($encoded4, true);
        $this->assertEquals($data, $decoded4);
    }

    public function testShouldEncodeAndDecodeWithLeadingZero()
    {
        $data = hex2bin("07d8e31da269bf28");
        $encoded = (new PhpEncoder)->encode($data);
        $encoded2 = (new GmpEncoder)->encode($data);
        $decoded = (new PhpEncoder)->decode($encoded);
        $decoded2 = (new GmpEncoder)->decode($encoded2);

        $this->assertEquals($decoded2, $decoded);
        $this->assertEquals($data, $decoded);
        $this->assertEquals($data, $decoded2);

        $base85 = new Base85;
        $encoded4 = $base85->encode($data);
        $decoded4 = $base85->decode($encoded4);
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

    public function testShouldAutoSelectEncoder()
    {
        $data = random_bytes(128);
        $encoded = (new Base85)->encode($data);
        $decoded = (new Base85)->decode($encoded);

        $this->assertEquals($data, $decoded);
    }
}
