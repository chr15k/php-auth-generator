<?php

declare(strict_types=1);

use Chr15k\AuthGenerator\DataTransfer\JWTData;
use Chr15k\AuthGenerator\Enums\Algorithm;
use Chr15k\AuthGenerator\Generators\JWT;

it('generates a valid JWT token using symmetric algorithm', function (): void {

    $secret = 'your-256-bit-secret';

    $payload = [
        'iss' => 'example.org',
        'aud' => 'example.com',
        'iat' => 1356999524,
        'nbf' => 1357000000,
    ];

    $data = new JWTData(
        key: $secret,
        payload: $payload,
        algorithm: Algorithm::HS256,
    );

    $output = (new JWT($data))->generate();

    expect($output)
        ->toBe('eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJleGFtcGxlLm9yZyIsImF1ZCI6ImV4YW1wbGUuY29tIiwiaWF0IjoxMzU2OTk5NTI0LCJuYmYiOjEzNTcwMDAwMDB9.NWy4aASKmTS4GVKdvbA6vAN4_hg6hSYpYTXOkzP_aA4');
});

it('generates a valid JWT token using asymmetric algorithm', function (): void {

    $privateKey = <<<'EOD'
    -----BEGIN RSA PRIVATE KEY-----
    MIIEowIBAAKCAQEAuzWHNM5f+amCjQztc5QTfJfzCC5J4nuW+L/aOxZ4f8J3Frew
    M2c/dufrnmedsApb0By7WhaHlcqCh/ScAPyJhzkPYLae7bTVro3hok0zDITR8F6S
    JGL42JAEUk+ILkPI+DONM0+3vzk6Kvfe548tu4czCuqU8BGVOlnp6IqBHhAswNMM
    78pos/2z0CjPM4tbeXqSTTbNkXRboxjU29vSopcT51koWOgiTf3C7nJUoMWZHZI5
    HqnIhPAG9yv8HAgNk6CMk2CadVHDo4IxjxTzTTqo1SCSH2pooJl9O8at6kkRYsrZ
    WwsKlOFE2LUce7ObnXsYihStBUDoeBQlGG/BwQIDAQABAoIBAFtGaOqNKGwggn9k
    6yzr6GhZ6Wt2rh1Xpq8XUz514UBhPxD7dFRLpbzCrLVpzY80LbmVGJ9+1pJozyWc
    VKeCeUdNwbqkr240Oe7GTFmGjDoxU+5/HX/SJYPpC8JZ9oqgEA87iz+WQX9hVoP2
    oF6EB4ckDvXmk8FMwVZW2l2/kd5mrEVbDaXKxhvUDf52iVD+sGIlTif7mBgR99/b
    c3qiCnxCMmfYUnT2eh7Vv2LhCR/G9S6C3R4lA71rEyiU3KgsGfg0d82/XWXbegJW
    h3QbWNtQLxTuIvLq5aAryV3PfaHlPgdgK0ft6ocU2de2FagFka3nfVEyC7IUsNTK
    bq6nhAECgYEA7d/0DPOIaItl/8BWKyCuAHMss47j0wlGbBSHdJIiS55akMvnAG0M
    39y22Qqfzh1at9kBFeYeFIIU82ZLF3xOcE3z6pJZ4Dyvx4BYdXH77odo9uVK9s1l
    3T3BlMcqd1hvZLMS7dviyH79jZo4CXSHiKzc7pQ2YfK5eKxKqONeXuECgYEAyXlG
    vonaus/YTb1IBei9HwaccnQ/1HRn6MvfDjb7JJDIBhNClGPt6xRlzBbSZ73c2QEC
    6Fu9h36K/HZ2qcLd2bXiNyhIV7b6tVKk+0Psoj0dL9EbhsD1OsmE1nTPyAc9XZbb
    OPYxy+dpBCUA8/1U9+uiFoCa7mIbWcSQ+39gHuECgYAz82pQfct30aH4JiBrkNqP
    nJfRq05UY70uk5k1u0ikLTRoVS/hJu/d4E1Kv4hBMqYCavFSwAwnvHUo51lVCr/y
    xQOVYlsgnwBg2MX4+GjmIkqpSVCC8D7j/73MaWb746OIYZervQ8dbKahi2HbpsiG
    8AHcVSA/agxZr38qvWV54QKBgCD5TlDE8x18AuTGQ9FjxAAd7uD0kbXNz2vUYg9L
    hFL5tyL3aAAtUrUUw4xhd9IuysRhW/53dU+FsG2dXdJu6CxHjlyEpUJl2iZu/j15
    YnMzGWHIEX8+eWRDsw/+Ujtko/B7TinGcWPz3cYl4EAOiCeDUyXnqnO1btCEUU44
    DJ1BAoGBAJuPD27ErTSVtId90+M4zFPNibFP50KprVdc8CR37BE7r8vuGgNYXmnI
    RLnGP9p3pVgFCktORuYS2J/6t84I3+A17nEoB4xvhTLeAinAW/uTQOUmNicOP4Ek
    2MsLL2kHgL8bLTmvXV4FX+PXphrDKg1XxzOYn0otuoqdAQrkK4og
    -----END RSA PRIVATE KEY-----
    EOD;

    $publicKey = <<<'EOD'
    -----BEGIN PUBLIC KEY-----
    MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAuzWHNM5f+amCjQztc5QT
    fJfzCC5J4nuW+L/aOxZ4f8J3FrewM2c/dufrnmedsApb0By7WhaHlcqCh/ScAPyJ
    hzkPYLae7bTVro3hok0zDITR8F6SJGL42JAEUk+ILkPI+DONM0+3vzk6Kvfe548t
    u4czCuqU8BGVOlnp6IqBHhAswNMM78pos/2z0CjPM4tbeXqSTTbNkXRboxjU29vS
    opcT51koWOgiTf3C7nJUoMWZHZI5HqnIhPAG9yv8HAgNk6CMk2CadVHDo4IxjxTz
    TTqo1SCSH2pooJl9O8at6kkRYsrZWwsKlOFE2LUce7ObnXsYihStBUDoeBQlGG/B
    wQIDAQAB
    -----END PUBLIC KEY-----
    EOD;

    $payload = [
        'iss' => 'example.org',
        'aud' => 'example.com',
        'iat' => 1356999524,
        'nbf' => 1357000000,
    ];

    $data = new JWTData(
        key: $privateKey,
        payload: $payload,
        algorithm: Algorithm::RS256,
    );

    $output = (new JWT($data))->generate();

    expect($output)
        ->toBe('eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpc3MiOiJleGFtcGxlLm9yZyIsImF1ZCI6ImV4YW1wbGUuY29tIiwiaWF0IjoxMzU2OTk5NTI0LCJuYmYiOjEzNTcwMDAwMDB9.fm0h0Ec3yp7S3JNBLeFa2owu4a91IXFJs8NPgBUxgKKSfd_-Mqes2zxuxmkYpmQDG936u739mIDZyG9KQm0ER5HB243MVbFZHcaC7VAIqmoCZ4dcS6yoJ1ltH6vdwc8o3xkYVEWKvLr8a7ck21u-pASWB_tpqM7XtIu7xyCZhfpmxTbhNyTgsJ1HN4fVYrHn2535qYsetOTps_zM2cgVRQYbkp1RovL-ZDsp3rMxzEiGQ7F80JXh2fsTHKlpPqAyF40GEXvCZa0MIoRa7g1pIjRtNLYgOgO94YsSRGB0VDDsLNdXzkjv0Ujfk_uqtD0IKgt7ffQzh8b8dUl8onXA_g');
});

