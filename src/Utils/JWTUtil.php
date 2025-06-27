<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\Utils;

use InvalidArgumentException;
use JsonSerializable;

final readonly class JWTUtil
{
    public static function stringifyHeaderValue(mixed $value): string
    {
        if (is_string($value)) {
            return $value;
        }

        if (is_scalar($value)) {
            return (string) $value;
        }

        throw new InvalidArgumentException(
            sprintf('Header value must be scalar, %s given', get_debug_type($value))
        );
    }

    public static function validateClaim(mixed $value): bool
    {
        $validType = function () use ($value): bool {
            if (is_string($value)) {
                return true;
            }

            if (is_resource($value) || is_callable($value)) {
                return false;
            }

            if (is_object($value)) {
                return $value instanceof JsonSerializable ||
                    method_exists($value, '__toString') ||
                    method_exists($value, 'jsonSerialize');
            }

            if (is_array($value)) {
                foreach ($value as $item) {
                    if (! self::validateClaim($item)) {
                        return false;
                    }
                }
            }

            return true;
        };

        if (! $validType()) {
            return false;
        }

        $value = json_encode($value);

        return $value && json_last_error() === JSON_ERROR_NONE;
    }
}
