<?php

declare(strict_types=1);

namespace Phabrique\Core;

use Phabrique\Core\Request\RequestMethod;
use Phabrique\Core\Utils\ArrayUtils;
use Phabrique\Core\Utils\StringUtils;

class RadixNode
{
    /**
     * @var RadixNode[]
     */
    private array $children = [];

    /**
     * @var array<string, RouteHandler>
     */
    private array $routeHandlers = [];

    public function __construct(private string $text = "", private ?RadixNode $parent = null) {}

    public function isRoot()
    {
        return is_null($this->parent);
    }

    public function search(string $text): RouteMatch
    {
        $text = trim($text, "/");
        if ($text === "") {
            if (count($this->routeHandlers) > 0) {
                return new RouteMatch(true, [], $this->routeHandlers);
            }
            return new RouteMatch(false, [], []);
        }

        $fullText = $text;
        $remainder = "";
        $delim = strpos($text, "/");
        if ($delim !== FALSE) {
            $remainder = substr($text, $delim);
            $text = substr($text, 0, $delim);
        }

        foreach ($this->children as $child) {
            if ($child->text[0] === ":") {
                $label = substr($child->text, 1);
                $match = $child->search($remainder);
                if (! $match->hasMatched()) {
                    continue;
                }
                $match->addPathParam($label, $text);
                return $match;
            } elseif ($child->text[0] === "*") {
                $label = substr($child->text, 1);
                return new RouteMatch(true, [$label => $fullText], $child->routeHandlers);
            } elseif ($child->text === $text) {
                return $child->search($remainder);
            }
        }
        return new RouteMatch(false, [], []);
    }

    public function insert(string $text, RequestMethod $method, RouteHandler $routeHandler): RadixNode
    {
        $text = trim($text, "/");
        if ($text === "") {
            $this->routeHandlers[$method->name] = $routeHandler;
            return $this;
        }

        $remainder = "";
        $delim = strpos($text, "/");

        if ($delim !== FALSE) {
            $remainder = substr($text, $delim);
            $text = substr($text, 0, $delim);
        }

        foreach ($this->children as $child) {
            if ($child->text === $text) {
                return $child->insert($remainder, $method, $routeHandler);
            }
        }
        $nextChild = new RadixNode($text, parent: $this);
        $compFn = fn(RadixNode $rn1, RadixNode $rn2) => strcmp($rn1->text, $rn2->text);
        $this->children = ArrayUtils::insertSorted($this->children, $nextChild, $compFn);
        return $nextChild->insert($remainder, $method, $routeHandler);
    }

    public function graph(): string
    {
        $result = is_null($this->parent) ? "<root>" : $this->text;
        foreach ($this->children as $child) {
            $result .= "\n" . StringUtils::indent("  ", $child->graph());
        }
        return $result;
    }
}
