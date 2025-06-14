<?php

declare(strict_types=1);

namespace Chr15k\Codec\Generators;

use Chr15k\Codec\Contracts\Generator;
use Chr15k\Codec\DataTransfer\BearerTokenData;
use DomainException;

/**
 * @internal
 */
final readonly class BearerToken implements Generator
{
    public function __construct(private BearerTokenData $data)
    {
        //
    }

    public function generate(): string
    {
        $this->validate();

        $bytes = random_bytes(max(1, $this->data->length));

        $token = rtrim(strtr(base64_encode($bytes), '+/', '-_'), '=');

        return $this->data->prefix.$token;
    }

    private function validate(): void
    {
        if ($this->data->length < 32 || $this->data->length > 128) {
            throw new DomainException('Token length must be between 32 and 128 bytes.');
        }

        if (strlen($this->data->prefix) > 10) {
            throw new DomainException('Prefix length must not exceed 10 characters.');
        }
    }
}
