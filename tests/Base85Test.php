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

use InvalidArgumentException;
use Tuupola\Base85;
use Tuupola\Base85Proxy;
use PHPUnit\Framework\TestCase;

class Base85Test extends TestCase
{
    protected function tearDown()
    {
        Base85Proxy::$options = [
            "characters" => Base85::ASCII85,
        ];
    }

    public function testShouldBeTrue()
    {
        $this->assertTrue(true);
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeRandomBytes($configuration)
    {
        $data = random_bytes(128);

        $php = new PhpEncoder($configuration);
        $gmp = new GmpEncoder($configuration);
        $base85 = new Base85($configuration);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base85->encode($data);

        Base85Proxy::$options = $configuration;
        $encoded5 = Base85Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $base85->decode($encoded4));
        $this->assertEquals($data, Base85Proxy::decode($encoded5));
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeIntegers($configuration)
    {
        $data = 987654321;

        $php = new PhpEncoder($configuration);
        $gmp = new GmpEncoder($configuration);
        $base85 = new Base85($configuration);

        $encoded = $php->encodeInteger($data);
        $encoded2 = $gmp->encodeInteger($data);
        $encoded4 = $base85->encodeInteger($data);

        Base85Proxy::$options = $configuration;
        $encoded5 = Base85Proxy::encodeInteger($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decodeInteger($encoded));
        $this->assertEquals($data, $gmp->decodeInteger($encoded2));
        $this->assertEquals($data, $base85->decodeInteger($encoded4));
        $this->assertEquals($data, Base85Proxy::decodeInteger($encoded5));
    }

    public function testShouldAutoSelectEncoder()
    {
        $data = random_bytes(128);
        $encoded = (new Base85)->encode($data);
        $decoded = (new Base85)->decode($encoded);

        $this->assertEquals($data, $decoded);
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeWithLeadingZero($configuration)
    {
        $data = hex2bin("07d8e31da269bf28");

        $php = new PhpEncoder($configuration);
        $gmp = new GmpEncoder($configuration);
        $base85 = new Base85($configuration);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base85->encode($data);

        Base85Proxy::$options = $configuration;
        $encoded5 = Base85Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $base85->decode($encoded4));
        $this->assertEquals($data, Base85Proxy::decode($encoded5));
    }

    public function testShouldUseDefaultCharacterSet()
    {
        $data = "Hello world!";

        $php = new PhpEncoder();
        $gmp = new GmpEncoder();
        $base85 = new Base85();

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base85->encode($data);

        // Base85Proxy::$options = [
        //     "characters" => $configuration,
        // ];
        $encoded5 = Base85Proxy::encode($data);

        $this->assertEquals($encoded, "87cURD]j7BEbo80");
        $this->assertEquals($encoded2, "87cURD]j7BEbo80");
        $this->assertEquals($encoded4, "87cURD]j7BEbo80");
        $this->assertEquals($encoded5, "87cURD]j7BEbo80");

        $data = hex2bin("0000010203040506");
        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base85->encode($data);

        // Base85Proxy::$options = [
        //     "characters" => $configuration,
        // ];
        $encoded5 = Base85Proxy::encode($data);

        $this->assertEquals($encoded, "!!!$$!sAc3");
        $this->assertEquals($encoded2, "!!!$$!sAc3");
        $this->assertEquals($encoded4, "!!!$$!sAc3");
        $this->assertEquals($encoded5, "!!!$$!sAc3");
    }

    // public function testShouldUseInvertedCharacterSet()
    // {
    //     $data = "Hello world!";

    //     $php = new PhpEncoder(["characters" => Base85::INVERTED]);
    //     $gmp = new GmpEncoder(["characters" => Base85::INVERTED]);
    //     $base85 = new Base85(["characters" => Base85::INVERTED]);

    //     $encoded = $php->encode($data);
    //     $encoded2 = $gmp->encode($data);
    //     $encoded4 = $base85->encode($data);

    //     Base85Proxy::$options = [
    //         "characters" => Base85::INVERTED,
    //     ];
    //     $encoded5 = Base85Proxy::encode($data);

    //     $this->assertEquals($encoded, "t8DGCJrgUyuUEwHT");
    //     $this->assertEquals($encoded2, "t8DGCJrgUyuUEwHT");
    //     $this->assertEquals($encoded3, "t8DGCJrgUyuUEwHT");
    //     $this->assertEquals($encoded4, "t8DGCJrgUyuUEwHT");
    //     $this->assertEquals($encoded5, "t8DGCJrgUyuUEwHT");

    //     $data = hex2bin("0000010203040506");

    //     $encoded = $php->encode($data);
    //     $encoded2 = $gmp->encode($data);
    //     $encoded4 = $base85->encode($data);
    //     $encoded5 = Base85Proxy::encode($data);

    //     $this->assertEquals($encoded, "00jvB3wii");
    //     $this->assertEquals($encoded2, "00jvB3wii");
    //     $this->assertEquals($encoded4, "00jvB3wii");
    //     $this->assertEquals($encoded5, "00jvB3wii");
    // }

    public function testShouldUseCustomCharacterSet()
    {
        $data = "Hello world!";
        $characters = "!\"#$%&'()*+,-./9876543210:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstu";

        $php = new PhpEncoder(["characters" => $characters]);
        $gmp = new GmpEncoder(["characters" => $characters]);
        $base85 = new Base85(["characters" => $characters]);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base85->encode($data);

        Base85Proxy::$options = [
            "characters" => $characters,
        ];
        $encoded5 = Base85Proxy::encode($data);

        $this->assertEquals($encoded, "12cURD]j2BEbo19");
        $this->assertEquals($encoded2, "12cURD]j2BEbo19");
        $this->assertEquals($encoded4, "12cURD]j2BEbo19");
        $this->assertEquals($encoded5, "12cURD]j2BEbo19");

        $data = hex2bin("0000010203040506");

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base85->encode($data);
        $encoded5 = Base85Proxy::encode($data);

        $this->assertEquals($encoded, "!!!$$!sAc6");
        $this->assertEquals($encoded2, "!!!$$!sAc6");
        $this->assertEquals($encoded4, "!!!$$!sAc6");
        $this->assertEquals($encoded5, "!!!$$!sAc6");
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeBigIntegers($configuration)
    {
        $data = PHP_INT_MAX;

        $php = new PhpEncoder($configuration);
        $gmp = new GmpEncoder($configuration);
        $base85 = new Base85($configuration);

        $encoded = $php->encodeInteger($data);
        $encoded2 = $gmp->encodeInteger($data);
        $encoded4 = $base85->encodeInteger($data);

        Base85Proxy::$options = $configuration;
        $encoded5 = Base85Proxy::encodeInteger($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decodeInteger($encoded));
        $this->assertEquals($data, $gmp->decodeInteger($encoded2));
        $this->assertEquals($data, $base85->decodeInteger($encoded4));
        $this->assertEquals($data, Base85Proxy::decodeInteger($encoded5));
    }

    // public function testShouldThrowExceptionOnDecodeInvalidData()
    // {
    //     $invalid = "invalid~data-%@#!@*#-foo";

    //     $decoders = [
    //         new PhpEncoder(),
    //         new GmpEncoder(),
    //         new Base85(),
    //     ];

    //     foreach ($decoders as $decoder) {
    //         $caught = null;

    //         try {
    //             $decoder->decode($invalid, false);
    //         } catch (InvalidArgumentException $exception) {
    //             $caught = $exception;
    //         }

    //         $this->assertInstanceOf(InvalidArgumentException::class, $caught);
    //     }
    // }

    /**
     * @dataProvider encoderProvider
     */
    public function testShouldThrowExceptionOnDecodeInvalidDataWithCustomCharacterSet($encoder)
    {
        /* This would normally be valid, however the custom character set */
        /* is missing the e character. */
        $invalid = "T8dgcjRGuYUueWht";
        $options = [
            "characters" => "!\"#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdxfghijklmnopqrstu"
        ];

        (new $encoder($options))->decode($invalid);

        // foreach ($decoders as $decoder) {
        //     $caught = null;

        //     try {
        //         $decoder->decode($invalid, false);
        //     } catch (InvalidArgumentException $exception) {
        //         $caught = $exception;
        //     }

        //     $this->assertInstanceOf(InvalidArgumentException::class, $caught);
        // }
    }

    public function testShouldThrowExceptionWithInvalidCharacterSet()
    {
        $options = [
            "characters" => "!!#$%&'()*+,-./0123456789:;<=>?@ABCDEFGHIJKLMNOPQRSTUVWXYZ[\]^_`abcdefghijklmnopqrstu"
        ];

        $decoders = [
            PhpEncoder::class,
            GmpEncoder::class,
            Base85::class,
        ];

        foreach ($decoders as $decoder) {
            $caught = null;

            try {
                new $decoder($options);
            } catch (InvalidArgumentException $exception) {
                $caught = $exception;
            }

            $this->assertInstanceOf(InvalidArgumentException::class, $caught);
        }

        $options = [
            "characters" => "00123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"
        ];


        foreach ($decoders as $decoder) {
            $caught = null;

            try {
                new $decoder($options);
            } catch (InvalidArgumentException $exception) {
                $caught = $exception;
            }

            $this->assertInstanceOf(InvalidArgumentException::class, $caught);
        }
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeSingleZeroByte($configuration)
    {
        $data = "\x00";

        $php = new PhpEncoder($configuration);
        $gmp = new GmpEncoder($configuration);
        $base85 = new Base85($configuration);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base85->encode($data);

        Base85Proxy::$options = $configuration;
        $encoded5 = Base85Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $base85->decode($encoded4));
        $this->assertEquals($data, Base85Proxy::decode($encoded5));
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeMultipleZeroBytes($configuration)
    {
        $data = "\x00\x00\x00";

        $php = new PhpEncoder($configuration);
        $gmp = new GmpEncoder($configuration);
        $base85 = new Base85($configuration);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base85->encode($data);

        Base85Proxy::$options = $configuration;
        $encoded5 = Base85Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $base85->decode($encoded4));
        $this->assertEquals($data, Base85Proxy::decode($encoded5));
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeSingleZeroBytePrefix($configuration)
    {
        $data = "\x00\x01\x02";

        $php = new PhpEncoder($configuration);
        $gmp = new GmpEncoder($configuration);
        $base85 = new Base85($configuration);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base85->encode($data);

        Base85Proxy::$options = $configuration;
        $encoded5 = Base85Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $base85->decode($encoded4));
        $this->assertEquals($data, Base85Proxy::decode($encoded5));
    }

    /**
     * @dataProvider configurationProvider
     */
    public function testShouldEncodeAndDecodeMultipleZeroBytePrefix($configuration)
    {
        $data = "\x00\x00\x00\x01\x02";

        $php = new PhpEncoder($configuration);
        $gmp = new GmpEncoder($configuration);
        $base85 = new Base85($configuration);

        $encoded = $php->encode($data);
        $encoded2 = $gmp->encode($data);
        $encoded4 = $base85->encode($data);

        Base85Proxy::$options = $configuration;
        $encoded5 = Base85Proxy::encode($data);

        $this->assertEquals($encoded2, $encoded);
        $this->assertEquals($encoded4, $encoded);
        $this->assertEquals($encoded5, $encoded);

        $this->assertEquals($data, $php->decode($encoded));
        $this->assertEquals($data, $gmp->decode($encoded2));
        $this->assertEquals($data, $base85->decode($encoded4));
        $this->assertEquals($data, Base85Proxy::decode($encoded5));
    }

    public function configurationProvider()
    {
        return [
            "ASCII85 mode" => [[
                "characters" => Base85::ASCII85,
                "compress.spaces" => false,
                "compress.zeroes" => true
            ]],
            "ADOBE85 mode" => [[
                    "characters" => Base85::ASCII85,
                    "compress.spaces" => false,
                    "compress.zeroes" => true,
                    "prefix" => "<~",
                    "suffix" => "~>"
            ]],
            "Z85 mode" => [[
                "characters" => Base85::Z85,
                "compress.spaces" => false,
                "compress.zeroes" => false
            ]],
            "RFC1924 mode" => [[
                "characters" => Base85::RFC1924,
                "compress.spaces" => false,
                "compress.zeroes" => false
            ]],
        ];
    }

    public function encoderProvider()
    {
        return [
            "PHP encoder" => [PhpEncoder::class],
            "GMP encoder" => [GmpEncoder::class],
            "Base encoder" => [Base58::class],
        ];
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
}
