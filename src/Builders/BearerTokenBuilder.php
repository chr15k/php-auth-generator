<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\Builders;

use Chr15k\AuthGenerator\Contracts\Builder;
use Chr15k\AuthGenerator\Contracts\Generator;
use Chr15k\AuthGenerator\DataTransfer\BearerTokenData;
use Chr15k\AuthGenerator\Generators\BearerToken as BearerTokenGenerator;

final readonly class BearerTokenBuilder implements Builder
{
    private BearerTokenData $data;

    public function __construct()
    {
        $this->data = new BearerTokenData;
    }

    /**
     * Set the token length.
     */
    public function length(int $length): self
    {
        $this->data->length = $length;

        return $this;
    }

    /**
     * Set the token prefix.
     */
    public function prefix(string $prefix): self
    {
        $this->data->prefix = $prefix;

        return $this;
    }

    /**
     * Build and return the BearerToken generator.
     */
    public function build(): Generator
    {
        return new BearerTokenGenerator($this->data);
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
     * @return string The formatted authorization header string (e.g., "Bearer api_token123")
     */
    public function toHeader(): string
    {
        return 'Bearer '.$this->toString();
    }
}
