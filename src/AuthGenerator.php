<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator;

use Chr15k\AuthGenerator\Builders\BasicAuthBuilder;
use Chr15k\AuthGenerator\Builders\BearerTokenBuilder;
use Chr15k\AuthGenerator\Builders\JWTBuilder;

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
}
