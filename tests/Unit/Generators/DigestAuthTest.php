<?php

declare(strict_types=1);

use Chr15k\AuthGenerator\DataTransfer\DigestAuthData;
use Chr15k\AuthGenerator\Enums\DigestAlgorithm;
use Chr15k\AuthGenerator\Generators\DigestAuth;

/*
|--------------------------------------------------------------------------
| Exception tests
|--------------------------------------------------------------------------
*/

it('throws an exception for empty username', function (): void {

    $data = new DigestAuthData(
        username: '',
        password: 'pass',
        realm: 'example.com'
    );

    (new DigestAuth($data))->generate();
})->throws(DomainException::class, 'Both username and realm must be provided.');

it('throws an exception for empty realm', function (): void {

    $data = new DigestAuthData(
        username: 'user',
        password: 'pass',
        realm: ''
    );

    (new DigestAuth($data))->generate();

})->throws(DomainException::class, 'Both username and realm must be provided.');

/*
|--------------------------------------------------------------------------
| minimal data tests
|--------------------------------------------------------------------------
*/

it('generates an MD5 digest auth string with minimal data', function (): void {

    $data = new DigestAuthData(
        username: 'user',
        password: 'pass',
        realm: 'example.com',
        algorithm: DigestAlgorithm::MD5,
    );

    $header = (new DigestAuth($data))->generate();

    expect($header)
        ->toBe('username="user", realm="example.com", nonce="", uri="/", algorithm="MD5", response="e563373ebcdfa0ce2f0d650107a7bdab"');
});

it('generates an MD5-sess digest auth string with minimal data', function (): void {

    $data = new DigestAuthData(
        username: 'user',
        password: 'pass',
        realm: 'example.com',
        algorithm: DigestAlgorithm::MD5_SESS,
    );

    $header = (new DigestAuth($data))->generate();

    expect($header)
        ->toBe('username="user", realm="example.com", nonce="", uri="/", algorithm="MD5-sess", cnonce="", response="5ba8bef571094fb5d63dab54d5dc212a"');
});

it('generates a SHA-256 digest auth string with minimal data', function (): void {

    $data = new DigestAuthData(
        username: 'user',
        password: 'pass',
        realm: 'example.com',
        algorithm: DigestAlgorithm::SHA256,
    );

    $header = (new DigestAuth($data))->generate();

    expect($header)
        ->toBe('username="user", realm="example.com", nonce="", uri="/", algorithm="SHA-256", response="fac676bef14a669c850f6ffae767bac065078ba21b84e232393d0cc2aa5bf1db"');
});

it('generates a SHA-256-sess digest auth string with minimal data', function (): void {

    $data = new DigestAuthData(
        username: 'user',
        password: 'pass',
        realm: 'example.com',
        algorithm: DigestAlgorithm::SHA256_SESS,
    );

    $header = (new DigestAuth($data))->generate();

    expect($header)
        ->toBe('username="user", realm="example.com", nonce="", uri="/", algorithm="SHA-256-sess", response="ff77243f4b21afc072312b41cb1dda7a4b1a50a221b5af3167416965974300f6"');
});

/*
|--------------------------------------------------------------------------
| full data tests - MD5, MD5-sess, SHA-256, SHA-256-sess (auth qop)
|--------------------------------------------------------------------------
*/

it('generates an MD5 digest auth string with full data', function (): void {

    $data = new DigestAuthData(
        username: 'user',
        password: 'pass',
        realm: 'example.com',
        method: 'GET',
        uri: '/path',
        nonce: 'nonce123',
        nc: '00000001',
        cnonce: '0a4f113b',
        qop: 'auth',
        algorithm: DigestAlgorithm::MD5,
        opaque: 'opaque123'
    );

    $header = (new DigestAuth($data))->generate();

    expect($header)
        ->toBe('username="user", realm="example.com", nonce="nonce123", uri="/path", algorithm="MD5", qop=auth, nc=00000001, cnonce="0a4f113b", response="95b1b30f94a1a47da30be528807d1293", opaque="opaque123"');
});

it('generates an MD5-sess digest auth string with full data', function (): void {

    $data = new DigestAuthData(
        username: 'user',
        password: 'pass',
        realm: 'example.com',
        method: 'GET',
        uri: '/path',
        nonce: 'nonce123',
        nc: '00000001',
        cnonce: '0a4f113b',
        qop: 'auth',
        algorithm: DigestAlgorithm::MD5_SESS,
        opaque: 'opaque123'
    );

    $header = (new DigestAuth($data))->generate();

    expect($header)
        ->toBe('username="user", realm="example.com", nonce="nonce123", uri="/path", algorithm="MD5-sess", qop=auth, nc=00000001, cnonce="0a4f113b", response="dd667990235163c508c325f7684927a8", opaque="opaque123"');
});

it('generates a SHA-256 digest auth string with full data', function (): void {

    $data = new DigestAuthData(
        username: 'user',
        password: 'pass',
        realm: 'example.com',
        method: 'GET',
        uri: '/path',
        nonce: 'nonce123',
        nc: '00000001',
        cnonce: '0a4f113b',
        qop: 'auth',
        algorithm: DigestAlgorithm::SHA256,
        opaque: 'opaque123'
    );

    $header = (new DigestAuth($data))->generate();

    expect($header)
        ->toBe('username="user", realm="example.com", nonce="nonce123", uri="/path", algorithm="SHA-256", qop=auth, nc=00000001, cnonce="0a4f113b", response="f203d6b012a7978c03f35a3860c90aba0e528320d6033c47828589c6c861a825", opaque="opaque123"');
});

