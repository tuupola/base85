# Changelog

All notable changes to this project will be documented in this file, in reverse chronological order by release.

## [0.4.0](https://github.com/tuupola/base85/compare/0.3.1...0.4.0) - unreleased

### Added
- Implicit `decodeInteger()` and `encodeInteger()` methods ([#3](https://github.com/tuupola/base85/pull/3/files)).

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
