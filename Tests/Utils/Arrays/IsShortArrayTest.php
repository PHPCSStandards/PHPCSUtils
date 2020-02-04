<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Arrays;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Arrays;

/**
 * Tests for the \PHPCSUtils\Utils\Arrays::isShortArray() method.
 *
 * @covers \PHPCSUtils\Utils\Arrays::isShortArray
 *
 * @group arrays
 *
 * @since 1.0.0
 */
class IsShortArrayTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(Arrays::isShortArray(self::$phpcsFile, 100000));
    }

    /**
     * Test that false is returned when a non-short array token is passed.
     *
     * @dataProvider dataNotShortArrayToken
     *
     * @param string           $testMarker  The comment which prefaces the target token in the test file.
     * @param int|string|array $targetToken The token type(s) to look for.
     *
     * @return void
     */
    public function testNotShortArrayToken($testMarker, $targetToken)
    {
        $target = $this->getTargetToken($testMarker, $targetToken);
        $this->assertFalse(Arrays::isShortArray(self::$phpcsFile, $target));
    }

    /**
     * Data provider.
     *
     * @see testNotShortArrayToken() For the array format.
     *
     * @return array
     */
    public function dataNotShortArrayToken()
    {
        return [
            'long-array' => [
                '/* testLongArray */',
                \T_ARRAY,
            ],
            'array-assignment-no-key' => [
                '/* testArrayAssignmentEmpty */',
                \T_OPEN_SQUARE_BRACKET,
            ],
            'array-assignment-string-key' => [
                '/* testArrayAssignmentStringKey */',
                \T_OPEN_SQUARE_BRACKET,
            ],
            'array-assignment-int-key' => [
                '/* testArrayAssignmentIntKey */',
                \T_OPEN_SQUARE_BRACKET,
            ],
            'array-assignment-var-key' => [
                '/* testArrayAssignmentVarKey */',
                \T_OPEN_SQUARE_BRACKET,
            ],
            'array-access-string-key' => [
                '/* testArrayAccessStringKey */',
                \T_CLOSE_SQUARE_BRACKET,
            ],
            'array-access-int-key-1' => [
                '/* testArrayAccessIntKey1 */',
                \T_OPEN_SQUARE_BRACKET,
            ],
            'array-access-int-key-2' => [
                '/* testArrayAccessIntKey2 */',
                \T_OPEN_SQUARE_BRACKET,
            ],
            'array-access-function-call' => [
                '/* testArrayAccessFunctionCall */',
                \T_OPEN_SQUARE_BRACKET,
            ],
            'array-access-constant' => [
                '/* testArrayAccessConstant */',
                \T_OPEN_SQUARE_BRACKET,
            ],
            'live-coding' => [
                '/* testLiveCoding */',
                \T_OPEN_SQUARE_BRACKET,
            ],
        ];
    }

    /**
     * Test whether a T_OPEN_SHORT_ARRAY token is a short array.
     *
     * @dataProvider dataIsShortArray
     *
     * @param string           $testMarker  The comment which prefaces the target token in the test file.
     * @param bool             $expected    The expected boolean return value.
     * @param int|string|array $targetToken The token type(s) to test. Defaults to T_OPEN_SHORT_ARRAY.
     *
     * @return void
     */
    public function testIsShortArray($testMarker, $expected, $targetToken = \T_OPEN_SHORT_ARRAY)
    {
        $stackPtr = $this->getTargetToken($testMarker, $targetToken);
        $result   = Arrays::isShortArray(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testIsShortArray() For the array format.
     *
     * @return array
     */
    public function dataIsShortArray()
    {
        return [
            'short-array-not-nested' => [
                '/* testNonNestedShortArray */',
                true,
            ],
            'comparison-no-assignment' => [
                '/* testInComparison */',
                true,
            ],
            'comparison-no-assignment-nested' => [
                '/* testNestedInComparison */',
                true,
            ],
            'short-array-in-foreach' => [
                '/* testShortArrayInForeach */',
                true,
            ],
            'short-list-in-foreach' => [
                '/* testShortListInForeach */',
                false,
            ],
            'chained-assignment-short-list' => [
                '/* testMultiAssignShortlist */',
                false,
            ],
            'chained-assignment-short-array' => [
                '/* testMultiAssignShortArray */',
                true,
                \T_CLOSE_SHORT_ARRAY,
            ],
            'short-array-with-nesting-and-keys' => [
                '/* testShortArrayWithNestingAndKeys */',
                true,
            ],
            'short-array-nested-with-keys-1' => [
                '/* testNestedShortArrayWithKeys_1 */',
                true,
            ],
            'short-array-nested-with-keys-2' => [
                '/* testNestedShortArrayWithKeys_2 */',
                true,
            ],
            'short-array-nested-with-keys-3' => [
                '/* testNestedShortArrayWithKeys_3 */',
                true,
            ],
            'parse-error-anon-class-trait-use-as' => [
                '/* testNestedAnonClassWithTraitUseAs */',
                true,
            ],
            'parse-error-use-as' => [
                '/* testParseError */',
                true,
            ],
            'parse-error-live-coding' => [
                '/* testLiveCodingNested */',
                true,
            ],
        ];
    }
}
