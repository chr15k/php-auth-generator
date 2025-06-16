<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\Generators;

use Chr15k\AuthGenerator\Contracts\Generator;
use Chr15k\AuthGenerator\DataTransfer\DigestAuthData;
use Chr15k\AuthGenerator\Enums\DigestAlgorithm;
use DomainException;

/**
 * @internal
 *
 * Generates an HTTP Digest Authentication token based on provided data.
 *
 * This class implements the token generation process according to RFC 2617 and RFC 7616,
 * supporting various digest algorithms (MD5, SHA-256) and their session variants.
 *
 * The Digest Authentication method improves security compared to Basic Auth by not
 * sending the password over the network in plaintext. Instead, it uses a cryptographic
 * hash of the username, password, realm, and other components to prove the client
 * knows the credentials without revealing them.
 *
 * @see https://datatracker.ietf.org/doc/html/rfc2617 Original Digest Auth specification
 * @see https://datatracker.ietf.org/doc/html/rfc7616 SHA-256 and other improvements
 */
final readonly class DigestAuth implements Generator
{
    public function __construct(private DigestAuthData $data)
    {
        //
    }

    public function generate(): string
    {
        if ($this->data->username === '' || $this->data->realm === '') {
            throw new DomainException('Both username and realm must be provided.');
        }

        $header = trim((string) preg_replace('/\s{2,}/', ' ', sprintf(
            'username="%s" realm="%s" nonce="%s" uri="%s" algorithm="%s" %s %s %s response="%s"%s',
            $this->data->username,
            $this->data->realm,
            $this->data->nonce,
            $this->data->uri,
            $this->data->algorithm->value,
            $this->data->qop !== '' ? " qop={$this->data->qop}" : '',
            $this->data->nc !== '' ? " nc={$this->data->nc}" : '',
            $this->data->algorithm === DigestAlgorithm::MD5_SESS || $this->data->qop !== ''
                ? ' cnonce="'.$this->data->cnonce.'"' : '',
            $this->generateResponse(),
            $this->data->opaque !== '' ? ' opaque="'.$this->data->opaque.'"' : ''
        )));

        return str_replace(' ', ', ', $header);
    }

    private function generateResponse(): string
    {
        $hash = $this->data->algorithm->func();

        $ha1 = $hash(sprintf('%s:%s:%s', $this->data->username, $this->data->realm, $this->data->password));

        if ($this->data->algorithm->isSessionVariant()) {
            $ha1 = $hash(sprintf('%s:%s:%s', $ha1, $this->data->nonce, $this->data->cnonce));
        }

        if ($this->data->qop === 'auth-int') {
            $ha2 = $hash(sprintf(
                '%s:%s:%s',
                $this->data->method,
                $this->data->uri,
                $hash($this->data->entityBody)
            ));
        } else {
            $ha2 = $hash(sprintf('%s:%s', $this->data->method, $this->data->uri));
        }

        if ($this->data->qop !== '' && $this->data->qop !== '0') {
            return $hash(sprintf(
                '%s:%s:%s:%s:%s:%s',
                $ha1,
                $this->data->nonce,
                $this->data->nc,
                $this->data->cnonce,
                $this->data->qop,
                $ha2
            ));
        }

        return $hash("{$ha1}:{$this->data->nonce}:{$ha2}");
    }
}
