<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\Generators;

use Chr15k\AuthGenerator\Contracts\Generator;
use Chr15k\AuthGenerator\DataTransfer\DigestAuthData;
use Chr15k\AuthGenerator\Enums\DigestAlgorithm;
use DomainException;

/**
 * @internal
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
        $data = $this->data;

        $closure = $data->algorithm->hashFunctionClosure();

        $ha1 = $closure(sprintf('%s:%s:%s', $data->username, $data->realm, $data->password));

        if ($data->algorithm->isSessionVariant()) {
            $ha1 = $closure(sprintf('%s:%s:%s', $ha1, $data->nonce, $data->cnonce));
        }

        if ($data->qop === 'auth-int') {
            $ha2 = $closure(sprintf(
                '%s:%s:%s',
                $data->method,
                $data->uri,
                $closure($data->entityBody)
            ));
        } else {
            $ha2 = $closure(sprintf('%s:%s', $data->method, $data->uri));
        }

        if ($data->qop !== '' && $data->qop !== '0') {
            return $closure(sprintf(
                '%s:%s:%s:%s:%s:%s',
                $ha1,
                $data->nonce,
                $data->nc,
                $data->cnonce,
                $data->qop,
                $ha2
            ));
        }

        return $closure("{$ha1}:{$data->nonce}:{$ha2}");
    }
}
