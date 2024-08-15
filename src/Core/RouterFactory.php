<?php

declare(strict_types=1);

namespace Phabrique\Core;

interface RouterFactory {
    public function buildRouter(): Router;
}