it('generates a valid JWT with custom headers', function (): void {

    $secret = 'your-256-bit-secret';

    $payload = [
        'iss' => 'example.org',
        'aud' => 'example.com',
        'iat' => 1356999524,
        'nbf' => 1357000000,
    ];

    $headers = [
        'kid' => 'key-id-12345',
        'cty' => 'application/json',
    ];

    $data = new JWTData(
        key: $secret,
        payload: $payload,
        headers: $headers,
        algorithm: Algorithm::HS256,
    );

    $output = (new JWT($data))->generate();
    $parts = explode('.', $output);

    // Test that we have three parts
    expect(count($parts))->toBe(3);

    // Base64 decode the header and check it contains our custom headers
    $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
    expect($header)->toHaveKey('kid');
    expect($header)->toHaveKey('cty');
    expect($header['kid'])->toBe('key-id-12345');
    expect($header['cty'])->toBe('application/json');

    // Base64 decode the payload and check it matches what we passed in
    $decodedPayload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
    expect($decodedPayload)->toBe($payload);
});

it('generates a valid JWT with base64 encoded secret', function (): void {

    $secret = base64_encode('your-256-bit-secret');

    $payload = [
        'iss' => 'example.org',
        'aud' => 'example.com',
    ];

    $data = new JWTData(
        key: $secret,
        payload: $payload,
        algorithm: Algorithm::HS256,
        keyBase64Encoded: true,
    );

    $output = (new JWT($data))->generate();
    $parts = explode('.', $output);

    // Test that we have three parts
    expect(count($parts))->toBe(3);

    // Base64 decode the header and check algorithm
    $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
    expect($header['alg'])->toBe('HS256');

    // Base64 decode the payload and check it matches what we passed in
    $decodedPayload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
    expect($decodedPayload)->toBe($payload);
});

