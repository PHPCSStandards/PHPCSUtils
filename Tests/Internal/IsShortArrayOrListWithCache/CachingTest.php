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

use PHPCSUtils\Internal\Cache;
use PHPCSUtils\Internal\IsShortArrayOrList;
use PHPCSUtils\Internal\IsShortArrayOrListWithCache;
use PHPCSUtils\Internal\StableCollections;
use PHPCSUtils\Tests\Internal\IsShortArrayOrListWithCache\IsShortArrayOrListWithCacheTestCase;

/**
 * Tests for the \PHPCSUtils\Utils\IsShortArrayOrListWithCache class.
 *
 * @covers \PHPCSUtils\Internal\IsShortArrayOrListWithCache::process
 * @covers \PHPCSUtils\Internal\IsShortArrayOrListWithCache::getFromCache
 * @covers \PHPCSUtils\Internal\IsShortArrayOrListWithCache::updateCache
 *
 * @since 1.0.0
 */
final class CachingTest extends IsShortArrayOrListWithCacheTestCase
{

    /**
     * Verify that the build-in caching is used when caching is enabled and that the cache is only saved
     * on the bracket opener, not on the closer.
     *
     * @dataProvider dataResultIsCached
     *
     * @param string      $testMarker The comment which prefaces the target token in the test file.
     * @param string|bool $expected   Expected function return value.
     *
     * @return void
     */
    public function testResultIsCached($testMarker, $expected)
    {
        $opener = $this->getTargetToken($testMarker, StableCollections::$shortArrayListOpenTokensBC);
        $closer = $this->getTargetToken($testMarker, [\T_CLOSE_SHORT_ARRAY, \T_CLOSE_SQUARE_BRACKET]);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = IsShortArrayOrListWithCache::getType(self::$phpcsFile, $closer);
        $isCachedOpener  = Cache::isCached(self::$phpcsFile, IsShortArrayOrListWithCache::CACHE_KEY, $opener);
        $isCachedCloser  = Cache::isCached(self::$phpcsFile, IsShortArrayOrListWithCache::CACHE_KEY, $closer);
        $resultSecondRun = IsShortArrayOrListWithCache::getType(self::$phpcsFile, $opener);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCachedOpener, 'Cache::isCached() could not find the cached value');
        $this->assertFalse($isCachedCloser, 'Cache::isCached() erroneously found a cached value for the closer');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataResultIsCached()
    {
        return [
            'short array' => [
                'testMarker' => '/* testShortArray */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'short list' => [
                'testMarker' => '/* testShortList */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'square bracket' => [
                'testMarker' => '/* testSquareBrackets */',
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
        ];
    }

    /**
     * Verify that the result is not cached when an invalid token is passed.
     *
     * @return void
     */
    public function testInvalidTokenResultIsNotCached()
    {
        $stackPtr = $this->getTargetToken('/* testLongArray */', \T_ARRAY);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = IsShortArrayOrListWithCache::getType(self::$phpcsFile, $stackPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, IsShortArrayOrListWithCache::CACHE_KEY, $stackPtr);
        $resultSecondRun = IsShortArrayOrListWithCache::getType(self::$phpcsFile, $stackPtr);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertFalse($resultFirstRun, 'First result did not match expectation');
        $this->assertFalse($isCached, 'Cache::isCached() erroneously found a cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }
}
