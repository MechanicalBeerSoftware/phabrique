<?php

declare(strict_types=1);

namespace Phabrique\Core;


class RadixNode
{
    /**
     * @var RadixNode[]
     */ 
    private array $children = [];

    public function __construct(private string $text = "", private ?RadixNode $parent=null, private mixed $value=null) { }

    public function isRoot()
    {
        return is_null($this->parent);
    }

    public function absolute(): string
    {
        if (is_null($this->parent)) {
            return "";
        } else {
            return $this->parent->absolute() + "/" + $this->text;
        }
    }

    public function getValue(): mixed
    {
        return $this->value;
    }

    public function search(string $text): ?RadixNode
    {
        $text = trim($text, "/");
        $remainder = "";
        $delim = strpos($text, "/");
         
        if ($delim !== FALSE) {
            $remainder = substr($text, $delim);
            $text = substr($text, 0, $delim);
        }

        foreach($this->children as $child) {
            if ($child->text === $text) {
                if ($remainder === "") {
                    return $child;
                } else {
                    return $child->search($remainder);
                }
            }
        }
        return null;
    }

    /**
     * Adds or replace the given value to the tree under the $text node.
     *
     * @param string $text The text node under which the value should be placed
     * @param mixed $value The value you want to store along the text in the Radix tree
     *
     * @return RadixNode The radix node that has been added to the tree or which value has been replaced
     */ 
    public function insert(string $text, mixed $value): RadixNode
    {
        $text = trim($text, "/");
        if ($text === "") {
            $this->value = $value;
            return $this;
        }

        $remainder = "";
        $delim = strpos($text, "/");
         
        if ($delim !== FALSE) {
            $remainder = substr($text, $delim);
            $text = substr($text, 0, $delim);
        }

        $nextChild = null;
        foreach($this->children as $child) {
            if ($child->text === $text) {
                $nextChild = $child;
                break;
            }
        }

        if (is_null($nextChild)) {
            $nextChild = new RadixNode($text, parent: $this, value: $value);
            array_push($this->children, $nextChild);
        }
        return $nextChild->insert($remainder, $value);
    }

    public function graph(): string
    {
        $result = is_null($this->parent) ? "<root>" : $this->text;
        foreach($this->children as $child)
        {
            $result .= "\n" . RadixNode::indent("  ", $child->graph());
        }
        return $result;
    }

    private static function indent($indentation, $src)
    {
        return implode("\n", array_map(fn($s) => ($indentation . $s), explode("\n", $src)));
    }
}
