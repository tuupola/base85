<?php

/*

Copyright (c) 2017-2019 Mika Tuupola

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
