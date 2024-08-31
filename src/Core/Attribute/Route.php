<?php

declare(strict_types=1);

namespace Phabrique\Core\Attribute;

use Attribute;
use Phabrique\Core\Request\RequestMethod;

#[Attribute(Attribute::TARGET_FUNCTION | Attribute::IS_REPEATABLE)]
class Route
{
    public function __construct(
        public readonly string $path,
        public readonly RequestMethod $method = RequestMethod::Get
    ) {}
}
