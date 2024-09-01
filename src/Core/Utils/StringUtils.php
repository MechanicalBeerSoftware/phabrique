<?php

declare(strict_types=1);

namespace Phabrique\Core\Utils;

class StringUtils
{
    /**
     * Given two strings, return the longest common part of them starting from index 0
     *
     * @param string $str1 A string value
     * @param string $str2 Another string value
     *
     * @return string The longest string that starts both $str1 and $str2
     */
    public static function commonPrefix(string $str1, string $str2): string
    {
        $maxCommonLen = min(strlen($str1), strlen($str2));
        $i = 0;
        for (; $i < $maxCommonLen && $str1[$i] === $str2[$i]; $i++) {
        }
        return substr($str1, 0, $i);
    }
}
