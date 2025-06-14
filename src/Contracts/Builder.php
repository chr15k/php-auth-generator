<?php

declare(strict_types=1);

namespace Chr15k\Codec\Contracts;

interface Builder
{
    /**
     * Build and return the Generator implementation.
     */
    public function build(): Generator;

    /**
     * Generate the token string directly.
     */
    public function toString(): string;

    /**
     * Generate the token and return it as an array of headers.
     *
     * @param  array<string, string|array<string>>  $additionalHeaders  Additional headers to include
     * @return array<string, string|array<string>> The complete headers array with the Authorization header
     */
    public function toArray(array $additionalHeaders = []): array;

    /**
     * Generate the token and format it as a complete Authorization header string.
     */
    public function toHeader(): string;
}
