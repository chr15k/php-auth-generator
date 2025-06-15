<?php

declare(strict_types=1);

use Chr15k\AuthGenerator\DataTransfer\BasicAuthData;
use Chr15k\AuthGenerator\Generators\BasicAuth;

it('generates encoded basic auth token', function (): void {
    $data = new BasicAuthData(
        username: 'testuser',
        password: 'testpass'
    );

    $generator = new BasicAuth($data);

    $token = $generator->generate();

    expect($token)
        ->toBeString()
        ->and($token)
        ->toBe(base64_encode('testuser:testpass'));
});

it('throws an exception for empty username', function (): void {
    $data = new BasicAuthData(username: '');

    $generator = (new BasicAuth($data));

    $generator->generate();
})->throws(DomainException::class, 'Username cannot be empty.');

it('generates basic auth token with empty password', function (): void {
    $data = new BasicAuthData(
        username: 'testuser',
        password: ''
    );

    $generator = new BasicAuth($data);

    $token = $generator->generate();

    expect($token)
        ->toBeString()
        ->and($token)
        ->toBe(base64_encode('testuser:'));
});

it('generates basic auth token with only username', function (): void {
    $data = new BasicAuthData(
        username: 'testuser'
    );

    $generator = new BasicAuth($data);

    $token = $generator->generate();

    expect($token)
        ->toBeString()
        ->and($token)
        ->toBe(base64_encode('testuser:'));
});
