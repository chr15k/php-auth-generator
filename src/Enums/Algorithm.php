<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\Enums;

enum Algorithm: string
{
    case ES256 = 'ES256';
    case ES384 = 'ES384';
    case ES256K = 'ES256K';
    case HS256 = 'HS256';
    case HS384 = 'HS384';
    case HS512 = 'HS512';
    case RS256 = 'RS256';
    case RS384 = 'RS384';
    case RS512 = 'RS512';
    case EdDSA = 'EdDSA';

    /**
     * @return array{func: string, alg: string}
     */
    public function hashFunction(): array
    {
        return match ($this) {
            self::ES384 => ['func' => 'openssl', 'alg' => 'SHA384'],
            self::ES256 => ['func' => 'openssl', 'alg' => 'SHA256'],
            self::ES256K => ['func' => 'openssl', 'alg' => 'SHA256'],
            self::HS256 => ['func' => 'hash_hmac', 'alg' => 'SHA256'],
            self::HS384 => ['func' => 'hash_hmac', 'alg' => 'SHA384'],
            self::HS512 => ['func' => 'hash_hmac', 'alg' => 'SHA512'],
            self::RS256 => ['func' => 'openssl', 'alg' => 'SHA256'],
            self::RS384 => ['func' => 'openssl', 'alg' => 'SHA384'],
            self::RS512 => ['func' => 'openssl', 'alg' => 'SHA512'],
            self::EdDSA => ['func' => 'sodium_crypto', 'alg' => 'EdDSA'],
        };
    }
}
