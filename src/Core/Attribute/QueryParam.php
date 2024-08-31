<?php

declare(strict_types=1);

namespace Phabrique\Core\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
class QueryParam
{
    public function __construct(
        public readonly ?string $name = null,
    ) {}
}
