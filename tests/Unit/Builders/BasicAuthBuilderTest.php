<?php

declare(strict_types=1);

namespace Tests\Unit\Builders;

use Chr15k\AuthGenerator\Builders\BasicAuthBuilder;
use Chr15k\AuthGenerator\Generators\BasicAuth;
use DomainException;

test('basic auth builder creates data with correct username and password', function (): void {
    $builder = new BasicAuthBuilder;
    $builder->username('testuser')->password('testpass');

    $generator = $builder->build();

    expect($generator)->toBeInstanceOf(BasicAuth::class);
    expect($builder->toString())->toBe(base64_encode('testuser:testpass'));
});

test('basic auth builder throws exception if username is empty', function (): void {
    $builder = new BasicAuthBuilder;
    $builder->password('testpass');

    expect(fn (): string => $builder->toString())->toThrow(DomainException::class, 'Username cannot be empty.');
});

test('basic auth builder can generate formatted header string', function (): void {
    $builder = new BasicAuthBuilder;
    $builder->username('testuser')->password('testpass');

    $formatted = $builder->toHeader();

    expect($formatted)->toBe('Basic '.base64_encode('testuser:testpass'));
});

test('basic auth builder toString returns correct token string', function (): void {
    $builder = new BasicAuthBuilder;
    $builder->username('testuser')->password('testpass');

    $token = $builder->toString();

    expect($token)->toBe(base64_encode('testuser:testpass'));
});

test('basic auth builder toArray returns correct headers', function (): void {
    $builder = new BasicAuthBuilder;
    $builder->username('testuser')->password('testpass');

    $headers = $builder->toArray();

    expect($headers)->toBe(['Authorization' => 'Basic '.base64_encode('testuser:testpass')]);
});

test('basic auth builder toHeader returns correct header string', function (): void {
    $builder = new BasicAuthBuilder;
    $builder->username('testuser')->password('testpass');

    $header = $builder->toHeader();

    expect($header)->toBe('Basic '.base64_encode('testuser:testpass'));
});
