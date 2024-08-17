<?php

declare(strict_types=1);

namespace Phabrique\Core\JSON;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
class JSONField
{
    public function __construct(public bool $ignore = false, public ?string $fieldName = null) {}
}
