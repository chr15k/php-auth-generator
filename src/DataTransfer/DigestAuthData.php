<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\DataTransfer;

use Chr15k\AuthGenerator\Enums\DigestAlgorithm;
use SensitiveParameter;

/**
 * @internal
 */
final readonly class DigestAuthData
{
    public function __construct(
        public string $username = '',
        #[SensitiveParameter] public string $password = '',
        public DigestAlgorithm $algorithm = DigestAlgorithm::MD5,
        public string $realm = '',
        public string $method = 'GET',
        public string $uri = '/',
        public string $nonce = '',
        public string $nc = '',
        public string $cnonce = '',
        public string $qop = '',
        public string $opaque = '',
        public string $entityBody = ''
    ) {
        //
    }
}
