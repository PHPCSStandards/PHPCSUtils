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
use PHPCSUtils\Internal\StableCollections;
use PHPCSUtils\Tests\Internal\IsShortArrayOrListWithCache\IsShortArrayOrListWithCacheTestCase;
use PHPCSUtils\Utils\Arrays;
use PHPCSUtils\Utils\Lists;

/**
 * Tests all access points for the \PHPCSUtils\Utils\IsShortArrayOrListWithCache class are accessible
 * and return a value of the expected type.
 *
 * @since 1.0.0
 */
final class EntryPointsTest extends IsShortArrayOrListWithCacheTestCase
{

    /**
     * Return values in use for the IsShortArrayOrListWithCache::solve() method.
     *
     * @var array<string|bool>
     */
    private $validValues = [
        IsShortArrayOrList::SHORT_ARRAY,
        IsShortArrayOrList::SHORT_LIST,
        IsShortArrayOrList::SQUARE_BRACKETS,
        false,
    ];

    /**
     * Validate that the `Arrays::isShortArray()` method is accessible and always returns a boolean value.
     *
     * @dataProvider dataEntryPoints
     *
     * @covers \PHPCSUtils\Utils\Arrays::isShortArray
     *
     * @param string                                   $testMarker The comment which prefaces the target token in the file.
     * @param int|string|array<int|string, int|string> $targetType The token type(s) to look for.
     *
     * @return void
     */
    public function testIsShortArrayApi($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $this->assertIsBool(Arrays::isShortArray(self::$phpcsFile, $target));
    }

    /**
     * Validate that the `Lists::isShortList()` method is accessible and always returns a boolean value.
     *
     * @dataProvider dataEntryPoints
     *
     * @covers \PHPCSUtils\Utils\Lists::isShortList
     *
     * @param string                                   $testMarker The comment which prefaces the target token in the file.
     * @param int|string|array<int|string, int|string> $targetType The token type(s) to look for.
     *
     * @return void
     */
    public function testIsShortListApi($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $this->assertIsBool(Lists::isShortList(self::$phpcsFile, $target));
    }

    /**
     * Validate that the `IsShortArrayOrListWithCache::isShortArray()` method is accessible
     * and always returns a boolean value.
     *
     * @dataProvider dataEntryPoints
     *
     * @covers \PHPCSUtils\Internal\IsShortArrayOrListWithCache::isShortArray
     *
     * @param string                                   $testMarker The comment which prefaces the target token in the file.
     * @param int|string|array<int|string, int|string> $targetType The token type(s) to look for.
     *
     * @return void
     */
    public function testIsShortArrayInternal($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $this->assertIsBool(IsShortArrayOrListWithCache::isShortArray(self::$phpcsFile, $target));
    }

    /**
     * Validate that the `IsShortArrayOrListWithCache::isShortList()` method is accessible
     * and always returns a boolean value.
     *
     * @dataProvider dataEntryPoints
     *
     * @covers \PHPCSUtils\Internal\IsShortArrayOrListWithCache::isShortList
     *
     * @param string                                   $testMarker The comment which prefaces the target token in the file.
     * @param int|string|array<int|string, int|string> $targetType The token type(s) to look for.
     *
     * @return void
     */
    public function testIsShortListInternal($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $this->assertIsBool(IsShortArrayOrListWithCache::isShortList(self::$phpcsFile, $target));
    }

    /**
     * Validate that the `IsShortArrayOrListWithCache::getType()` method is accessible
     * and always returns a boolean value.
     *
     * @dataProvider dataEntryPoints
     *
     * @covers \PHPCSUtils\Internal\IsShortArrayOrListWithCache::getType
     *
     * @param string                                   $testMarker The comment which prefaces the target token in the file.
     * @param int|string|array<int|string, int|string> $targetType The token type(s) to look for.
     *
     * @return void
     */
    public function testGetTypeInternal($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $this->assertContains(IsShortArrayOrListWithCache::getType(self::$phpcsFile, $target), $this->validValues);
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, int|string|array<int|string, int|string>>>
     */
    public static function dataEntryPoints()
    {
        return [
            'not a square bracket' => [
                'testMarker' => '/* testLongArray */',
                'targetType' => \T_ARRAY,
            ],
            'short array' => [
                'testMarker' => '/* testShortArray */',
                'targetType' => StableCollections::$shortArrayListOpenTokensBC,
            ],
            'short list' => [
                'testMarker' => '/* testShortList */',
                'targetType' => StableCollections::$shortArrayListOpenTokensBC,
            ],
            'square bracket' => [
                'testMarker' => '/* testSquareBrackets */',
                'targetType' => StableCollections::$shortArrayListOpenTokensBC,
            ],
        ];
    }
}
