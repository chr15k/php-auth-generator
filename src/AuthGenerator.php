<?php

declare(strict_types=1);

namespace Chr15k\Codec;

use Chr15k\Codec\Builders\BasicAuthBuilder;
use Chr15k\Codec\Builders\BearerTokenBuilder;
use Chr15k\Codec\Builders\JWTBuilder;

/**
 * Factory class for creating authentication token builders.
 */
final class AuthGenerator
{
    /**
     * Create a basic authentication token builder.
     */
    public static function basicAuth(): BasicAuthBuilder
    {
        return new BasicAuthBuilder;
    }

    /**
     * Create a bearer token builder.
     */
    public static function bearerToken(): BearerTokenBuilder
    {
        return new BearerTokenBuilder;
    }

    /**
     * Create a JWT token builder.
     */
    public static function jwt(): JWTBuilder
    {
        return new JWTBuilder;
    }

    // Static formatting methods removed in favor of the fluent formatted() method on builders
}
