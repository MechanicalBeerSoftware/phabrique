<?php

declare(strict_types=1);

namespace Phabrique\Core\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class Controller
{
    public function __construct(
        public readonly string $prefix = ""
    ) {}
}
