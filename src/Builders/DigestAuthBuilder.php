<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\Builders;

use Chr15k\AuthGenerator\Contracts\Builder;
use Chr15k\AuthGenerator\Contracts\Generator;
use Chr15k\AuthGenerator\DataTransfer\DigestAuthData;
use Chr15k\AuthGenerator\Enums\DigestAlgorithm;
use Chr15k\AuthGenerator\Generators\DigestAuth as DigestAuthGenerator;
use SensitiveParameter;

final class DigestAuthBuilder implements Builder
{
    private string $username = '';

    private string $password = '';

    private DigestAlgorithm $algorithm = DigestAlgorithm::MD5;

    private string $realm = '';

    private string $method = 'GET';

    private string $uri = '/';

    private string $nonce = '';

    private string $nc = '';

    private string $cnonce = '';

    private string $qop = '';

    private string $opaque = '';

    public function __construct()
    {
        $this->nonce = bin2hex(random_bytes(16));
        $this->cnonce = bin2hex(random_bytes(8));
    }

    /**
     * Set the username for Digest Auth.
     */
    public function username(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Set the password for Digest Auth.
     */
    public function password(#[SensitiveParameter] string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Set the algorithm for Digest Auth.
     */
    public function algorithm(DigestAlgorithm $algorithm): self
    {
        $this->algorithm = $algorithm;

        return $this;
    }

    /**
     * Set the realm for Digest Auth.
     */
    public function realm(string $realm): self
    {
        $this->realm = $realm;

        return $this;
    }

    /**
     * Set the HTTP method for Digest Auth.
     */
    public function method(string $method): self
    {
        $this->method = $method;

        return $this;
    }

    /**
     * Set the URI for Digest Auth.
     */
    public function uri(string $uri): self
    {
        $this->uri = $uri;

        return $this;
    }

    /**
     * Set the nonce for Digest Auth.
     */
    public function nonce(string $nonce): self
    {
        $this->nonce = $nonce;

        return $this;
    }

    /**
     * Set the nonce count for Digest Auth.
     */
    public function nonceCount(string $nc): self
    {
        $this->nc = $nc;

        return $this;
    }

    /**
     * Set the client nonce for Digest Auth.
     */
    public function clientNonce(string $cnonce): self
    {
        $this->cnonce = $cnonce;

        return $this;
    }

    /**
     * Set the quality of protection for Digest Auth.
     */
    public function qop(string $qop): self
    {
        $this->qop = $qop;

        return $this;
    }

    /**
     * Set the opaque value for Digest Auth.
     */
    public function opaque(string $opaque): self
    {
        $this->opaque = $opaque;

        return $this;
    }

    /**
     * Build and return the DigestAuth generator.
     */
    public function build(): Generator
    {
        $data = new DigestAuthData(
            username: $this->username,
            password: $this->password,
            algorithm: $this->algorithm,
            realm: $this->realm,
            method: $this->method,
            uri: $this->uri,
            nonce: $this->nonce,
            nc: $this->nc,
            cnonce: $this->cnonce,
            qop: $this->qop,
            opaque: $this->opaque,
        );

        return new DigestAuthGenerator($data);
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
            ['Authorization' => 'Digest '.$token],
            $additionalHeaders
        );
    }

    /**
     * Generate the token and format it as a complete Digest Authorization header string.
     *
     * @return string The formatted authorization header string (e.g., "Digest username="user", realm="realm", ...")
     */
    public function toHeader(): string
    {
        return 'Digest '.$this->toString();
    }
}
