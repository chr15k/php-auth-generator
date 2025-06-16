<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\Enums;

use Closure;

/**
 * Represents the algorithms available for Digest Authentication.
 *
 * These algorithms are used to generate the cryptographic digest responses
 * in HTTP Digest Authentication according to RFC 2617 and RFC 7616.
 *
 * - MD5: Original algorithm from RFC 2617
 * - MD5_SESS: Session variant of MD5 that includes client nonce
 * - SHA256: More secure SHA-256 algorithm from RFC 7616
 * - SHA256_SESS: Session variant of SHA-256 that includes client nonce
 */
enum DigestAlgorithm: string
{
    case MD5 = 'MD5';
    case MD5_SESS = 'MD5-sess';
    case SHA256 = 'SHA-256';
    case SHA256_SESS = 'SHA-256-sess';

    /**
     * Get the PHP hash algorithm identifier for this digest algorithm.
     *
     * @return string The hash algorithm name compatible with PHP's hash functions
     */
    public function algo(): string
    {
        return match ($this) {
            self::MD5, self::MD5_SESS => 'md5',
            self::SHA256, self::SHA256_SESS => 'sha256',
        };
    }

    /**
     * Get a closure that can be used to hash data with this algorithm.
     *
     * @return Closure A function that accepts a string and returns its hash
     */
    public function func(): Closure
    {
        return fn (string $data): string => hash($this->algo(), $data);
    }

    /**
     * Determines if this is a session variant algorithm (MD5-sess or SHA-256-sess).
     * Session variants incorporate the client nonce in the calculation.
     *
     * @return bool True if this is a session variant, false otherwise
     */
    public function isSessionVariant(): bool
    {
        return str_ends_with($this->value, '-sess');
    }
}