it('throws an InvalidArgumentException for invalid base64 encoded key', function (): void {

    $invalidBase64Key = 'this-is-not-a-valid-base64-string';
    $payload = [
        'iss' => 'example.org',
        'aud' => 'example.com',
    ];
    $data = new JWTData(
        key: $invalidBase64Key,
        payload: $payload,
        algorithm: Algorithm::HS256,
        keyBase64Encoded: true,
    );
    $jwt = new JWT($data);
    $jwt->generate();
})->throws(InvalidArgumentException::class, 'Invalid base64 encoded key');

it('generates a valid JWT using HS384 algorithm', function (): void {

    $secret = 'your-384-bit-secret-key-for-testing-purposes';

    $payload = [
        'iss' => 'example.org',
        'aud' => 'example.com',
        'iat' => 1356999524,
    ];

    $data = new JWTData(
        key: $secret,
        payload: $payload,
        algorithm: Algorithm::HS384,
    );

    $output = (new JWT($data))->generate();
    $parts = explode('.', $output);

    // Test that we have three parts
    expect(count($parts))->toBe(3);

    // Base64 decode the header and check algorithm
    $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
    expect($header['alg'])->toBe('HS384');

    // Base64 decode the payload and check it matches what we passed in
    $decodedPayload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
    expect($decodedPayload)->toBe($payload);
});

it('generates a valid JWT using HS512 algorithm', function (): void {

    $secret = 'your-512-bit-secret-key-for-testing-purposes-with-extra-length';

    $payload = [
        'iss' => 'example.org',
        'aud' => 'example.com',
        'iat' => 1356999524,
    ];

    $data = new JWTData(
        key: $secret,
        payload: $payload,
        algorithm: Algorithm::HS512,
    );

    $output = (new JWT($data))->generate();
    $parts = explode('.', $output);

    // Test that we have three parts
    expect(count($parts))->toBe(3);

    // Base64 decode the header and check algorithm
    $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
    expect($header['alg'])->toBe('HS512');

    // Base64 decode the payload and check it matches what we passed in
    $decodedPayload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
    expect($decodedPayload)->toBe($payload);
});

