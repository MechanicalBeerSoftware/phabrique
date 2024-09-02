<?php

declare(strict_types=1);

use Phabrique\Core\Utils\ArrayUtils;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ArrayUtilsTest extends TestCase
{
    static function sortedInsertCases()
    {
        return [
            [[], 999, [999]],
            [[1, 2, 4], 3, [1, 2, 3, 4]],
            [[1, 2, 3], 4, [1, 2, 3, 4]],
            [[1, 2, 2, 2, 2, 3], 2, [1, 2, 2, 2, 2, 2, 3]],
        ];
    }

    #[DataProvider("sortedInsertCases")]
    function testInsertSortedNumbers($array, $item, $expected)
    {
        $actual = ArrayUtils::insertSorted($array, $item, fn($a, $b) => $b - $a);
        $this->assertEquals($expected, $actual);
    }
}