it('generates a SHA-256-sess digest auth string with full data', function (): void {

    $data = new DigestAuthData(
        username: 'user',
        password: 'pass',
        realm: 'example.com',
        method: 'GET',
        uri: '/path',
        nonce: 'nonce123',
        nc: '00000001',
        cnonce: '0a4f113b',
        qop: 'auth',
        algorithm: DigestAlgorithm::SHA256_SESS,
        opaque: 'opaque123'
    );

    $header = (new DigestAuth($data))->generate();

    expect($header)
        ->toBe('username="user", realm="example.com", nonce="nonce123", uri="/path", algorithm="SHA-256-sess", qop=auth, nc=00000001, cnonce="0a4f113b", response="2f3b67327484fead11b62971f5b1710628cc3e7f46f6b9612873615237efe0be", opaque="opaque123"');
});

/*
|--------------------------------------------------------------------------
| full data tests with data qop integrity (auth-int)
|--------------------------------------------------------------------------
*/

it('generates an MD5 digest auth string with QOP data integrity (auth-int)', function (): void {

    $data = new DigestAuthData(
        username: 'user',
        password: 'pass',
        realm: 'example.com',
        method: 'POST',
        uri: '/path',
        nonce: 'nonce123',
        nc: '00000001',
        cnonce: '0a4f113b',
        qop: 'auth-int',
        algorithm: DigestAlgorithm::MD5,
        opaque: 'opaque123',
        entityBody: 'foo=bar'
    );

    $header = (new DigestAuth($data))->generate();

    expect($header)
        ->toBe('username="user", realm="example.com", nonce="nonce123", uri="/path", algorithm="MD5", qop=auth-int, nc=00000001, cnonce="0a4f113b", response="ea60fba829b499794a1203f158f0b3f5", opaque="opaque123"');
});

it('generates an MD5-sess digest auth string with QOP data integrity (auth-int)', function (): void {

    $data = new DigestAuthData(
        username: 'user',
        password: 'pass',
        realm: 'example.com',
        method: 'POST',
        uri: '/path',
        nonce: 'nonce123',
        nc: '00000001',
        cnonce: '0a4f113b',
        qop: 'auth-int',
        algorithm: DigestAlgorithm::MD5_SESS,
        opaque: 'opaque123',
        entityBody: 'foo=bar'
    );

    $header = (new DigestAuth($data))->generate();

    expect($header)
        ->toBe('username="user", realm="example.com", nonce="nonce123", uri="/path", algorithm="MD5-sess", qop=auth-int, nc=00000001, cnonce="0a4f113b", response="31cbb11e3d84759370d9638bc1acbb40", opaque="opaque123"');
});

it('generates a SHA-256 digest auth string with QOP data integrity (auth-int)', function (): void {

    $data = new DigestAuthData(
        username: 'user',
        password: 'pass',
        realm: 'example.com',
        method: 'POST',
        uri: '/path',
        nonce: 'nonce123',
        nc: '00000001',
        cnonce: '0a4f113b',
        qop: 'auth-int',
        algorithm: DigestAlgorithm::SHA256,
        opaque: 'opaque123',
        entityBody: 'foo=bar'
    );

    $header = (new DigestAuth($data))->generate();

    expect($header)
        ->toBe('username="user", realm="example.com", nonce="nonce123", uri="/path", algorithm="SHA-256", qop=auth-int, nc=00000001, cnonce="0a4f113b", response="fb38b33e886f57a870bbca89a4dfe65c56e4db804690f10503100d43b1c2e10a", opaque="opaque123"');
});

it('generates a SHA-256-sess digest auth string with QOP data integrity (auth-int)', function (): void {

    $data = new DigestAuthData(
        username: 'user',
        password: 'pass',
        realm: 'example.com',
        method: 'POST',
        uri: '/path',
        nonce: 'nonce123',
        nc: '00000001',
        cnonce: '0a4f113b',
        qop: 'auth-int',
        algorithm: DigestAlgorithm::SHA256_SESS,
        opaque: 'opaque123',
        entityBody: 'foo=bar'
    );

    $header = (new DigestAuth($data))->generate();

    expect($header)
        ->toBe('username="user", realm="example.com", nonce="nonce123", uri="/path", algorithm="SHA-256-sess", qop=auth-int, nc=00000001, cnonce="0a4f113b", response="2b670e1b044a3dac654caa87437317fb9641ff9c73bd3c9859a8064d34c907f0", opaque="opaque123"');
});

it('generates a SHA-256-sess digest auth string with QOP data integrity (auth-int) with multiple data', function (): void {

    $data = new DigestAuthData(
        username: 'user',
        password: 'pass',
        realm: 'example.com',
        method: 'POST',
        uri: '/path',
        nonce: 'nonce123',
        nc: '00000001',
        cnonce: '0a4f113b',
        qop: 'auth-int',
        algorithm: DigestAlgorithm::SHA256_SESS,
        opaque: 'opaque123',
        entityBody: 'foo=bar&test=123&lang=en'
    );

    $header = (new DigestAuth($data))->generate();

    expect($header)
        ->toBe('username="user", realm="example.com", nonce="nonce123", uri="/path", algorithm="SHA-256-sess", qop=auth-int, nc=00000001, cnonce="0a4f113b", response="561dcb3d2f3ecc953f6432bd9a63a831e3176f4a4ba1ef692d1d1759698eee80", opaque="opaque123"');
});