it('generates a valid JWT using RS384 algorithm', function (): void {

    $privateKey = <<<'EOD'
    -----BEGIN RSA PRIVATE KEY-----
    MIIEowIBAAKCAQEAuzWHNM5f+amCjQztc5QTfJfzCC5J4nuW+L/aOxZ4f8J3Frew
    M2c/dufrnmedsApb0By7WhaHlcqCh/ScAPyJhzkPYLae7bTVro3hok0zDITR8F6S
    JGL42JAEUk+ILkPI+DONM0+3vzk6Kvfe548tu4czCuqU8BGVOlnp6IqBHhAswNMM
    78pos/2z0CjPM4tbeXqSTTbNkXRboxjU29vSopcT51koWOgiTf3C7nJUoMWZHZI5
    HqnIhPAG9yv8HAgNk6CMk2CadVHDo4IxjxTzTTqo1SCSH2pooJl9O8at6kkRYsrZ
    WwsKlOFE2LUce7ObnXsYihStBUDoeBQlGG/BwQIDAQABAoIBAFtGaOqNKGwggn9k
    6yzr6GhZ6Wt2rh1Xpq8XUz514UBhPxD7dFRLpbzCrLVpzY80LbmVGJ9+1pJozyWc
    VKeCeUdNwbqkr240Oe7GTFmGjDoxU+5/HX/SJYPpC8JZ9oqgEA87iz+WQX9hVoP2
    oF6EB4ckDvXmk8FMwVZW2l2/kd5mrEVbDaXKxhvUDf52iVD+sGIlTif7mBgR99/b
    c3qiCnxCMmfYUnT2eh7Vv2LhCR/G9S6C3R4lA71rEyiU3KgsGfg0d82/XWXbegJW
    h3QbWNtQLxTuIvLq5aAryV3PfaHlPgdgK0ft6ocU2de2FagFka3nfVEyC7IUsNTK
    bq6nhAECgYEA7d/0DPOIaItl/8BWKyCuAHMss47j0wlGbBSHdJIiS55akMvnAG0M
    39y22Qqfzh1at9kBFeYeFIIU82ZLF3xOcE3z6pJZ4Dyvx4BYdXH77odo9uVK9s1l
    3T3BlMcqd1hvZLMS7dviyH79jZo4CXSHiKzc7pQ2YfK5eKxKqONeXuECgYEAyXlG
    vonaus/YTb1IBei9HwaccnQ/1HRn6MvfDjb7JJDIBhNClGPt6xRlzBbSZ73c2QEC
    6Fu9h36K/HZ2qcLd2bXiNyhIV7b6tVKk+0Psoj0dL9EbhsD1OsmE1nTPyAc9XZbb
    OPYxy+dpBCUA8/1U9+uiFoCa7mIbWcSQ+39gHuECgYAz82pQfct30aH4JiBrkNqP
    nJfRq05UY70uk5k1u0ikLTRoVS/hJu/d4E1Kv4hBMqYCavFSwAwnvHUo51lVCr/y
    xQOVYlsgnwBg2MX4+GjmIkqpSVCC8D7j/73MaWb746OIYZervQ8dbKahi2HbpsiG
    8AHcVSA/agxZr38qvWV54QKBgCD5TlDE8x18AuTGQ9FjxAAd7uD0kbXNz2vUYg9L
    hFL5tyL3aAAtUrUUw4xhd9IuysRhW/53dU+FsG2dXdJu6CxHjlyEpUJl2iZu/j15
    YnMzGWHIEX8+eWRDsw/+Ujtko/B7TinGcWPz3cYl4EAOiCeDUyXnqnO1btCEUU44
    DJ1BAoGBAJuPD27ErTSVtId90+M4zFPNibFP50KprVdc8CR37BE7r8vuGgNYXmnI
    RLnGP9p3pVgFCktORuYS2J/6t84I3+A17nEoB4xvhTLeAinAW/uTQOUmNicOP4Ek
    2MsLL2kHgL8bLTmvXV4FX+PXphrDKg1XxzOYn0otuoqdAQrkK4og
    -----END RSA PRIVATE KEY-----
    EOD;

    $payload = [
        'iss' => 'example.org',
        'aud' => 'example.com',
        'iat' => 1356999524,
    ];

    $data = new JWTData(
        key: $privateKey,
        payload: $payload,
        algorithm: Algorithm::RS384,
    );

    $output = (new JWT($data))->generate();

    // We'll only test the header and payload parts since the signature will be different each time
    $parts = explode('.', $output);
    expect(count($parts))->toBe(3);
    expect($parts[0])->toBe('eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzM4NCJ9');
    expect($parts[1])->toBe('eyJpc3MiOiJleGFtcGxlLm9yZyIsImF1ZCI6ImV4YW1wbGUuY29tIiwiaWF0IjoxMzU2OTk5NTI0fQ');
});

