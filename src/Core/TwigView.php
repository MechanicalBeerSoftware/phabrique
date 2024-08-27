<?php

declare(strict_types=1);

namespace Phabrique\Core;

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigView implements View
{
    private Environment $twigEnvironment;
    private string $fileName;

    public function __construct(private readonly string $name)
    {
        $this->fileName = $name . ".twig.html";
        $basePath = "../resources/views";
        if (!file_exists($basePath . "/" . $this->fileName)) {
            throw new Exception("The view with the name {$name} doesn't exists");
        }
        $fileLoader = new FilesystemLoader($basePath);
        $this->twigEnvironment = new Environment($fileLoader);
    }

    public function render(ViewModel $viewModel = null): string
    {
        return $this->twigEnvironment->render($this->name . ".twig.html", ["viewModel" => $viewModel]);
    }
}
