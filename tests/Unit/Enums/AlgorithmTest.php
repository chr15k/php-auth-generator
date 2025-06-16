<?php

declare(strict_types=1);

use Chr15k\AuthGenerator\Enums\Algorithm;

it('returns correct hash functions for all algorithms', function (): void {
    // HMAC algorithms
    expect(Algorithm::HS256->config())->toBe(['func' => 'hash_hmac', 'alg' => 'SHA256']);
    expect(Algorithm::HS384->config())->toBe(['func' => 'hash_hmac', 'alg' => 'SHA384']);
    expect(Algorithm::HS512->config())->toBe(['func' => 'hash_hmac', 'alg' => 'SHA512']);

    // RSA algorithms
    expect(Algorithm::RS256->config())->toBe(['func' => 'openssl', 'alg' => 'SHA256']);
    expect(Algorithm::RS384->config())->toBe(['func' => 'openssl', 'alg' => 'SHA384']);
    expect(Algorithm::RS512->config())->toBe(['func' => 'openssl', 'alg' => 'SHA512']);

    // ECDSA algorithms
    expect(Algorithm::ES256->config())->toBe(['func' => 'openssl', 'alg' => 'SHA256']);
    expect(Algorithm::ES384->config())->toBe(['func' => 'openssl', 'alg' => 'SHA384']);
    expect(Algorithm::ES256K->config())->toBe(['func' => 'openssl', 'alg' => 'SHA256']);

    // EdDSA algorithm
    expect(Algorithm::EdDSA->config())->toBe(['func' => 'sodium_crypto', 'alg' => 'EdDSA']);
});