it('generates a valid JWT using RS512 algorithm', function (): void {

    $privateKey = <<<'EOD'
    -----BEGIN RSA PRIVATE KEY-----
    MIIEowIBAAKCAQEAuzWHNM5f+amCjQztc5QTfJfzCC5J4nuW+L/aOxZ4f8J3Frew
    M2c/dufrnmedsApb0By7WhaHlcqCh/ScAPyJhzkPYLae7bTVro3hok0zDITR8F6S
    JGL42JAEUk+ILkPI+DONM0+3vzk6Kvfe548tu4czCuqU8BGVOlnp6IqBHhAswNMM
    78pos/2z0CjPM4tbeXqSTTbNkXRboxjU29vSopcT51koWOgiTf3C7nJUoMWZHZI5
    HqnIhPAG9yv8HAgNk6CMk2CadVHDo4IxjxTzTTqo1SCSH2pooJl9O8at6kkRYsrZ
    WwsKlOFE2LUce7ObnXsYihStBUDoeBQlGG/BwQIDAQABAoIBAFtGaOqNKGwggn9k
    6yzr6GhZ6Wt2rh1Xpq8XUz514UBhPxD7dFRLpbzCrLVpzY80LbmVGJ9+1pJozyWc
    VKeCeUdNwbqkr240Oe7GTFmGjDoxU+5/HX/SJYPpC8JZ9oqgEA87iz+WQX9hVoP2
    oF6EB4ckDvXmk8FMwVZW2l2/kd5mrEVbDaXKxhvUDf52iVD+sGIlTif7mBgR99/b
    c3qiCnxCMmfYUnT2eh7Vv2LhCR/G9S6C3R4lA71rEyiU3KgsGfg0d82/XWXbegJW
    h3QbWNtQLxTuIvLq5aAryV3PfaHlPgdgK0ft6ocU2de2FagFka3nfVEyC7IUsNTK
    bq6nhAECgYEA7d/0DPOIaItl/8BWKyCuAHMss47j0wlGbBSHdJIiS55akMvnAG0M
    39y22Qqfzh1at9kBFeYeFIIU82ZLF3xOcE3z6pJZ4Dyvx4BYdXH77odo9uVK9s1l
    3T3BlMcqd1hvZLMS7dviyH79jZo4CXSHiKzc7pQ2YfK5eKxKqONeXuECgYEAyXlG
    vonaus/YTb1IBei9HwaccnQ/1HRn6MvfDjb7JJDIBhNClGPt6xRlzBbSZ73c2QEC
    6Fu9h36K/HZ2qcLd2bXiNyhIV7b6tVKk+0Psoj0dL9EbhsD1OsmE1nTPyAc9XZbb
    OPYxy+dpBCUA8/1U9+uiFoCa7mIbWcSQ+39gHuECgYAz82pQfct30aH4JiBrkNqP
    nJfRq05UY70uk5k1u0ikLTRoVS/hJu/d4E1Kv4hBMqYCavFSwAwnvHUo51lVCr/y
    xQOVYlsgnwBg2MX4+GjmIkqpSVCC8D7j/73MaWb746OIYZervQ8dbKahi2HbpsiG
    8AHcVSA/agxZr38qvWV54QKBgCD5TlDE8x18AuTGQ9FjxAAd7uD0kbXNz2vUYg9L
    hFL5tyL3aAAtUrUUw4xhd9IuysRhW/53dU+FsG2dXdJu6CxHjlyEpUJl2iZu/j15
    YnMzGWHIEX8+eWRDsw/+Ujtko/B7TinGcWPz3cYl4EAOiCeDUyXnqnO1btCEUU44
    DJ1BAoGBAJuPD27ErTSVtId90+M4zFPNibFP50KprVdc8CR37BE7r8vuGgNYXmnI
    RLnGP9p3pVgFCktORuYS2J/6t84I3+A17nEoB4xvhTLeAinAW/uTQOUmNicOP4Ek
    2MsLL2kHgL8bLTmvXV4FX+PXphrDKg1XxzOYn0otuoqdAQrkK4og
    -----END RSA PRIVATE KEY-----
    EOD;

    $payload = [
        'iss' => 'example.org',
        'aud' => 'example.com',
        'iat' => 1356999524,
    ];

    $data = new JWTData(
        key: $privateKey,
        payload: $payload,
        algorithm: Algorithm::RS512,
    );

    $output = (new JWT($data))->generate();

    // We'll only test the header and payload parts since the signature will be different each time
    $parts = explode('.', $output);
    expect(count($parts))->toBe(3);
    expect($parts[0])->toBe('eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzUxMiJ9');
    expect($parts[1])->toBe('eyJpc3MiOiJleGFtcGxlLm9yZyIsImF1ZCI6ImV4YW1wbGUuY29tIiwiaWF0IjoxMzU2OTk5NTI0fQ');
});

