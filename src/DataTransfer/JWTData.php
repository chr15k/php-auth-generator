<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\DataTransfer;

use Chr15k\AuthGenerator\Enums\Algorithm;
use SensitiveParameter;

/**
 * @internal
 */
final readonly class JWTData
{
    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, string>  $headers
     */
    public function __construct(
        #[SensitiveParameter] public string $key = '',
        public array $payload = [],
        public array $headers = [],
        public Algorithm $algorithm = Algorithm::HS256,
        public bool $keyBase64Encoded = false
    ) {
        //
    }
}
