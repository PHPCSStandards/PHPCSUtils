<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Internal\IsShortArrayOrList;

use PHPCSUtils\Internal\IsShortArrayOrList;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\Utils\IsShortArrayOrList class.
 *
 * @covers \PHPCSUtils\Internal\IsShortArrayOrList::__construct
 *
 * @since 1.0.0
 */
final class ConstructorTest extends UtilityMethodTestCase
{

    /**
     * Test receiving an exception when passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException(
            'The IsShortArrayOrList class expects to be passed a T_OPEN_SHORT_ARRAY or T_OPEN_SQUARE_BRACKET token.'
        );

        new IsShortArrayOrList(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an exception when a non-bracket token is passed.
     *
     * @dataProvider dataNotOpenBracket
     *
     * @param string           $testMarker The comment which prefaces the target token in the test file.
     * @param int|string|array $targetType The token type(s) to look for.
     *
     * @return void
     */
    public function testNotOpenBracket($testMarker, $targetType)
    {
        $this->expectPhpcsException(
            'The IsShortArrayOrList class expects to be passed a T_OPEN_SHORT_ARRAY or T_OPEN_SQUARE_BRACKET token.'
        );

        $target = $this->getTargetToken($testMarker, $targetType);
        new IsShortArrayOrList(self::$phpcsFile, $target);
    }

    /**
     * Data provider.
     *
     * @see testNotBracket() For the array format.
     *
     * @return array
     */
    public static function dataNotOpenBracket()
    {
        return [
            'long-array' => [
                'testMarker' => '/* testLongArray */',
                'targetType' => \T_ARRAY,
            ],
            'long-list' => [
                'testMarker' => '/* testLongList */',
                'targetType' => \T_LIST,
            ],
            'short-array-close-bracket' => [
                'testMarker' => '/* testShortArray */',
                'targetType' => \T_CLOSE_SHORT_ARRAY,
            ],
            'short-list-close-bracket' => [
                'testMarker' => '/* testShortList */',
                'targetType' => \T_CLOSE_SHORT_ARRAY,
            ],
            'square-brackets-close-bracket' => [
                'testMarker' => '/* testSquareBrackets */',
                'targetType' => \T_CLOSE_SQUARE_BRACKET,
            ],
        ];
    }

    /**
     * Test the constructor accepts all supported token types which are allowed to be passed to it.
     *
     * @dataProvider dataSupportedBrackets
     *
     * @param string           $testMarker The comment which prefaces the target token in the test file.
     * @param int|string|array $targetType The token type(s) to look for.
     *
     * @return void
     */
    public function testSupportedBrackets($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $solver = new IsShortArrayOrList(self::$phpcsFile, $target);

        $this->assertInstanceof('\PHPCSUtils\Internal\IsShortArrayOrList', $solver);
    }

    /**
     * Data provider.
     *
     * @see testSupportedBrackets() For the array format.
     *
     * @return array
     */
    public static function dataSupportedBrackets()
    {
        return [
            'short-array-open-bracket' => [
                'testMarker' => '/* testShortArray */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
            ],
            'short-list-open-bracket' => [
                'testMarker' => '/* testShortList */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
            ],
            'square-brackets-open-bracket' => [
                'testMarker' => '/* testSquareBrackets */',
                'targetType' => \T_OPEN_SQUARE_BRACKET,
            ],
        ];
    }
}