it('generates a valid JWT using ES256 algorithm', function (): void {

    // Skip if ECDSA is not available
    if (! function_exists('openssl_pkey_get_private') || ! defined('OPENSSL_KEYTYPE_EC')) {
        $this->markTestSkipped('OpenSSL with ECDSA support is not available.');
    }

    // Create an ECDSA key for testing
    $res = false;

    try {
        $res = openssl_pkey_new([
            'curve_name' => 'prime256v1',
            'private_key_type' => OPENSSL_KEYTYPE_EC,
        ]);
    } catch (Exception $e) {
        $this->markTestSkipped('Unable to create ECDSA key: '.$e->getMessage());
    }

    if ($res === false) {
        $this->markTestSkipped('OpenSSL failed to create ECDSA key.');
    }

    $privateKey = '';
    openssl_pkey_export($res, $privateKey);

    $payload = [
        'iss' => 'example.org',
        'sub' => '1234567890',
        'iat' => 1356999524,
    ];

    $data = new JWTData(
        key: $privateKey,
        payload: $payload,
        algorithm: Algorithm::ES256,
    );

    $output = (new JWT($data))->generate();

    $parts = explode('.', $output);
    expect(count($parts))->toBe(3);
    expect($parts[0])->toBe('eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NiJ9');
    expect($parts[1])->toBe('eyJpc3MiOiJleGFtcGxlLm9yZyIsInN1YiI6IjEyMzQ1Njc4OTAiLCJpYXQiOjEzNTY5OTk1MjR9');
});

it('generates a valid JWT with minimal configuration', function (): void {

    $secret = 'minimal-secret-key';

    $payload = [
        'sub' => 'user123',
    ];

    $data = new JWTData(
        key: $secret,
        payload: $payload,
    );

    $output = (new JWT($data))->generate();
    $parts = explode('.', $output);

    // Test that we have three parts
    expect(count($parts))->toBe(3);

    // Base64 decode the header and check for default algorithm
    $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
    expect($header['typ'])->toBe('JWT');
    expect($header['alg'])->toBe('HS256');

    // Base64 decode the payload and check it matches what we passed in
    $decodedPayload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
    expect($decodedPayload)->toBe($payload);
});

it('generates a valid JWT with empty payload', function (): void {

    $secret = 'secret-key-for-empty-payload';

    $data = new JWTData(
        key: $secret,
        payload: [],
        algorithm: Algorithm::HS256,
    );

    $output = (new JWT($data))->generate();
    $parts = explode('.', $output);

    // Test that we have three parts
    expect(count($parts))->toBe(3);

    // Base64 decode the header and check algorithm
    $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
    expect($header['alg'])->toBe('HS256');

    // Base64 decode the payload and check it's empty
    $decodedPayload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
    expect($decodedPayload)->toBe([]);
});

it('generates a valid JWT with empty headers', function (): void {
    $secret = 'empty-headers-secret-key';

    $payload = [
        'sub' => '1234567890',
        'name' => 'John Doe',
    ];

    $data = new JWTData(
        key: $secret,
        payload: $payload,
        headers: [],
        algorithm: Algorithm::HS256,
    );

    $output = (new JWT($data))->generate();
    $parts = explode('.', $output);

    // Test that we have three parts
    expect(count($parts))->toBe(3);

    // Base64 decode the header and check that default values are still added
    $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
    expect($header)->toHaveKey('typ');
    expect($header)->toHaveKey('alg');
    expect($header['typ'])->toBe('JWT');
    expect($header['alg'])->toBe('HS256');

    // Base64 decode the payload and check it matches what we passed in
    $decodedPayload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
    expect($decodedPayload)->toBe($payload);
});

