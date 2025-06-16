<?php

declare(strict_types=1);

namespace Tests\Feature;

use Chr15k\AuthGenerator\AuthGenerator;

test('basic auth builder can generate correct token', function (): void {
    $token = AuthGenerator::basicAuth()
        ->username('testuser')
        ->password('testpass')
        ->toString();

    expect($token)->toBe(base64_encode('testuser:testpass'));
});

test('bearer token builder can generate token with custom settings', function (): void {
    $token = AuthGenerator::bearerToken()
        ->length(32)
        ->prefix('test_')
        ->toString();

    expect($token)->toStartWith('test_');
    expect(strlen($token) > 32)->toBeTrue();
});

test('jwt builder can generate token with claims', function (): void {
    $token = AuthGenerator::jwt()
        ->key('secret-test-key')
        ->claim('user_id', 123)
        ->claim('role', 'user')
        ->expiresIn(3600)
        ->toString();

    expect($token)->toBeString();
    // JWT should have 3 parts separated by dots
    expect(count(explode('.', $token)))->toBe(3);
});

test('jwt builder can add multiple claims at once', function (): void {
    $claims = [
        'user_id' => 123,
        'name' => 'Test User',
        'role' => 'admin',
    ];

    $token = AuthGenerator::jwt()
        ->key('secret-test-key')
        ->claims($claims)
        ->toString();

    expect($token)->toBeString();
    expect(count(explode('.', $token)))->toBe(3);
});

test('digest auth builder can generate correct header', function (): void {
    $token = AuthGenerator::digestAuth()
        ->username('testuser')
        ->password('testpass')
        ->realm('example.com')
        ->uri('/api/resource')
        ->toString();

    expect($token)->toBeString();
    expect($token)->toContain('username="testuser"');
    expect($token)->toContain('realm="example.com"');
    expect($token)->toContain('uri="/api/resource"');
});
