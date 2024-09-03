<?php

declare(strict_types=1);

namespace Phabrique\Core\Utils;

class ArrayUtils
{
    /**
     * Returns a new array containing all $array elements + $item. If inputed $array is sorted regarding $comparator
     * (or empty), result is ensured to be sorted too.
     * @param array $array An array to insert in. Despite the poor choice of words, this array is kept untouched.
     * @param mixed $item An item to insert into the array
     * @param callable $comparator A classical comparison function for items: ($a, $b) => >0 if $a > $b, 0 if equal, <0 if $a < $b 
     * @return A new array containing all initial array elements + the new item so that if the array was previously sorted,
     * it remains sorted.
     */
    public static function insertSorted(array $array, mixed $item, callable $comparator)
    {
        $newArray = [];
        $inserted = false;
        for ($i = 0; $i < count($array); $i++) {
            if (!$inserted && $comparator($item, $array[$i]) >= 0) {
                $inserted = true;
                array_push($newArray, $item);
            }
            array_push($newArray, $array[$i]);
        }
        if (! $inserted) {
            array_push($newArray, $item);
        }
        return $newArray;
    }
}
