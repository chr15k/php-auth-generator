<?php

declare(strict_types=1);

namespace Tests\Unit\Builders;

use Chr15k\AuthGenerator\Builders\DigestAuthBuilder;
use Chr15k\AuthGenerator\Enums\DigestAlgorithm;
use Chr15k\AuthGenerator\Generators\DigestAuth;
use DomainException;

test('digest auth builder creates data with correct settings', function (): void {
    $builder = new DigestAuthBuilder;
    $builder->username('testuser')
        ->password('testpass')
        ->realm('testrealm')
        ->uri('/api/resource')
        ->method('GET')
        ->algorithm(DigestAlgorithm::MD5);

    $generator = $builder->build();

    expect($generator)->toBeInstanceOf(DigestAuth::class);
    $token = $builder->toString();

    expect($token)->toContain('username="testuser"');
    expect($token)->toContain('realm="testrealm"');
    expect($token)->toContain('uri="/api/resource"');
    expect($token)->toContain('algorithm="MD5"');
});

test('digest auth builder throws exception for empty username', function (): void {
    $builder = new DigestAuthBuilder;
    $builder->realm('testrealm')
        ->password('testpass');

    expect(fn (): string => $builder->toString())->toThrow(DomainException::class, 'Both username and realm must be provided.');
});

test('digest auth builder toArray returns correct headers', function (): void {
    $builder = new DigestAuthBuilder;
    $builder->username('testuser')
        ->password('testpass')
        ->realm('testrealm');

    $headers = $builder->toArray();

    expect($headers)->toHaveKey('Authorization');
    expect($headers['Authorization'])->toStartWith('Digest ');
    expect($headers['Authorization'])->toContain('username="testuser"');
});

test('digest auth builder toHeader returns correct header string', function (): void {
    $builder = new DigestAuthBuilder;
    $builder->username('testuser')
        ->password('testpass')
        ->realm('testrealm');

    $header = $builder->toHeader();

    expect($header)->toBeString();
    expect($header)->toStartWith('Digest ');
    expect($header)->toContain('username="testuser"');
    expect($header)->toContain('realm="testrealm"');
});

test('digest auth builder works with SHA-256 algorithm', function (): void {
    $builder = new DigestAuthBuilder;
    $builder->username('testuser')
        ->password('testpass')
        ->realm('testrealm')
        ->algorithm(DigestAlgorithm::SHA256);

    $token = $builder->toString();

    expect($token)->toContain('username="testuser"');
    expect($token)->toContain('algorithm="SHA-256"');
});

test('digest auth builder works with MD5-sess algorithm', function (): void {
    $builder = new DigestAuthBuilder;
    $builder->username('testuser')
        ->password('testpass')
        ->realm('testrealm')
        ->algorithm(DigestAlgorithm::MD5_SESS)
        ->nonce('testnonce')
        ->clientNonce('testcnonce');

    $token = $builder->toString();

    expect($token)->toContain('username="testuser"');
    expect($token)->toContain('algorithm="MD5-sess"');
    expect($token)->toContain('cnonce="testcnonce"');
});

test('digest auth builder can set quality of protection', function (): void {
    $builder = new DigestAuthBuilder;
    $builder->username('testuser')
        ->password('testpass')
        ->realm('testrealm')
        ->qop('auth')
        ->nonceCount('00000001');

    $token = $builder->toString();

    expect($token)->toContain('username="testuser"');
    expect($token)->toContain(', nc=00000001');
});

test('digest auth builder can set opaque value', function (): void {
    $builder = new DigestAuthBuilder;
    $builder->username('testuser')
        ->password('testpass')
        ->realm('testrealm')
        ->opaque('testopaque');

    $token = $builder->toString();

    expect($token)->toContain('username="testuser"');
    expect($token)->toContain('opaque="testopaque"');
});
