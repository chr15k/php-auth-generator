<?php

declare(strict_types=1);

namespace Chr15k\Codec\Helpers;

/**
 * @internal
 */
final class AuthHeaderFormatter
{
    /**
     * Format a token for use in the Authorization header with the Basic scheme.
     */
    public static function formatBasicAuth(string $token): string
    {
        return 'Basic '.$token;
    }

    /**
     * Format a token for use in the Authorization header with the Bearer scheme.
     */
    public static function formatBearerToken(string $token): string
    {
        return 'Bearer '.$token;
    }

    /**
     * Generate a complete Authorization header value.
     *
     * @param  string  $scheme  The authentication scheme (e.g., 'Basic', 'Bearer')
     * @param  string  $token  The authentication token
     */
    public static function formatHeader(string $scheme, string $token): string
    {
        return $scheme.' '.$token;
    }
}
