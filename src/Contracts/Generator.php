<?php

declare(strict_types=1);

namespace Chr15k\AuthGenerator\Contracts;

/**
 * @internal
 */
interface Generator
{
    public function generate(): string;
}
