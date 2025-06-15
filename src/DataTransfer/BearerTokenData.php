<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\DataTransfer;

/**
 * @internal
 */
final class BearerTokenData
{
    public function __construct(
        public int $length = 32,
        public string $prefix = 'brr_',
    ) {
        //
    }
}
