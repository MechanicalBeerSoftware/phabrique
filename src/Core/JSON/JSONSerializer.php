<?php

declare(strict_types=1);

namespace Phabrique\Core\JSON;

use ReflectionObject;

class JSONSerializer
{
    public function serialize(mixed $obj): string
    {
        if (is_null($obj)) {
            return "null";
        }

        if (is_string($obj)) {
            return '"' . $obj . '"';
        }

        if (is_bool($obj)) {
            return $obj ? "true" : "false";
        }

        if (is_int($obj)) {
            return strval($obj);
        }

        if (is_float($obj)) {
            if (is_nan($obj)) {
                return "null";
            } else {
                return strval($obj);
            }
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
            $name = $this->serialize($prop->getName());

            $attributes = $prop->getAttributes(JsonField::class);
            if (count($attributes) > 1) {
                throw new JSONException("Cannot make use of multiple '" . JSONField::class . "' attributes.");
            }

            if (count($attributes) == 1) {
                $attrInstance = $attributes[0]->newInstance();
                if ($attrInstance->ignore) {
                    continue;
                }
                if (! is_null($attrInstance->fieldName)) {
                    $name = '"' . $attrInstance->fieldName . '"';
                }
            }

            $value = $this->serialize($prop->getValue($obj));
            $result .= "$name:$value,";
        }
        $result[strlen($result) - 1] = "}";
        return $result;
    }
}
