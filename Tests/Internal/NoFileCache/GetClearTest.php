<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Internal\NoFileCache;

use PHPCSUtils\Internal\NoFileCache;
use PHPCSUtils\Tests\TypeProviderHelper;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the cache retrieval and cache clear methods.
 *
 * @group cache
 *
 * @coversDefaultClass \PHPCSUtils\Internal\NoFileCache
 *
 * @since 1.0.0
 */
final class GetClearTest extends TestCase
{

    /**
     * Original value for whether or not caching is enabled for this test run.
     *
     * @var bool
     */
    private static $origCacheEnabled;

    /**
     * Enable caching.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function enableCache()
    {
        /*
         * Make sure caching is ALWAYS enabled for these tests and
         * make sure these tests are run with a clear cache to start with.
         */
        self::$origCacheEnabled = NoFileCache::$enabled;
        NoFileCache::$enabled   = true;
        NoFileCache::clear();
    }

    /**
     * Fill the initial cache.
     *
     * @before
     *
     * @return void
     */
    protected function createCache()
    {
        $data = TypeProviderHelper::getAll();
        foreach ($data as $id => $dataset) {
            NoFileCache::set('Utility1', $id, $dataset['input']);
            NoFileCache::set('Utility2', $id, $dataset['input']);
        }
    }

    /**
     * Clear the cache between tests.
     *
     * @after
     *
     * @return void
     */
    protected function clearCache()
    {
        NoFileCache::clear();
    }

    /**
     * Reset the caching status.
     *
     * @afterClass
     *
     * @return void
     */
    public static function resetCachingStatus()
    {
        NoFileCache::$enabled = self::$origCacheEnabled;
    }

    /**
     * Test that a cache key which has not been set is identified correctly as such.
     *
     * @covers ::isCached
     *
     * @return void
     */
    public function testIsCachedWillReturnFalseForUnavailableKey()
    {
        $this->assertFalse(NoFileCache::isCached('Utility3', 'numeric string'));
    }

    /**
     * Test that a cache id which has not been set is identified correctly as such.
     *
     * @covers ::isCached
     *
     * @return void
     */
    public function testIsCachedWillReturnFalseForUnavailableId()
    {
        $this->assertFalse(NoFileCache::isCached('Utility1', 'this ID does not exist'));
    }

    /**
     * Test that disabling the cache will short-circuit cache checking.
     *
     * @covers ::isCached
     *
     * @return void
     */
    public function testIsCachedWillReturnFalseWhenCachingDisabled()
    {
        $wasEnabled           = NoFileCache::$enabled;
        NoFileCache::$enabled = false;

        $isCached = NoFileCache::isCached('Utility1', 'numeric string');

        NoFileCache::$enabled = $wasEnabled;

        $this->assertFalse($isCached);
    }

    /**
     * Test that retrieving a cache key which has not been set, yields null.
     *
     * @covers ::get
     *
     * @return void
     */
    public function testGetWillReturnNullForUnavailableKey()
    {
        $this->assertNull(NoFileCache::get('Utility3', 'numeric string'));
    }

    /**
     * Test that retrieving a cache id which has not been set, yields null.
     *
     * @covers ::get
     *
     * @return void
     */
    public function testGetWillReturnNullForUnavailableId()
    {
        $this->assertNull(NoFileCache::get('Utility1', 'this ID does not exist'));
    }

    /**
     * Test that disabling the cache will short-circuit cache retrieval.
     *
     * @covers ::get
     *
     * @return void
     */
    public function testGetWillReturnNullWhenCachingDisabled()
    {
        $wasEnabled           = NoFileCache::$enabled;
        NoFileCache::$enabled = false;

        $retrieved = NoFileCache::get('Utility1', 'numeric string');

        NoFileCache::$enabled = $wasEnabled;

        $this->assertNull($retrieved);
    }

    /**
     * Test that retrieving a cache set for a cache key which has not been set, yields an empty array.
     *
     * @covers ::getForKey
     *
     * @return void
     */
    public function testGetForKeyWillReturnEmptyArrayForUnavailableData()
    {
        $this->assertSame([], NoFileCache::getForKey('Utility3'));
    }

    /**
     * Test that disabling the cache will short-circuit cache for key retrieval.
     *
     * @covers ::getForKey
     *
     * @return void
     */
    public function testGetForKeyWillReturnEmptyArrayWhenCachingDisabled()
    {
        $wasEnabled           = NoFileCache::$enabled;
        NoFileCache::$enabled = false;

        $retrieved = NoFileCache::getForKey('Utility1');

        NoFileCache::$enabled = $wasEnabled;

        $this->assertSame([], $retrieved);
    }

    /**
     * Test that previously cached data can be retrieved correctly.
     *
     * @dataProvider dataEveryTypeOfInput
     *
     * @covers ::get
     *
     * @param int|string $id       The ID of the cached value to retrieve.
     * @param mixed      $expected The expected cached value.
     *
     * @return void
     */
    public function testGetWillRetrievedPreviouslySetValue($id, $expected)
    {
        if (\is_object($expected)) {
            $this->assertEquals($expected, NoFileCache::get('Utility1', $id));
        } else {
            $this->assertSame($expected, NoFileCache::get('Utility1', $id));
        }
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dataEveryTypeOfInput()
    {
        $allTypes = TypeProviderHelper::getAll();
        $data     = [];
        foreach ($allTypes as $key => $dataset) {
            $data[$key] = [
                'id'       => $key,
                'expected' => $dataset['input'],
            ];
        }

        return $data;
    }

    /**
     * Test that the `getForKey()` method correctly retrieves a subset of the cached data.
     *
     * @covers ::getForKey
     *
     * @return void
     */
    public function testGetForKey()
    {
        $dataSet1 = NoFileCache::getForKey('Utility1');
        $dataSet2 = NoFileCache::getForKey('Utility2');
        $this->assertSame($dataSet1, $dataSet2);
    }

    /**
     * Test that previously cached data is no longer available when the cache has been cleared.
     *
     * @dataProvider dataClearCache
     *
     * @covers ::clear
     *
     * @param int|string $id The ID of the cached value to retrieve.
     *
     * @return void
     */
    public function testClearCache($id)
    {
        NoFileCache::clear();

        $this->assertFalse(NoFileCache::isCached('Utility1', $id));
        $this->assertFalse(NoFileCache::isCached('Utility2', $id));
    }

    /**
     * Data provider.
     *
     * @see testClearCache() For the array format.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataClearCache()
    {
        $data = self::dataEveryTypeOfInput();

        foreach ($data as $name => $dataset) {
            unset($data[$name]['expected']);
        }

        return $data;
    }
}
