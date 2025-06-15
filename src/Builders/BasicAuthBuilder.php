<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\Builders;

use Chr15k\AuthGenerator\Contracts\Builder;
use Chr15k\AuthGenerator\Contracts\Generator;
use Chr15k\AuthGenerator\DataTransfer\BasicAuthData;
use Chr15k\AuthGenerator\Generators\BasicAuth as BasicAuthGenerator;

final readonly class BasicAuthBuilder implements Builder
{
    private BasicAuthData $data;

    public function __construct()
    {
        $this->data = new BasicAuthData;
    }

    /**
     * Set the username for Basic Auth.
     */
    public function username(string $username): self
    {
        $this->data->username = $username;

        return $this;
    }

    /**
     * Set the password for Basic Auth.
     */
    public function password(string $password): self
    {
        $this->data->password = $password;

        return $this;
    }

    /**
     * Build and return the BasicAuth generator.
     */
    public function build(): Generator
    {
        return new BasicAuthGenerator($this->data);
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
            ['Authorization' => 'Basic '.$token],
            $additionalHeaders
        );
    }

    /**
     * Generate the token and format it as a complete Basic Authorization header string.
     *
     * @return string The formatted authorization header string (e.g., "Basic dXNlcjpwYXNz")
     */
    public function toHeader(): string
    {
        return 'Basic '.$this->toString();
    }
}
