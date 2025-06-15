<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\DataTransfer;

use SensitiveParameter;

/**
 * @internal
 */
final readonly class BasicAuthData
{
    public function __construct(
        public string $username = '',
        #[SensitiveParameter] public string $password = ''
    ) {
        //
    }
}
