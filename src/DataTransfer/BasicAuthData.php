<?php

declare(strict_types=1);

namespace Chr15k\Codec\DataTransfer;

/**
 * @internal
 */
final class BasicAuthData
{
    public function __construct(
        public string $username = '',
        public string $password = ''
    ) {
        //
    }
}
