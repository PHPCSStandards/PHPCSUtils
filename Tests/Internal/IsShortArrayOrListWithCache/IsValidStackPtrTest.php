<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Internal\IsShortArrayOrListWithCache;

use PHPCSUtils\Internal\IsShortArrayOrList;
use PHPCSUtils\Internal\IsShortArrayOrListWithCache;
use PHPCSUtils\Tests\Internal\IsShortArrayOrListWithCache\IsShortArrayOrListWithCacheTestCase;

/**
 * Tests for the \PHPCSUtils\Utils\IsShortArrayOrListWithCache class.
 *
 * @covers \PHPCSUtils\Internal\IsShortArrayOrListWithCache::isValidStackPtr
 *
 * @since 1.0.0
 */
final class IsValidStackPtrTest extends IsShortArrayOrListWithCacheTestCase
{

    /**
     * Return values in use for square brackets.
     *
     * @var array<string>
     */
    private $validValues = [
        IsShortArrayOrList::SHORT_ARRAY,
        IsShortArrayOrList::SHORT_LIST,
        IsShortArrayOrList::SQUARE_BRACKETS,
    ];

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(IsShortArrayOrListWithCache::getType(self::$phpcsFile, 100000));
    }

    /**
     * Test that false is returned when a non-bracket token is passed.
     *
     * @dataProvider dataNotBracket
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType The token type(s) to look for.
     *
     * @return void
     */
    public function testNotBracket($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $this->assertFalse(IsShortArrayOrListWithCache::getType(self::$phpcsFile, $target));
    }

    /**
     * Data provider.
     *
     * @see testNotBracket() For the array format.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataNotBracket()
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
        ];
    }

    /**
     * Test that the returned type is one of the valid values when a valid bracket token is passed.
     *
     * This test also safeguards that the class supports both open, as well as close brackets.
     *
     * @dataProvider dataValidBracket
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType The token type(s) to look for.
     *
     * @return void
     */
    public function testValidBracket($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $result = IsShortArrayOrListWithCache::getType(self::$phpcsFile, $target);

        $this->assertContains($result, $this->validValues);
    }

    /**
     * Data provider.
     *
     * @see testNotBracket() For the array format.
     *
     * @return array<string, array<string, string|int>>
     */
    public static function dataValidBracket()
    {
        return [
            'open square bracket' => [
                'testMarker' => '/* testSquareBrackets */',
                'targetType' => \T_OPEN_SQUARE_BRACKET,
            ],
            'close square bracket' => [
                'testMarker' => '/* testSquareBrackets */',
                'targetType' => \T_CLOSE_SQUARE_BRACKET,
            ],
            'open short array token' => [
                'testMarker' => '/* testShortArray */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
            ],
            'close short list token' => [
                'testMarker' => '/* testShortList */',
                'targetType' => \T_CLOSE_SHORT_ARRAY,
            ],
        ];
    }
}
