<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\Enums;

use Closure;

enum DigestAlgorithm: string
{
    case MD5 = 'MD5';
    case MD5_SESS = 'MD5-sess';
    case SHA256 = 'SHA-256';
    case SHA256_SESS = 'SHA-256-sess';

    /**
     * @return array{func: string, alg: string}
     */
    public function hashFunction(): array
    {
        return match ($this) {
            self::MD5 => ['func' => 'md5', 'alg' => ''],
            self::MD5_SESS => ['func' => 'md5', 'alg' => ''],
            self::SHA256 => ['func' => 'hash', 'alg' => 'sha256'],
            self::SHA256_SESS => ['func' => 'hash', 'alg' => 'sha256'],
        };
    }

    public function hashFunctionClosure(): Closure
    {
        ['func' => $func, 'alg' => $alg] = $this->hashFunction();

        return match ($this) {
            self::SHA256, self::SHA256_SESS => fn ($v): string => $func($alg, $v),
            self::MD5, self::MD5_SESS => fn ($v): string => $func($v),
            default => fn ($v): string => $func($v)
        };
    }

    public function isSessionVariant(): bool
    {
        return str_ends_with($this->value, '-sess');
    }
}
