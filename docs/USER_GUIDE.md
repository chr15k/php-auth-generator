# HTTP Auth Generator - User Guide

This guide provides a comprehensive overview of how to use the HTTP Auth Generator library for generating authentication tokens.

## Table of Contents

1. [Installation](#installation)
2. [Basic Auth](#basic-auth)
3. [Bearer Tokens](#bearer-tokens)
4. [Digest Auth](#digest-auth)
5. [JWT (JSON Web Tokens)](#jwt-json-web-tokens)
6. [HTTP Headers](#http-headers)
7. [HTTP Client Integration](#http-client-integration)
8. [Advanced Usage](#advanced-usage)

## Installation

```bash
composer require chr15k/php-auth-generator
```

## Basic Auth

Basic Authentication (Basic Auth) is a simple authentication scheme built into the HTTP protocol. The client sends HTTP requests with an Authorization header containing the word `Basic` followed by a space and a base64-encoded string of `username:password`.

### Generating a Basic Auth Token

```php
use Chr15k\AuthGenerator\AuthGenerator;

// Generate a Basic Auth token
$token = AuthGenerator::basicAuth()
    ->username('user')
    ->password('pass')
    ->toString();

// Output: dXNlcjpwYXNz (base64 encoded version of "user:pass")
```

### Generating Headers Directly

```php
// Generate token and get complete headers in one go
$headers = AuthGenerator::basicAuth()
    ->username('user')
    ->password('pass')
    ->toArray([
        'Content-Type' => 'application/json'
    ]);
// Output: [
//     'Authorization' => 'Basic dXNlcjpwYXNz',
//     'Content-Type'  => 'application/json'
// ];

// Alternatively, format for use in the Authorization header
$token = AuthGenerator::basicAuth()
    ->username('user')
    ->password('pass')
    ->toString();

// Format token directly to a complete authorization header string with the fluent toHeader() method
$header = AuthGenerator::basicAuth()
    ->username('user')
    ->password('pass')
    ->toHeader();
// Output: "Basic dXNlcjpwYXNz"
```

## Bearer Tokens

Bearer tokens are commonly used for API authentication. The token is a cryptographically random string that is used as an access credential.

### Generating a Bearer Token

```php
use Chr15k\AuthGenerator\AuthGenerator;

// Generate a Bearer token with default settings
$token = AuthGenerator::bearerToken()->toString();
// Output: "brr_8f7d49b3c70e4..."

// Generate a Bearer token with custom settings
$token = AuthGenerator::bearerToken()
    ->length(64)       // Must be between 32-128
    ->prefix('api_')   // Must be 10 characters or less
    ->toString();
// Output: "api_8f7d49b3c70e4..."
```

### Formatting for HTTP Headers

```php
// Format for use in the Authorization header
$header = AuthGenerator::bearerToken()
    ->length(64)
    ->prefix('api_')
    ->toHeader();
// Output: "Bearer api_8f7d49b3c70e4..."
```

## Digest Auth

Digest Authentication is an authentication mechanism that improves upon Basic Authentication by avoiding sending the password in plaintext over the network. It uses a challenge-response mechanism and cryptographic hashing (MD5 or SHA-256).

The Digest authentication process typically involves:
1. A server challenge containing a nonce value
2. A client response with a cryptographic hash that proves password knowledge without revealing it
3. The response includes various components like realm, nonce, URI, and algorithm details

This implementation provides a convenient way to generate properly formatted Digest Auth tokens for HTTP requests. It supports all RFC 2617 and RFC 7616 algorithm variants including MD5, MD5-sess, SHA-256, and SHA-256-sess.

### Generating a Digest Auth Token

```php
use Chr15k\AuthGenerator\AuthGenerator;
use Chr15k\AuthGenerator\Enums\DigestAlgorithm;

// Generate a Digest Auth token
$token = AuthGenerator::digestAuth()
    ->username('user')
    ->password('pass')
    ->realm('example.com')
    ->uri('/protected-resource')
    ->method('GET')
    ->algorithm(DigestAlgorithm::MD5)
    ->toString();

// Output: username="user", realm="example.com", nonce="1234abcd...", uri="/protected-resource", algorithm="MD5" response="a2fc57d9..."
```

### Working with Different Digest Algorithms

```php
// Using MD5 algorithm (default)
$token = AuthGenerator::digestAuth()
    ->username('user')
    ->password('pass')
    ->realm('example.com')
    ->algorithm(DigestAlgorithm::MD5)
    ->toString();

// Using SHA-256 algorithm
$token = AuthGenerator::digestAuth()
    ->username('user')
    ->password('pass')
    ->realm('example.com')
    ->algorithm(DigestAlgorithm::SHA256)
    ->toString();

// Using MD5 Session variant
$token = AuthGenerator::digestAuth()
    ->username('user')
    ->password('pass')
    ->realm('example.com')
    ->algorithm(DigestAlgorithm::MD5_SESS)
    ->toString();
```

### Additional Digest Auth Options

```php
$token = AuthGenerator::digestAuth()
    ->username('user')
    ->password('pass')
    ->realm('example.com')
    ->uri('/api/resource')
    ->method('POST') // HTTP method
    ->nonce('custom-nonce-value') // Custom server nonce
    ->clientNonce('client-nonce') // Client nonce for session algorithms
    ->nonceCount('00000001') // Nonce count
    ->qop('auth') // Quality of protection
    ->opaque('server-opaque-value') // Server opaque value
    ->toString();
```

### Formatting for HTTP Headers

```php
// Format for use in the Authorization header
$header = AuthGenerator::digestAuth()
    ->username('user')
    ->password('pass')
    ->realm('example.com')
    ->toHeader();
// Output: "Digest username="user", realm="example.com", ..."
```

## JWT (JSON Web Tokens)

JWT is an open standard that defines a compact and self-contained way for securely transmitting information between parties as a JSON object.

### Generating a JWT

```php
use Chr15k\AuthGenerator\AuthGenerator;
use Chr15k\AuthGenerator\Enums\Algorithm;

// Generate a simple JWT
$token = AuthGenerator::jwt()
    ->key('secret-key')
    ->claim('user_id', 123)
    ->toString();

// Generate a more complex JWT
$token = AuthGenerator::jwt()
    ->key('secret-key', false)         // Key is not base64 encoded
    ->algorithm(Algorithm::HS256)      // Use HMAC-SHA256
    ->claim('user_id', 123)
    ->claims([
        'name' => 'John Doe',
        'admin' => true,
    ])
    ->expiresIn(3600)                  // Token expires in 1 hour
    ->issuedBy('https://myapp.com')
    ->subject('user-123')
    ->audience('api')
    ->toString();
```

### Advanced JWT Features

```php
use Chr15k\AuthGenerator\AuthGenerator;

$token = AuthGenerator::jwt()
    ->key('secret-key')
    // Timestamp claims
    ->withTimestampClaims(3600)        // Sets iat, exp, and nbf all at once
    ->notBefore(time() + 60)           // Token not valid until 60 seconds from now
    // Unique identifier to prevent token replay
    ->withUniqueJwtId()                // Adds a random JWT ID
    // Custom headers
    ->header('kid', 'key-1')           // Key ID
    ->toString();
```

### Supported Algorithms

The library supports various algorithms for JWT signing:

```php
use Chr15k\AuthGenerator\Enums\Algorithm;

// HMAC algorithms
$token = AuthGenerator::jwt()->algorithm(Algorithm::HS256)->toString(); // HMAC with SHA-256
$token = AuthGenerator::jwt()->algorithm(Algorithm::HS384)->toString(); // HMAC with SHA-384
$token = AuthGenerator::jwt()->algorithm(Algorithm::HS512)->toString(); // HMAC with SHA-512

// RSA algorithms
$token = AuthGenerator::jwt()->algorithm(Algorithm::RS256)->toString(); // RSA with SHA-256
$token = AuthGenerator::jwt()->algorithm(Algorithm::RS384)->toString(); // RSA with SHA-384
$token = AuthGenerator::jwt()->algorithm(Algorithm::RS512)->toString(); // RSA with SHA-512

// ECDSA algorithms
$token = AuthGenerator::jwt()->algorithm(Algorithm::ES256)->toString(); // ECDSA with P-256 and SHA-256
$token = AuthGenerator::jwt()->algorithm(Algorithm::ES384)->toString(); // ECDSA with P-384 and SHA-384
$token = AuthGenerator::jwt()->algorithm(Algorithm::ES256K)->toString(); // ECDSA with secp256k1 and SHA-256

// Edwards-curve Digital Signature Algorithm
$token = AuthGenerator::jwt()->algorithm(Algorithm::EdDSA)->toString(); // EdDSA
```

## HTTP Headers

The library provides helper methods to format tokens for use in HTTP headers.

```php
use Chr15k\AuthGenerator\AuthGenerator;

// Format a token directly to a proper authorization header using the fluent API
$header = AuthGenerator::bearerToken()
    ->prefix('api_')
    ->length(48)
    ->toHeader();
// Output: "Bearer api_8f7d49b3c70e4..."

// Get complete headers array for HTTP clients with the fluent API
$headers = AuthGenerator::bearerToken()
    ->prefix('api_')
    ->length(48)
    ->toArray([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ]);
```

## HTTP Client Integration

The library can be integrated with various HTTP clients.

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

### Symfony HTTP Client

```php
use Chr15k\AuthGenerator\AuthGenerator;
use Symfony\Component\HttpClient\HttpClient;

// Create Symfony HTTP client with authentication headers
$client = HttpClient::create([
    'headers' => AuthGenerator::basicAuth()
        ->username('api_user')
        ->password('secret_password')
        ->toArray([
            'Content-Type' => 'application/json',
        ]),
]);

$response = $client->request('GET', 'https://api.example.com/resources');
```

### Laravel HTTP Client

```php
use Chr15k\AuthGenerator\AuthGenerator;
use Illuminate\Support\Facades\Http;

// Make request with Laravel HTTP client and Bearer token
$response = Http::withHeaders(
    AuthGenerator::bearerToken()
        ->length(48)
        ->prefix('api_')
        ->toArray([
            'Accept' => 'application/json',
        ])
)->get('https://api.example.com/resources');

// Using Digest Authentication
$response = Http::withHeaders(
    AuthGenerator::digestAuth()
        ->username('api_user')
        ->password('secure_password')
        ->realm('api.example.com')
        ->uri('/protected-resource')
        ->method('GET')
        ->toArray([
            'Accept' => 'application/json',
        ])
)->get('https://api.example.com/protected-resource');
```

## Advanced Usage

If you need more control, you can access the underlying generator instance.

```php
use Chr15k\AuthGenerator\AuthGenerator;
use Chr15k\AuthGenerator\Enums\DigestAlgorithm;

// Get the generator instance (Basic Auth example)
$generator = AuthGenerator::basicAuth()
    ->username('user')
    ->password('pass')
    ->build();

// Now you can work directly with the generator
$token = $generator->generate();

// Advanced Digest Auth example
$digestGenerator = AuthGenerator::digestAuth()
    ->username('user')
    ->password('pass')
    ->realm('example.com')
    ->uri('/api/resource')
    ->algorithm(DigestAlgorithm::SHA256)
    ->build();

// Generate the digest token directly
$digestToken = $digestGenerator->generate();
```


