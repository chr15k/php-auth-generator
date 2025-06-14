<?php

declare(strict_types=1);

namespace Tests\Unit\Builders;

use Chr15k\Codec\Builders\JWTBuilder;
use Chr15k\Codec\Enums\Algorithm;
use Chr15k\Codec\Generators\JWT;

test('jwt builder creates data with correct key and algorithm', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->algorithm(Algorithm::HS256);

    $generator = $builder->build();

    expect($generator)->toBeInstanceOf(JWT::class);
    expect($builder->toString())->toBeString();
});

test('jwt builder adds claims correctly', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->claim('user_id', 123)
        ->claim('role', 'admin');

    $generator = $builder->build();

    expect($generator)->toBeInstanceOf(JWT::class);
    $token = $builder->toString();

    expect($token)->toBeString();
    expect(count(explode('.', $token)))->toBe(3);
});

test('jwt builder adds multiple claims at once', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->claims([
            'user_id' => 123,
            'role' => 'admin',
            'name' => 'Test User',
        ]);

    $generator = $builder->build();

    expect($generator)->toBeInstanceOf(JWT::class);
    $token = $builder->toString();

    expect($token)->toBeString();
    expect(count(explode('.', $token)))->toBe(3);
});

test('jwt builder adds standard claims correctly', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->expiresIn(3600)
        ->issuedBy('https://test.com')
        ->subject('user-123')
        ->audience('api');

    $generator = $builder->build();

    expect($generator)->toBeInstanceOf(JWT::class);
    $token = $builder->toString();

    expect($token)->toBeString();
    expect(count(explode('.', $token)))->toBe(3);
});

test('jwt builder can add unique jwt id', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->withUniqueJwtId();

    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);

    $token = $builder->toString();
    expect($token)->toBeString();
});

test('jwt builder can set not-before time', function (): void {
    $tomorrow = time() + 86400;

    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->notBefore($tomorrow);

    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);

    $token = $builder->toString();
    expect($token)->toBeString();
});

test('jwt builder can add all timestamp claims at once', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->withTimestampClaims(3600);

    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);

    $token = $builder->toString();
    expect($token)->toBeString();
});

test('jwt builder can add multiple headers at once', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->headers([
            'kid' => 'key-123',
            'typ' => 'JWT',
            'custom' => 'value',
        ]);

    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);

    $token = $builder->toString();
    expect($token)->toBeString();
    expect(count(explode('.', $token)))->toBe(3);
});

test('jwt builder can add specific jwt id', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->withJwtId('custom-id-123');

    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);

    $token = $builder->toString();
    expect($token)->toBeString();
});

test('jwt builder sets default not-before time when not specified', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->withTimestampClaims(3600, null); // Explicitly pass null for notBefore

    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);

    $token = $builder->toString();
    expect($token)->toBeString();
});

test('jwt builder sets not-before', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->withTimestampClaims(3600, time());

    $generator = $builder->build();
    expect($generator)->toBeInstanceOf(JWT::class);

    $token = $builder->toString();
    expect($token)->toBeString();
});

test('jwt builder can generate formatted header string', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->claim('user_id', 123);

    $formatted = $builder->toHeader();

    expect($formatted)->toBeString();
    expect($formatted)->toStartWith('Bearer ');
});

test('jwt builder toString returns correct token string', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->claim('user_id', 123);

    $token = $builder->toString();

    expect($token)->toBeString();
    expect(count(explode('.', $token)))->toBe(3);
});

test('jwt builder toArray returns correct headers', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->claim('user_id', 123);

    $headers = $builder->toArray();

    expect($headers)->toHaveKey('Authorization');
    expect($headers['Authorization'])->toStartWith('Bearer ');
});

test('jwt builder toHeader returns correct header string', function (): void {
    $builder = new JWTBuilder;
    $builder
        ->key('test-key')
        ->claim('user_id', 123);

    $header = $builder->toHeader();

    expect($header)->toBeString();
    expect($header)->toStartWith('Bearer ');
});
