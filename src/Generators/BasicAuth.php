<?php

declare(strict_types=1);

namespace Chr15k\Codec\Generators;

use Chr15k\Codec\Contracts\Generator;
use Chr15k\Codec\DataTransfer\BasicAuthData;
use DomainException;

/**
 * @internal
 */
final readonly class BasicAuth implements Generator
{
    public function __construct(private BasicAuthData $data)
    {
        //
    }

    public function generate(): string
    {
        if ($this->data->username === '') {
            throw new DomainException('Username cannot be empty.');
        }

        return base64_encode(sprintf('%s:%s', $this->data->username, $this->data->password ?? ''));
    }
}
