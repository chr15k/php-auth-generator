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

### Working with Complex Data

JWT claims can contain complex nested data structures:

```php
$token = AuthGenerator::jwt()
    ->key('secret-key')
    ->claim('user', [
        'id' => 123,
        'name' => 'John Doe',
        'roles' => ['admin', 'user'],
        'permissions' => [
            'read' => true,
            'write' => true,
            'admin' => ['users', 'settings']
        ]
    ])
    ->claim('metadata', [
        'ip' => '192.168.1.1',
        'device' => 'mobile',
        'location' => ['city' => 'New York', 'country' => 'US']
    ])
    ->toString();
```

### Data Type Rules

The library enforces different validation rules for headers vs claims:

**Headers** - Must be scalar values (strings, numbers, booleans):
```php
$jwt->header('kid', 'key-123');      // ✅ String
$jwt->header('version', 1);          // ✅ Integer
$jwt->header('debug', true);         // ✅ Boolean
$jwt->header('invalid', []);         // ❌ Arrays not allowed
$jwt->header('optional', null);      // ℹ️ Null values are ignored
```

**Claims** - Can be any JSON-serializable data:
```php
$jwt->claim('simple', 123);          // ✅ Scalar values
$jwt->claim('array', [1, 2, 3]);     // ✅ Arrays
$jwt->claim('nested', ['a' => ['b' => 'c']]); // ✅ Nested structures
$jwt->claim('invalid', fopen('file', 'r'));   // ❌ Resources not allowed
```

### Validation Alignment with RFC Standards

The library's validation rules are designed to ensure compliance with JWT standards:

**RFC 7515 (JSON Web Signature) - Headers:**
- Headers contain algorithm parameters and metadata for token processing
- Values should be simple strings or numbers for interoperability
- Complex data structures in headers can cause compatibility issues with other JWT libraries
- Our validation enforces scalar types (strings, numbers, booleans) to maintain standard compliance

**RFC 7519 (JSON Web Token) - Claims:**
- Claims are designed to convey assertions about an entity (typically the user)
- The payload can contain any valid JSON data structure
- Standard claims like `iss`, `sub`, `aud` can be strings or arrays
- Custom claims can be any JSON-serializable data to support application-specific requirements
- Our validation allows complex data structures while preventing non-serializable types

**Example of RFC-compliant usage:**
```php
$jwt = AuthGenerator::jwt()
    ->key('secret-key')
    // RFC 7519 standard claims
    ->claim('iss', 'https://myapp.com')           // Issuer (string)
    ->claim('sub', 'user-123')                    // Subject (string)
    ->claim('aud', ['api', 'mobile'])             // Audience (string or array)
    ->claim('exp', time() + 3600)                 // Expiration (number)
    ->claim('iat', time())                        // Issued at (number)
    // Custom claims with complex data (RFC compliant)
    ->claim('permissions', ['read', 'write'])     // Array
    ->claim('profile', ['name' => 'John'])        // Object
    // RFC 7515 compliant headers
    ->header('kid', 'key-id-123')                 // Key ID (string)
    ->header('typ', 'JWT')                        // Type (string)
    ->toString();
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


