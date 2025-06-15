<?php

declare(strict_types=1);

namespace Tests\Unit;

use Chr15k\AuthGenerator\AuthGenerator;

test('basic auth builder toHeader returns correct header string', function (): void {
    $formatted = AuthGenerator::basicAuth()
        ->username('user')
        ->password('pass')
        ->toHeader();

    expect($formatted)->toBe('Basic '.base64_encode('user:pass'));
});

test('bearer token builder toHeader returns correct header string', function (): void {
    // Since bearer token contains random content, just check the format
    $formatted = AuthGenerator::bearerToken()
        ->prefix('api_')
        ->length(32)
        ->toHeader();

    expect($formatted)->toStartWith('Bearer api_');
    expect(strlen($formatted))->toBeGreaterThan(40); // "Bearer api_" + 32 chars
});

test('jwt builder toHeader returns correct header string', function (): void {
    // Using a fixed key and claim for deterministic testing
    $builder = AuthGenerator::jwt()
        ->key('test-key')
        ->claim('user_id', 123);

    // Generate the token and header string using same builder configuration
    $token = $builder->toString();
    $formatted = $builder->toHeader();

    expect($formatted)->toBe('Bearer '.$token);
});

test('basic auth builder toArray returns correct headers', function (): void {
    $headers = AuthGenerator::basicAuth()
        ->username('user')
        ->password('pass')
        ->toArray();

    expect($headers)->toBe([
        'Authorization' => 'Basic '.base64_encode('user:pass'),
    ]);
});

test('bearer token builder toArray returns correct headers', function (): void {
    // We can't directly compare tokens since they're random
    // Instead, verify that the Authorization header exists and has the correct format
    $headers = AuthGenerator::bearerToken()
        ->prefix('api_')
        ->length(32)
        ->toArray();

    expect($headers)->toHaveKey('Authorization');
    expect($headers['Authorization'])->toStartWith('Bearer api_');
    expect(strlen($headers['Authorization']))->toBeGreaterThan(40); // "Bearer api_" + 32 chars
});

test('jwt builder toArray returns correct headers', function (): void {
    // Using a fixed key and claim for deterministic testing
    $builder = AuthGenerator::jwt()
        ->key('test-key')
        ->claim('user_id', 123);

    // Generate the token and headers using same builder configuration
    $token = $builder->toString();
    $headers = $builder->toArray();

    expect($headers['Authorization'])->toBe('Bearer '.$token);
});

test('builder toArray merges additional headers correctly', function (): void {
    $additionalHeaders = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ];

    $headers = AuthGenerator::basicAuth()
        ->username('user')
        ->password('pass')
        ->toArray($additionalHeaders);

    expect($headers)->toBe([
        'Authorization' => 'Basic '.base64_encode('user:pass'),
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ]);
});

test('basic auth builder toString returns correct token string', function (): void {
    $token = AuthGenerator::basicAuth()
        ->username('user')
        ->password('pass')
        ->toString();

    expect($token)->toBe(base64_encode('user:pass'));
});

test('bearer token builder toString returns correct token string', function (): void {
    // Since bearer token contains random content, just check the format
    $token = AuthGenerator::bearerToken()
        ->prefix('api_')
        ->length(32)
        ->toString();

    expect($token)->toStartWith('api_');
    expect(strlen($token))->toBeGreaterThan(32); // "api_" + 32 chars
});

test('jwt builder toString returns correct token string', function (): void {
    $token = AuthGenerator::jwt()
        ->key('test-key')
        ->claim('user_id', 123)
        ->toString();

    expect($token)->toBeString();
    expect(count(explode('.', $token)))->toBe(3);
});
