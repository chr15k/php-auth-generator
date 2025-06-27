<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\Builders;

use Chr15k\AuthGenerator\Contracts\Builder;
use Chr15k\AuthGenerator\Contracts\Generator;
use Chr15k\AuthGenerator\DataTransfer\JWTData;
use Chr15k\AuthGenerator\Enums\Algorithm;
use Chr15k\AuthGenerator\Generators\JWT as JWTGenerator;
use Chr15k\AuthGenerator\Utils\JWTUtil;
use InvalidArgumentException;
use SensitiveParameter;

final class JWTBuilder implements Builder
{
    /**
     * Standard JWT claim names as defined in RFC 7519.
     */
    public const CLAIM_ISSUER = 'iss';

    public const CLAIM_SUBJECT = 'sub';

    public const CLAIM_AUDIENCE = 'aud';

    public const CLAIM_EXPIRATION = 'exp';

    public const CLAIM_NOT_BEFORE = 'nbf';

    public const CLAIM_ISSUED_AT = 'iat';

    public const CLAIM_JWT_ID = 'jti';

    private string $key = '';

    private bool $keyBase64Encoded = false;

    private Algorithm $algorithm = Algorithm::HS256;

    /** @var array<string, mixed> */
    private array $payload = [];

    /** @var array<string, string> */
    private array $headers = [];

    /**
     * Set the signing key for the JWT.
     */
    public function key(#[SensitiveParameter] string $key, bool $isBase64Encoded = false): self
    {
        $this->key = $key;
        $this->keyBase64Encoded = $isBase64Encoded;

        return $this;
    }

    /**
     * Set the algorithm for signing the JWT.
     */
    public function algorithm(Algorithm $algorithm): self
    {
        $this->algorithm = $algorithm;

        return $this;
    }

    /**
     * Add a claim to the JWT payload.
     */
    public function claim(string $name, mixed $value): self
    {
        if (! JWTUtil::validateClaim($value)) {
            throw new InvalidArgumentException("Claim '{$name}' contains non-serializable data");
        }

        $this->payload[$name] = $value;

        return $this;
    }

    /**
     * Add multiple claims to the JWT payload.
     *
     * @param  array<string, mixed>  $claims
     */
    public function claims(array $claims): self
    {
        foreach ($claims as $name => $value) {
            $this->claim($name, $value);
        }

        return $this;
    }

    /**
     * Add a header to the JWT.
     */
    public function header(string $name, mixed $value): self
    {
        if ($value === null) {
            return $this;
        }

        $this->headers[$name] = JWTUtil::stringifyHeaderValue($value);

        return $this;
    }

    /**
     * Add multiple headers to the JWT.
     *
     * @param  array<string, string>  $headers
     */
    public function headers(array $headers): self
    {
        foreach ($headers as $name => $value) {
            $this->header($name, $value);
        }

        return $this;
    }

    /**
     * Add standard claims for token expiration.
     */
    public function expiresIn(int $seconds): self
    {
        $now = time();
        $this->claim(self::CLAIM_ISSUED_AT, $now);
        $this->claim(self::CLAIM_EXPIRATION, $now + $seconds);

        return $this;
    }

    /**
     * Make the token not valid before a certain time.
     */
    public function notBefore(int $timestamp): self
    {
        $this->claim(self::CLAIM_NOT_BEFORE, $timestamp);

        return $this;
    }

    /**
     * Add a unique identifier for the token.
     */
    public function withJwtId(string $id): self
    {
        $this->claim(self::CLAIM_JWT_ID, $id);

        return $this;
    }

    /**
     * Generate a unique JWT ID (useful to prevent replay attacks).
     */
    public function withUniqueJwtId(): self
    {
        $this->claim(self::CLAIM_JWT_ID, bin2hex(random_bytes(16)));

        return $this;
    }

    /**
     * Add standard claims for token issuer.
     */
    public function issuedBy(string $issuer): self
    {
        $this->claim(self::CLAIM_ISSUER, $issuer);

        return $this;
    }

    /**
     * Add standard claim for token subject.
     */
    public function subject(string $subject): self
    {
        $this->claim(self::CLAIM_SUBJECT, $subject);

        return $this;
    }

    /**
     * Add standard claim for token audience.
     *
     * @param  string|string[]  $audience  Single audience or array of audiences
     */
    public function audience(string|array $audience): self
    {
        $this->claim(self::CLAIM_AUDIENCE, $audience);

        return $this;
    }

    /**
     * Add all standard timestamp claims at once.
     *
     * @param  int  $expiresIn  Number of seconds until the token expires
     * @param  int  $notBefore  Timestamp when the token becomes valid (default: now)
     */
    public function withTimestampClaims(int $expiresIn, ?int $notBefore = null): self
    {
        $now = time();
        $this->claim(self::CLAIM_ISSUED_AT, $now);
        $this->claim(self::CLAIM_EXPIRATION, $now + $expiresIn);

        if ($notBefore !== null) {
            $this->claim(self::CLAIM_NOT_BEFORE, $notBefore);
        } else {
            $this->claim(self::CLAIM_NOT_BEFORE, $now);
        }

        return $this;
    }

    /**
     * Build and return the JWT generator.
     */
    public function build(): Generator
    {
        $data = new JWTData(
            $this->key,
            $this->payload,
            $this->headers,
            $this->algorithm,
            $this->keyBase64Encoded
        );

        return new JWTGenerator($data);
    }

    /**
     * Generate the token string directly.
     */
    public function toString(): string
    {
        return $this->build()->generate();
    }

    /**
     * Generate the token and return it as an array of headers.
     *
     * @param  array<string, string|array<string>>  $additionalHeaders  Additional headers to include
     * @return array<string, string|array<string>> The complete headers array with the Authorization header
     */
    public function toArray(array $additionalHeaders = []): array
    {
        $token = $this->toString();

        return array_merge(
            ['Authorization' => 'Bearer '.$token],
            $additionalHeaders
        );
    }

    /**
     * Generate the token and format it as a complete Bearer Authorization header string.
     *
     * @return string The formatted authorization header string (e.g., "Bearer eyJhb...")
     */
    public function toHeader(): string
    {
        return 'Bearer '.$this->toString();
    }
}
