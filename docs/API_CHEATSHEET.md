# HTTP Auth Generator API Cheat Sheet

## Basic Authentication

```php
use Chr15k\AuthGenerator\AuthGenerator;

// Generate token
$token = AuthGenerator::basicAuth()
    ->username('user')
    ->password('pass')
    ->toString();

// Format for HTTP header
$header = AuthGenerator::basicAuth()
    ->username('user')
    ->password('pass')
    ->toHeader();
// Output: "Basic dXNlcjpwYXNz"
```

## Bearer Token

```php
use Chr15k\AuthGenerator\AuthGenerator;

// Generate token with default settings (32 bytes, 'brr_' prefix)
$token = AuthGenerator::bearerToken()->toString();

// Generate token with custom settings
$token = AuthGenerator::bearerToken()
    ->length(64)         // Between 32-128 bytes
    ->prefix('api_')     // Max 10 characters
    ->toString();

// Format for HTTP header
$header = AuthGenerator::bearerToken()
    ->length(64)
    ->prefix('api_')
    ->toHeader();
// Output: "Bearer api_8f7d49b3..."
```

## JWT (JSON Web Token)

```php
use Chr15k\AuthGenerator\AuthGenerator;
use Chr15k\AuthGenerator\Enums\Algorithm;

// Generate token with default settings (HS256 algorithm)
$token = AuthGenerator::jwt()
    ->key('secret-key')
    ->claim('user_id', 123)
    ->toString();

// Generate token with more options
$token = AuthGenerator::jwt()
    ->key('secret-key', false)         // Key is not base64 encoded
    ->algorithm(Algorithm::HS256)      // Use HMAC-SHA256
    ->claim('user_id', 123)            // Add individual claim
    ->claims([                         // Add multiple claims at once
        'name' => 'John Doe',
        'admin' => true,
    ])
    // Standard JWT Claims
    ->expiresIn(3600)                  // Token expires in 1 hour (adds iat, exp)
    ->issuedBy('https://myapp.com')    // Add issuer (iss) claim
    ->subject('user-123')              // Add subject (sub) claim
    ->audience('api')                  // Add audience (aud) claim - can also pass array
    ->notBefore(time())                // Set time when token becomes valid (nbf)
    ->withJwtId('unique-id-123')       // Add JWT ID (jti) claim
    ->withUniqueJwtId()                // Generate random JWT ID (jti)
    ->withTimestampClaims(3600)        // Add iat, exp, and nbf claims at once
    ->header('kid', 'key-1')           // Add custom header
    ->toString();

// Format for HTTP header
$header = AuthGenerator::jwt()
    ->key('secret-key')
    ->claim('user_id', 123)
    ->toHeader();
// Output: "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
```

## HTTP Headers Formatting

```php
use Chr15k\AuthGenerator\AuthGenerator;

// Generate token and headers in one go
$headers = AuthGenerator::basicAuth()
    ->username('user')
    ->password('pass')
    ->toArray([
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ]);

// Bearer token headers
$headers = AuthGenerator::bearerToken()
    ->length(64)
    ->toArray();

// JWT headers
$headers = AuthGenerator::jwt()
    ->key('secret-key')
    ->claim('user_id', 123)
    ->toArray();

// Fluent formatting with toHeader() method
$header = AuthGenerator::jwt()
    ->key('secret-key')
    ->toHeader();
// Output: "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9..."
```

## JWT Algorithms

The following algorithms are supported for JWT tokens:

| Algorithm | Description |
|-----------|-------------|
| Algorithm::HS256 | HMAC using SHA-256 |
| Algorithm::HS384 | HMAC using SHA-384 |
| Algorithm::HS512 | HMAC using SHA-512 |
| Algorithm::RS256 | RSASSA-PKCS1-v1_5 using SHA-256 |
| Algorithm::RS384 | RSASSA-PKCS1-v1_5 using SHA-384 |
| Algorithm::RS512 | RSASSA-PKCS1-v1_5 using SHA-512 |
| Algorithm::ES256 | ECDSA using P-256 curve and SHA-256 |
| Algorithm::ES384 | ECDSA using P-384 curve and SHA-384 |
| Algorithm::ES256K | ECDSA using secp256k1 curve and SHA-256 |
| Algorithm::EdDSA | Edwards-curve Digital Signature Algorithm (EdDSA) |
