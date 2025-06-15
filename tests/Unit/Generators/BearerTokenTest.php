<?php

declare(strict_types=1);

use Chr15k\AuthGenerator\DataTransfer\BearerTokenData;
use Chr15k\AuthGenerator\Generators\BearerToken;

it('generates a bearer token', function (): void {
    $data = new BearerTokenData;

    $generator = new BearerToken($data);

    $token = $generator->generate();

    expect($token)->toBeString()
        ->and($token)->toStartWith($data->prefix)
        ->and(strlen($token))->toBeGreaterThanOrEqual($data->length + strlen($data->prefix));
});

it('generates a bearer token with custom length and prefix', function (): void {
    $data = new BearerTokenData(length: 64, prefix: 'custom_');

    $generator = new BearerToken($data);

    $token = $generator->generate();

    expect($token)->toBeString()
        ->and($token)->toStartWith($data->prefix)
        ->and(strlen($token))->toBeGreaterThanOrEqual($data->length + strlen($data->prefix));
});

it('throws an exception for invalid token length', function (): void {
    $data = new BearerTokenData(length: 16);

    $generator = new BearerToken($data);

    $generator->generate();
})->throws(DomainException::class, 'Token length must be between 32 and 128 bytes.');

it('throws an exception for invalid prefix length', function (): void {
    $data = new BearerTokenData(prefix: str_repeat('a', 11));

    $generator = new BearerToken($data);

    $generator->generate();
})->throws(DomainException::class, 'Prefix length must not exceed 10 characters.');

it('generates a bearer token with default values', function (): void {
    $data = new BearerTokenData;

    $generator = new BearerToken($data);

    $token = $generator->generate();

    expect($token)->toBeString()
        ->and($token)->toStartWith($data->prefix)
        ->and(strlen($token))->toBeGreaterThanOrEqual($data->length + strlen($data->prefix));
});