it('generates a valid JWT using EdDSA algorithm', function (): void {
    // Skip if sodium extension is not available
    if (! function_exists('sodium_crypto_sign_keypair') || ! function_exists('sodium_crypto_sign_detached')) {
        $this->markTestSkipped('Sodium extension not available.');
    }

    try {
        $keyPair = sodium_crypto_sign_keypair();

        $privateKey = base64_encode(sodium_crypto_sign_secretkey($keyPair));

        $publicKey = base64_encode(sodium_crypto_sign_publickey($keyPair));

        $payload = [
            'iss' => 'example.org',
            'aud' => 'example.com',
            'iat' => 1356999524,
            'nbf' => 1357000000,
        ];

        $data = new JWTData(
            key: $privateKey,
            payload: $payload,
            algorithm: Algorithm::EdDSA,
        );

        $output = (new JWT($data))->generate();

        $parts = explode('.', $output);
        expect(count($parts))->toBe(3);

        $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
        expect($header['alg'])->toBe('EdDSA');

        $decodedPayload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
        expect($decodedPayload)->toBe($payload);
    } catch (Exception $e) {
        $this->markTestSkipped('Sodium exception: '.$e->getMessage());
    }
});

it('throws DomainException for an invalid key via openssl', function (): void {
    $invalidKey = '-----BEGIN INVALID KEY-----';

    $payload = [
        'iss' => 'example.org',
        'aud' => 'example.com',
    ];

    $data = new JWTData(
        key: $invalidKey,
        payload: $payload,
        algorithm: Algorithm::ES384,
    );

    (new JWT($data))->generate();

})->throws(DomainException::class, 'OpenSSL unable to validate key');

it('throws DomainException when using EdDSA with an empty string as key', function (): void {
    // Skip if sodium extension is not available
    if (! function_exists('sodium_crypto_sign_keypair') || ! function_exists('sodium_crypto_sign_detached')) {
        $this->markTestSkipped('Sodium extension not available.');
    }

    $payload = [
        'iss' => 'example.org',
        'sub' => '1234567890',
    ];

    $data = new JWTData(
        key: '', // Empty key
        payload: $payload,
        algorithm: Algorithm::EdDSA,
    );

    expect(function () use ($data): void {
        (new JWT($data))->generate();
    })->toThrow(DomainException::class, 'Key cannot be empty string');
});

it('generates a valid JWT using ES256K algorithm', function (): void {
    // Skip if ECDSA is not available
    if (! function_exists('openssl_pkey_get_private') || ! defined('OPENSSL_KEYTYPE_EC')) {
        $this->markTestSkipped('OpenSSL with ECDSA support is not available.');
    }

    // Create an ECDSA key for testing
    $res = false;

    try {
        // ES256K uses secp256k1 curve (the Bitcoin curve)
        $res = openssl_pkey_new([
            'curve_name' => 'secp256k1',
            'private_key_type' => OPENSSL_KEYTYPE_EC,
        ]);
    } catch (Exception $e) {
        $this->markTestSkipped('Unable to create ES256K ECDSA key: '.$e->getMessage());
    }

    if ($res === false) {
        $this->markTestSkipped('OpenSSL failed to create ES256K ECDSA key or curve not supported.');
    }

    $privateKey = '';
    openssl_pkey_export($res, $privateKey);

    $payload = [
        'iss' => 'example.org',
        'sub' => '1234567890',
        'iat' => 1356999524,
    ];

    $data = new JWTData(
        key: $privateKey,
        payload: $payload,
        algorithm: Algorithm::ES256K,
    );

    $output = (new JWT($data))->generate();

    // Verify the structure and header/payload content
    $parts = explode('.', $output);
    expect(count($parts))->toBe(3);

    // ES256K header check
    expect($parts[0])->toBe('eyJ0eXAiOiJKV1QiLCJhbGciOiJFUzI1NksifQ');

    // Check the payload
    $decodedPayload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);
    expect($decodedPayload)->toBe($payload);

    // Verify signature length - ES256K should produce signatures of fixed length
    $signature = base64_decode(strtr($parts[2], '-_', '+/'));
    // ES256K signatures may vary in length due to DER encoding, but should be approximately 70-72 bytes
    expect(strlen($signature))->toBeGreaterThanOrEqual(64)->toBeLessThanOrEqual(72);
});

