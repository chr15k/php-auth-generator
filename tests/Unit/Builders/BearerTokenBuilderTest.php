<?php

declare(strict_types=1);

namespace Tests\Unit\Builders;

use Chr15k\AuthGenerator\Builders\BearerTokenBuilder;
use Chr15k\AuthGenerator\Generators\BearerToken;
use DomainException;

test('bearer token builder creates data with correct length and prefix', function (): void {
    $builder = new BearerTokenBuilder;
    $builder->length(32)->prefix('test_');

    $generator = $builder->build();

    expect($generator)->toBeInstanceOf(BearerToken::class);
    $token = $builder->toString();

    expect($token)->toStartWith('test_');
    expect(strlen($token) > 32)->toBeTrue();
});

test('bearer token builder throws exception for invalid length', function (): void {
    $builder = new BearerTokenBuilder;
    $builder->length(16); // Too small

    expect(fn (): string => $builder->toString())->toThrow(DomainException::class, 'Token length must be between 32 and 128 bytes.');
});

test('bearer token builder throws exception for long prefix', function (): void {
    $builder = new BearerTokenBuilder;
    $builder->prefix('this_is_too_long_prefix_'); // More than 10 chars

    expect(fn (): string => $builder->toString())->toThrow(DomainException::class, 'Prefix length must not exceed 10 characters.');
});

test('bearer token builder can generate formatted header string', function (): void {
    $builder = new BearerTokenBuilder;
    $builder->length(32)->prefix('test_');

    $formatted = $builder->toHeader();

    expect($formatted)->toBeString();
    expect($formatted)->toStartWith('Bearer test_');
    expect(strlen($formatted))->toBeGreaterThan(38); // "Bearer test_" + 32 chars
});

test('bearer token builder toString returns correct token string', function (): void {
    $builder = new BearerTokenBuilder;
    $builder->length(32)->prefix('test_');

    $token = $builder->toString();

    expect($token)->toBeString();
    expect($token)->toStartWith('test_');
});

test('bearer token builder toArray returns correct headers', function (): void {
    $builder = new BearerTokenBuilder;
    $builder->length(32)->prefix('test_');

    $headers = $builder->toArray();

    expect($headers)->toHaveKey('Authorization');
    expect($headers['Authorization'])->toStartWith('Bearer test_');
});

test('bearer token builder toHeader returns correct header string', function (): void {
    $builder = new BearerTokenBuilder;
    $builder->length(32)->prefix('test_');

    $header = $builder->toHeader();

    expect($header)->toBeString();
    expect($header)->toStartWith('Bearer test_');
    expect(strlen($header))->toBeGreaterThan(38); // "Bearer test_" + 32 chars
});
