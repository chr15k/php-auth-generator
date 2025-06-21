# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [v0.1.3] - 2025-06-21

### Changed
- Readme

## [v0.1.2] - 2025-06-16

### Added
- Added Digest Authentication support with MD5, MD5-sess, SHA-256, and SHA-256-sess algorithms
- Added DigestAuthBuilder with fluent API for generating Digest Auth tokens

## [v0.1.1] - 2025-06-14

### Added
- Added #[SensitiveParameter] attribute to enhance security for password and secret parameters

### Changed
- Enhanced type safety by making DTOs readonly
- Updated documentation in README for clearer usage instructions
- Added libsodium suggestion in composer.json for improved cryptographic operations (required for EdDSA support)

## [v0.1.0] - 2025-06-13

### Added
- Initial release of the HTTP Auth Generator library
- Fluent API for generating authentication tokens
- Support for Basic Authentication tokens
- Support for Bearer tokens with customizable length and prefix
- Support for JWT (JSON Web Tokens) with extensive claim and header options
- Support for multiple JWT signing algorithms (HS256, HS384, HS512, RS256, RS384, RS512, ES256, ES384, ES256K, EdDSA)
- Helper methods for formatting tokens for HTTP headers: `toString()`, `toArray()`, and `toHeader()`
- Comprehensive documentation in README, API cheatsheet, and user guide
- Integration examples for popular HTTP clients (Guzzle, Symfony HTTP, Laravel HTTP)
- Extensive test suite with high code coverage
