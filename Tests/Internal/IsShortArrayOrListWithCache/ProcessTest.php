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
 * @covers \PHPCSUtils\Internal\IsShortArrayOrListWithCache::__construct
 * @covers \PHPCSUtils\Internal\IsShortArrayOrListWithCache::process
 * @covers \PHPCSUtils\Internal\IsShortArrayOrListWithCache::getOpener
 *
 * @since 1.0.0
 */
final class ProcessTest extends IsShortArrayOrListWithCacheTestCase
{

    /**
     * Test that false is returned when a non-bracket token is passed.
     *
     * @return void
     */
    public function testInvalidStackPtr()
    {
        $target = $this->getTargetToken('/* testLongArray */', \T_ARRAY);
        $this->assertFalse(IsShortArrayOrListWithCache::getType(self::$phpcsFile, $target));
    }

    /**
     * Test the process method works for all supported token types which are allowed to be passed to it.
     *
     * @dataProvider dataSupportedBrackets
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType The token type(s) to look for.
     * @param string     $expected   The expected function return value.
     *
     * @return void
     */
    public function testSupportedBrackets($testMarker, $targetType, $expected)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $this->assertSame($expected, IsShortArrayOrListWithCache::getType(self::$phpcsFile, $target));
    }

    /**
     * Data provider.
     *
     * @see testSupportedBrackets() For the array format.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataSupportedBrackets()
    {
        return [
            'short array open bracket' => [
                'testMarker' => '/* testShortArray */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short array close bracket' => [
                'testMarker' => '/* testShortArray */',
                'targetType' => \T_CLOSE_SHORT_ARRAY,
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short list openbracket' => [
                'testMarker' => '/* testShortList */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'short list close bracket' => [
                'testMarker' => '/* testShortList */',
                'targetType' => \T_CLOSE_SHORT_ARRAY,
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'square brackets open bracket' => [
                'testMarker' => '/* testSquareBrackets */',
                'targetType' => \T_OPEN_SQUARE_BRACKET,
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'square brackets close-bracket' => [
                'testMarker' => '/* testSquareBrackets */',
                'targetType' => \T_CLOSE_SQUARE_BRACKET,
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'parse error close bracket' => [
                'testMarker' => '/* testParseError */',
                'targetType' => \T_CLOSE_SQUARE_BRACKET,
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
        ];
    }
}
