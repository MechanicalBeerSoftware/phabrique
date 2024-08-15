<?php

declare(strict_types=1);

namespace Phabrique\Core;

interface View
{
    public function render(ViewModel $viewModel): string;
}
