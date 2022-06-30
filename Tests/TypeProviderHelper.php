<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests;

use ArrayIterator;
use EmptyIterator;
use stdClass;

/**
 * Helper class to provide an exhaustive list of types to test type safety/type support.
 *
 * @phpcs:disable Squiz.Arrays.ArrayDeclaration.DoubleArrowNotAligned -- If needed, fix once replaced by better sniff.
 */
final class TypeProviderHelper
{

    /**
     * Retrieve an array in data provider format with all typical PHP data types (with the exception of resources).
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getAll()
    {
        return [
            'null' => [
                'input' => null,
            ],
            'boolean false' => [
                'input' => false,
            ],
            'boolean true' => [
                'input' => true,
            ],
            'integer 0' => [
                'input' => 0,
            ],
            'negative integer' => [
                'input' => -123,
            ],
            'positive integer' => [
                'input' => 786687,
            ],
            'float 0.0' => [
                'input' => 0.0,
            ],
            'negative float' => [
                'input' => 5.600e-3,
            ],
            'positive float' => [
                'input' => 124.7,
            ],
            'empty string' => [
                'input' => '',
            ],
            'numeric string' => [
                'input' => '123',
            ],
            'textual string' => [
                'input' => 'foobar',
            ],
            'textual string starting with numbers' => [
                'input' => '123 My Street',
            ],
            'empty array' => [
                'input' => [],
            ],
            'array with values, no keys' => [
                'input' => [1, 2, 3],
            ],
            'array with values, string keys' => [
                'input' => ['a' => 1, 'b' => 2],
            ],
            'plain object' => [
                'input' => new stdClass(),
            ],
            'ArrayIterator object' => [
                'input' => new ArrayIterator([1, 2, 3]),
            ],
            'Iterator object, no array access' => [
                'input' => new EmptyIterator(),
            ],
        ];
    }
}
