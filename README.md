# All your Base85

[![Latest Version](https://img.shields.io/packagist/v/tuupola/base85.svg?style=flat-square)](https://packagist.org/packages/tuupola/base85)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/tuupola/base85/master.svg?style=flat-square)](https://travis-ci.org/tuupola/base85)
[![HHVM Status](https://img.shields.io/hhvm/tuupola/base85.svg?style=flat-square)](http://hhvm.h4cc.de/package/tuupola/base85)
[![Coverage](http://img.shields.io/codecov/c/github/tuupola/base85.svg?style=flat-square)](https://codecov.io/github/tuupola/base85)

## Install

Install with [composer](https://getcomposer.org/).

``` bash
$ composer require tuupola/base85
```

## Usage

This package has both pure PHP and [GMP](http://php.net/manual/en/ref.gmp.php) based encoders. By default encoder and decoder will use GMP functions if the extension is installed. If GMP is not available pure PHP encoder will be used instead.

``` php
use Tuupola\Base85;

$encoded = Base85::encode(random_bytes(128));
$decoded = Base85::decode($encoded);
```

Or if you prefer to use object syntax.

``` php
use Tuupola\Base85\Encoder as Base85;

$base85 = new Base85;

$encoded = $base85->encode(random_bytes(128));
$decoded = $base85->decode($encoded);
```

## Testing

You can run tests either manually...

``` bash
$ composer test
```

... or automatically on every code change. This requires [entr](http://entrproject.org/) to work.

``` bash
$ composer watch
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email tuupola@appelsiini.net instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
