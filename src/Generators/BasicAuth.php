<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\Generators;

use Chr15k\AuthGenerator\Contracts\Generator;
use Chr15k\AuthGenerator\DataTransfer\BasicAuthData;
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
