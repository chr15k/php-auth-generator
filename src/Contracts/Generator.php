<?php

declare(strict_types=1);

namespace Chr15k\Codec\Contracts;

/**
 * @internal
 */
interface Generator
{
    public function generate(): string;
}
