# PHP Auth Generator

A PHP library that focuses exclusively on **generating** HTTP authentication tokens, including Basic Auth, Bearer tokens, and JWTs with a fluent API. Built with zero dependencies, it's lightweight and adds token creation capabilities without bloating your project.

> [!IMPORTANT]
> This package is designed solely for **generating** authentication tokens. It does not include any token decoding, validation, or verification functionality.

> [!TIP]
> For complete JWT encoding/decoding solutions, consider using a dedicated library such as [firebase/php-jwt](https://github.com/firebase/php-jwt)

## Installation

> [!NOTE]
> Requires [PHP 8.2+](https://www.php.net/releases/)

```bash
composer require chr15k/php-auth-generator
```

## Usage

### Basic Auth

```php
use Chr15k\AuthGenerator\AuthGenerator;

// Generate a Basic Auth token
$token = AuthGenerator::basicAuth()
    ->username('user')
    ->password('pass')
    ->toString();

// Output: dXNlcjpwYXNz
```

### Bearer Token

```php
use Chr15k\AuthGenerator\AuthGenerator;

// Generate a Bearer token with custom length and prefix
$token = AuthGenerator::bearerToken()
    ->length(64)
    ->prefix('api_')
    ->toString();

// Output: api_8f7d49b3c70e4...
```

### JWT

```php
use Chr15k\AuthGenerator\AuthGenerator;
use Chr15k\AuthGenerator\Enums\Algorithm;

// Generate a JWT with claims and custom algorithm
$token = AuthGenerator::jwt()
    ->key('secret-key')
    ->algorithm(Algorithm::HS256)
    ->claim('user_id', 123)
    ->claim('role', 'admin')
    ->expiresIn(3600) // 1 hour
    ->issuedBy('https://myapp.com')
    ->toString();

// Output: eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VyX2lkIjoxMjMsInJvbGUiOiJhZG1pbiIsImlhdCI6MTY...
```

## Headers and Formatting

The library provides fluent methods to format tokens for use in HTTP headers:

```php
use Chr15k\AuthGenerator\AuthGenerator;

// Generate a Basic Auth token and get headers in one go
$headers = AuthGenerator::basicAuth()
    ->username('user')
    ->password('pass')
    ->toArray([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ]);
/*
[
    'Authorization' => 'Basic dXNlcjpwYXNz',
    'Content-Type' => 'application/json',
    'Accept' => 'application/json',
]
*/

// For Bearer tokens
$headers = AuthGenerator::bearerToken()
    ->length(64)
    ->prefix('api_')
    ->toArray();
// Output: ['Authorization' => 'Bearer api_8f7d49b3...']

// For JWT tokens
$headers = AuthGenerator::jwt()
    ->key('secret-key')
    ->claim('user_id', 123)
    ->toArray([
        'Content-Type' => 'application/json',
    ]);

// Get the raw token string
$token = AuthGenerator::basicAuth()
    ->username('user')
    ->password('pass')
    ->toString();
// Output: 'dXNlcjpwYXNz'

// Format tokens directly to header strings with the fluent toHeader() method
$headerString = AuthGenerator::basicAuth()
    ->username('user')
    ->password('pass')
    ->toHeader();
// Output: 'Basic dXNlcjpwYXNz'
```

## HTTP Client Integration

You can easily integrate with popular HTTP clients:

### Guzzle

```php
use Chr15k\AuthGenerator\AuthGenerator;
use GuzzleHttp\Client;

// Create Guzzle client with authentication headers
$client = new Client([
    'base_uri' => 'https://api.example.com',
    'headers' => AuthGenerator::jwt()
        ->key('secret-key')
        ->claim('user_id', 123)
        ->expiresIn(3600)
        ->toArray([
            'Content-Type' => 'application/json',
        ]),
]);

$response = $client->get('/resources');
```

## Advanced Usage

You can access the underlying generator instance if needed:

```php
$generator = AuthGenerator::basicAuth()
    ->username('user')
    ->password('pass')
    ->build();

// Now you can work directly with the generator
$token = $generator->generate();
```

## Documentation

- [User Guide](docs/USER_GUIDE.md) - Comprehensive guide with examples
- [API Cheat Sheet](docs/API_CHEATSHEET.md) - Quick reference of all available methods