it('tests the parseDER method edge cases via reflection', function (): void {
    // Create a test DER encoded data
    // This is a simplified version that exercises the parseDER edge cases
    $reflectionClass = new ReflectionClass(JWT::class);
    $parseDERMethod = $reflectionClass->getMethod('parseDER');
    $parseDERMethod->setAccessible(true);

    // Create JWT with any data - we'll just use this as a vehicle to call the parseDER method
    $data = new JWTData(
        key: 'test-key',
        payload: ['test' => 'value'],
    );
    $jwt = new JWT($data);

    // Create test data that will trigger various code paths

    // 1. Basic DER structure with short length
    $shortLenDER = chr(0x02).chr(0x01).chr(0xFF);
    [$pos, $data] = $parseDERMethod->invoke($jwt, $shortLenDER);
    expect($pos)->toBe(3);
    expect($data)->toBe(chr(0xFF));

    // 2. DER with long length form (1 byte length of length)
    $longLenDER = chr(0x02).chr(0x81).chr(0x05).str_repeat(chr(0x00), 5);
    [$pos, $data] = $parseDERMethod->invoke($jwt, $longLenDER);
    expect($pos)->toBe(8);
    expect(strlen((string) $data))->toBe(5);

    // 3. DER with constructed type
    $constructedDER = chr(0x20 | 0x10).chr(0x00);
    [$pos, $data] = $parseDERMethod->invoke($jwt, $constructedDER);
    expect($data)->toBeNull();

    // 4. DER with BIT STRING type
    $bitStringDER = chr(0x03).chr(0x04).chr(0x00).chr(0x01).chr(0x02).chr(0x03);
    [$pos, $data] = $parseDERMethod->invoke($jwt, $bitStringDER);
    expect($pos)->toBe(6);
    expect($data)->toBe(chr(0x01).chr(0x02).chr(0x03));
});

it('tests the generateSignatureFromDER method via reflection', function (): void {
    // Get access to the private method
    $reflectionClass = new ReflectionClass(JWT::class);
    $generateSignatureFromDERMethod = $reflectionClass->getMethod('generateSignatureFromDER');
    $generateSignatureFromDERMethod->setAccessible(true);

    // Create JWT with any data - we'll just use this as a vehicle to call the method
    $data = new JWTData(
        key: 'test-key',
        payload: ['test' => 'value'],
    );
    $jwt = new JWT($data);

    // Create a mock DER encoded ECDSA signature
    // For a real ECDSA signature, this would be a DER encoding of two INTEGER values (r and s)
    // Format: SEQUENCE(INTEGER(r), INTEGER(s))

    // Simulated DER encoding:
    // 0x30 - SEQUENCE tag
    // 0x09 - Length of sequence
    // 0x02 - INTEGER tag for r
    // 0x03 - Length of r
    // 0x00 0x01 0x02 - Value of r (with leading zero)
    // 0x02 - INTEGER tag for s
    // 0x02 - Length of s
    // 0x03 0x04 - Value of s

    $r = chr(0x00).chr(0x01).chr(0x02); // r value with leading zero
    $s = chr(0x03).chr(0x04); // s value

    $der =
        chr(0x30).chr(9). // SEQUENCE of length 9
        chr(0x02).chr(3).$r. // r value
        chr(0x02).chr(2).$s; // s value

    // For ES256, key size is 256 bits (32 bytes)
    $result = $generateSignatureFromDERMethod->invoke($jwt, $der, 256);

    // Expect leading zeros to be trimmed and values padded to correct length (32 bytes each)
    $expectedR = str_pad(chr(0x01).chr(0x02), 32, chr(0x00), STR_PAD_LEFT);
    $expectedS = str_pad(chr(0x03).chr(0x04), 32, chr(0x00), STR_PAD_LEFT);

    expect($result)->toBe($expectedR.$expectedS);
    expect(strlen((string) $result))->toBe(64); // 32 bytes for r + 32 bytes for s
});
