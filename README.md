# All your Base85

[![Latest Version](https://img.shields.io/packagist/v/tuupola/base85.svg?style=flat-square)](https://packagist.org/packages/tuupola/base85)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/github/workflow/status/tuupola/base85/Tests/2.x?style=flat-square)](https://github.com/tuupola/base85/actions)[![Coverage](https://img.shields.io/codecov/c/github/tuupola/base85.svg?style=flat-square)](https://codecov.io/github/tuupola/base85)

## Install

Install with [composer](https://getcomposer.org/).

``` bash
$ composer require tuupola/base85
```

This branch requires PHP 7.1 or up. The older 1.x branch supports also PHP 5.6 and 7.0.

``` bash
$ composer require "tuupola/base85:^1.0"
```

## Usage

This package has both pure PHP and [GMP](http://php.net/manual/en/ref.gmp.php) based encoders. By default encoder and decoder will use GMP functions if the extension is installed. If GMP is not available pure PHP encoder will be used instead.

``` php
$base85 = new Tuupola\Base85;

$encoded = $base85->encode(random_bytes(128));
$decoded = $base85->decode($encoded);
```

If you are encoding to and from integer use the implicit `decodeInteger()` and `encodeInteger()` methods.

``` php
$integer = $base85->encodeInteger(987654321); /* 3o4PT */
print $base85->decodeInteger("3o4PT", true); /* 987654321 */
```

Note that encoding a string and an integer will yield different results.

``` php
$string = $base85->encode("987654321"); /* 3B/rU2)I*E0` */
$integer = $base85->encodeInteger(987654321); /* 3o4PT */
```

## Encoding modes

[ASCII85](https://en.wikipedia.org/wiki/Ascii85) encoding. This is the default. `0x00000000` is compressed to `z`. Spaces are not compressed.

``` php
use Tuupola\Base85;

$ascii85 = new Base85([
    "characters" => Base85::ASCII85,
    "compress.spaces" => false,
    "compress.zeroes" => true
]);

print $ascii85->encode("Hello world!"); /* 87cURD]j7BEbo80 */
```

[Adobe ASCII85](https://en.wikipedia.org/wiki/Ascii85) encoding is same as previous except data is enclosed between `<~` and `~>`.

``` php
use Tuupola\Base85;

$adobe85 = new Base85([
    "characters" => Base85::ASCII85,
    "compress.spaces" => false,
    "compress.zeroes" => true,
    "prefix" => "<~",
    "suffix" => "~>"
]);

print $adobe85->encode("Hello world!"); /* <~87cURD]j7BEbo80~> */
```

[ZeroMQ (Z85)](https://rfc.zeromq.org/spec:32/Z85/) encoding. NOTE! Even though specification says input length must be divisible by 4, this is not currently enforced. Spaces and zeroes are not compressed.

``` php
use Tuupola\Base85;

$z85 = new Base85([
    "characters" => Base85::Z85,
    "compress.spaces" => false,
    "compress.zeroes" => false
]);

print $z85->encode("Hello world!"); /* NM=qnZy<MXa+]NF */
```

Character set from [RFC1924](https://tools.ietf.org/html/rfc1924) which is an April fools joke. Spaces and zeroes are not compressed.

``` php
use Tuupola\Base85;

$rfc1924 = new Base85([
    "characters" => Base85::RFC1924,
    "compress.spaces" => false,
    "compress.zeroes" => false
]);

print $rfc1924->encode("Hello world!"); /* NM&qnZy<MXa%^NF */
```

## Speed

Pure PHP encoder seems to be faster than the GMP implementation. Below benchmarks use `random_bytes(128)` as data.

```
$ php --version
PHP 8.0.1 (cli) (built: Jan  8 2021 09:07:02) ( NTS )

$ vendor/bin/phpbench run benchmarks/ --report=default

+-----------------+-----------------+-------+
| subject         | mean            | diff  |
+-----------------+-----------------+-------+
| benchGmpDecoder | 24,515.692ops/s | 1.01x |
| benchPhpDecoder | 24,666.509ops/s | 1.00x |
+-----------------+-----------------+-------+
+-----------------+-----------------+-------+
| subject         | mean            | diff  |
+-----------------+-----------------+-------+
| benchGmpEncoder | 9,654.448ops/s  | 4.76x |
| benchPhpEncoder | 45,944.903ops/s | 1.00x |
+-----------------+-----------------+-------+
```

## Static Proxy

If you prefer static syntax use the provided static proxy.

```php
use Tuupola\Base85Proxy as Base85;

print Base85::encode("Hello world!") /* 87cURD]j7BEbo80 */
```

To change static proxy options set the `Base85::$options` variable.

```php
use Tuupola\Base85;
use Tuupola\Base85Proxy as Z85;

Z85::$options = [
    "characters" => Base85::Z85,
    "compress.spaces" => false,
    "compress.zeroes" => false
];

print Z85::encode("Hello world!"); /* NM=qnZy<MXa+]NF */
```

## Testing

You can run tests either manually or automatically on every code change. Automatic tests require [entr](http://entrproject.org/) to work.

``` bash
$ make test
```
``` bash
$ brew install entr
$ make watch
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email tuupola@appelsiini.net instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.txt) for more information.
