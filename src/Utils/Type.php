<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\Utils;

use InvalidArgumentException;
use JsonSerializable;

final readonly class Type
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

    public static function isJsonSerializable(mixed $value): bool
    {
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
                if (! self::isJsonSerializable($item)) {
                    return false;
                }
            }
        }

        return true;
    }
}
