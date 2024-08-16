<?php

declare(strict_types=1);

namespace Phabrique\Core\JSON;

use ReflectionObject;

class JSONSerializer
{
    public function serialize(mixed $obj): string
    {
        if (is_string($obj)) {
            return '"' . $obj . '"';
        }

        if (is_bool($obj)) {
            return $obj ? "true" : "false";
        }

        if (is_int($obj) | is_bool($obj) | is_float($obj)) {
            return strval($obj);
        }

        if (is_array($obj)) {
            if (array_is_list($obj)) {
                return $this->serializeArray($obj);
            } else {
                return $this->serializeAssoc($obj);
            }
        }
        return $this->serializeObject($obj);
    }

    private function serializeArray(array $arr): string
    {
        $result = '[';
        for ($i = 0; $i < count($arr); $i++) {
            $result .= $this->serialize($arr[$i]) . ",";
        }
        $result[strlen($result) - 1] = "]";
        return $result;
    }

    private function serializeAssoc(array $arr): string
    {
        $result = "{";
        foreach ($arr as $k => $v) {
            $result .= $this->serialize($k) . ":" . $this->serialize($v) . ",";
        }
        $result[strlen($result) - 1] = "}";
        return $result;
    }

    private function serializeObject(object $obj): string
    {
        $result = "{";
        $reflection = new ReflectionObject($obj);
        $props = $reflection->getProperties();
        foreach ($props as $prop) {
            $result .= $this->serialize($prop->getName()) . ":" . $this->serialize($prop->getValue($obj)) . ",";
        }
        $result[strlen($result) - 1] = "}";
        return $result;
    }
}
