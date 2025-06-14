<?php

declare(strict_types=1);

namespace Chr15k\Codec\DataTransfer;

use Chr15k\Codec\Enums\Algorithm;

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
        public string $key = '',
        public array $payload = [],
        public array $headers = [],
        public Algorithm $algorithm = Algorithm::HS256,
        public bool $keyBase64Encoded = false
    ) {
        //
    }
}
