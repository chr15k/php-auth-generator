<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\DataTransfer;

use Chr15k\AuthGenerator\Enums\DigestAlgorithm;
use SensitiveParameter;

/**
 * @internal
 *
 * Data transfer object containing all the parameters needed for Digest Authentication.
 *
 * This class encapsulates all the components required to generate a Digest Auth token:
 * - username and password for authentication
 * - realm, nonce, and algorithm as provided by the server challenge
 * - method and URI from the HTTP request
 * - nc (nonce count), cnonce (client nonce), qop (quality of protection) for auth-int
 * - opaque value as provided by the server
 * - entityBody for auth-int quality of protection (optional)
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
