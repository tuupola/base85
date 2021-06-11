# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## [2.0.0](https://github.com/tuupola/base85/compare/1.0.0...2.0.0) - 2021-06-11

### Added
- Allow installing with PHP 8 ([#8](https://github.com/tuupola/base85/pull/8)).

### Changed
- PHP 7.1 is now minimum requirement
- All methods have return types ([#14](https://github.com/tuupola/base85/pull/14)).
- All methods are typehinted ([#14](https://github.com/tuupola/base85/pull/14)).
- All type juggling is removed ([#14](https://github.com/tuupola/base85/pull/14)).
- InvalidArgumentException is now thrown if trying to decode empty string as integer ([#18](https://github.com/tuupola/base85/pull/18)).
### Fixed
- Autoselection was always choosing the PHP encoder([#15](https://github.com/tuupola/base85/pull/15)).

## [1.0.0](https://github.com/tuupola/base85/compare/0.4.0...1.0.0) - 2021-02-01

This is same as previous but released as stable.

## [0.4.0](https://github.com/tuupola/base85/compare/0.3.1...0.4.0) - 2019-04-27

### Added
- Implicit `decodeInteger()` and `encodeInteger()` methods ([#3](https://github.com/tuupola/base85/pull/3/files)).
- Character set validation for both configuration and data going to be encoded ([#5](https://github.com/tuupola/base85/pull/5/files)).

### Removed
- The unused and undocumented `$options` parameter from static proxy methods ([#6](https://github.com/tuupola/base85/pull/6/files)).


## [0.3.1](https://github.com/tuupola/base85/compare/0.3.0...0.3.1) - 2017-04-01

### Removed
- Removed final keyword from classes.

## [0.3.0](https://github.com/tuupola/base85/compare/0.2.0...0.3.0) - 2017-02-28

### Added
- New `prefix` and `suffix` setting to support Adobe85 mode.
- Static proxy to optionally enable `Base85::decode($data)` and `Base85::encode($data)` style usage.

## [0.2.0](https://github.com/tuupola/base85/compare/0.1.0...0.2.0) - 2017-02-28

### Removed
- Static functions ie `Base85::decode($data)` and `Base85::encode($data)`.

## 0.1.0 - 2017-02-20

Initial realese.
