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

    public function algo(): string
    {
        return match ($this) {
            self::MD5, self::MD5_SESS => 'md5',
            self::SHA256, self::SHA256_SESS => 'sha256',
        };
    }

    public function func(): Closure
    {
        return fn (string $data): string => hash($this->algo(), $data);
    }

    public function isSessionVariant(): bool
    {
        return str_ends_with($this->value, '-sess');
    }
}
