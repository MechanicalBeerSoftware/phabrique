<?php

declare(strict_types=1);

namespace Phabrique\Core\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class PathParam
{
    public function __construct(
        public readonly string|null $name = null,
    ) {}
}
