<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Internal\Cache;

use PHPCSUtils\Internal\Cache;
use PHPCSUtils\Tests\TypeProviderHelper;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the cache retrieval and cache clear methods.
 *
 * @group cache
 *
 * @coversDefaultClass \PHPCSUtils\Internal\Cache
 *
 * @since 1.0.0
 */
final class GetClearTest extends UtilityMethodTestCase
{

    /**
     * Original value for whether or not caching is enabled for this test run.
     *
     * @var bool
     */
    private static $origCacheEnabled;

    /**
     * Initialize PHPCS & tokenize the test case file.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = \dirname(\dirname(__DIR__)) . '/DummyFile.inc';

        /*
         * Make sure caching is ALWAYS enabled for these tests and
         * make sure these tests are run with a clear cache to start with.
         */
        self::$origCacheEnabled = Cache::$enabled;
        Cache::$enabled         = true;
        Cache::clear();

        parent::setUpTestFile();
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
            Cache::set(self::$phpcsFile, 'Utility1', $id, $dataset['input']);
            Cache::set(self::$phpcsFile, 'Utility2', $id, $dataset['input']);
        }
    }

    /**
     * Clear the cache between tests and reset the base fixer loop.
     *
     * @after
     *
     * @return void
     */
    protected function clearCacheAndFixer()
    {
        Cache::clear();
        self::$phpcsFile->fixer->enabled = false;
        self::$phpcsFile->fixer->loops   = 0;
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
        Cache::$enabled = self::$origCacheEnabled;
    }

    /**
     * Test that a cache for a loop which has not been set is identified correctly as such.
     *
     * @covers ::isCached
     *
     * @return void
     */
    public function testIsCachedWillReturnFalseForUnavailableLoop()
    {
        self::$phpcsFile->fixer->enabled = true;
        self::$phpcsFile->fixer->loops   = 3;
        $this->assertFalse(Cache::isCached(self::$phpcsFile, 'Utility1', 'numeric string'));
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
        $this->assertFalse(Cache::isCached(self::$phpcsFile, 'Utility3', 'numeric string'));
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
        $this->assertFalse(Cache::isCached(self::$phpcsFile, 'Utility1', 'this ID does not exist'));
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
        $wasEnabled     = Cache::$enabled;
        Cache::$enabled = false;

        $isCached = Cache::isCached(self::$phpcsFile, 'Utility1', 'numeric string');

        Cache::$enabled = $wasEnabled;

        $this->assertFalse($isCached);
    }

    /**
     * Test that retrieving a cache key for a loop which has not been set, yields null.
     *
     * @covers ::get
     *
     * @return void
     */
    public function testGetWillReturnNullForUnavailableLoop()
    {
        self::$phpcsFile->fixer->enabled = true;
        self::$phpcsFile->fixer->loops   = 2;
        $this->assertNull(Cache::get(self::$phpcsFile, 'Utility1', 'numeric string'));
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
        $this->assertNull(Cache::get(self::$phpcsFile, 'Utility3', 'numeric string'));
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
        $this->assertNull(Cache::get(self::$phpcsFile, 'Utility1', 'this ID does not exist'));
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
        $wasEnabled     = Cache::$enabled;
        Cache::$enabled = false;

        $retrieved = Cache::get(self::$phpcsFile, 'Utility1', 'numeric string');

        Cache::$enabled = $wasEnabled;

        $this->assertNull($retrieved);
    }

    /**
     * Test that retrieving a cache set for a loop which has not been set, yields an empty array.
     *
     * @covers ::getForFile
     *
     * @return void
     */
    public function testGetForFileWillReturnEmptyArrayForUnavailableLoop()
    {
        self::$phpcsFile->fixer->enabled = true;
        self::$phpcsFile->fixer->loops   = 15;
        $this->assertSame([], Cache::getForFile(self::$phpcsFile, 'Utility1'));
    }

    /**
     * Test that retrieving a cache set for a cache key which has not been set, yields an empty array.
     *
     * @covers ::getForFile
     *
     * @return void
     */
    public function testGetForFileWillReturnEmptyArrayForUnavailableKey()
    {
        $this->assertSame([], Cache::getForFile(self::$phpcsFile, 'Utility3'));
    }

    /**
     * Test that disabling the cache will short-circuit cache for file retrieval.
     *
     * @covers ::getForFile
     *
     * @return void
     */
    public function testGetForFileWillReturnEmptyArrayWhenCachingDisabled()
    {
        $wasEnabled     = Cache::$enabled;
        Cache::$enabled = false;

        $retrieved = Cache::getForFile(self::$phpcsFile, 'Utility1');

        Cache::$enabled = $wasEnabled;

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
            $this->assertEquals($expected, Cache::get(self::$phpcsFile, 'Utility1', $id));
        } else {
            $this->assertSame($expected, Cache::get(self::$phpcsFile, 'Utility1', $id));
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
     * Test that the `getForFile()` method correctly retrieves a subset of the cached data.
     *
     * @covers ::getForFile
     *
     * @return void
     */
    public function testGetForFile()
    {
        $dataSet1 = Cache::getForFile(self::$phpcsFile, 'Utility1');
        $dataSet2 = Cache::getForFile(self::$phpcsFile, 'Utility2');
        $this->assertSame($dataSet1, $dataSet2);
    }

    /**
     * Test that data cached during a previous loop will not be recognized once we're in a different loop.
     *
     * @covers ::isCached
     *
     * @return void
     */
    public function testIsCachedWillNotConfuseDataFromDifferentLoops()
    {
        $id = 50;

        // Set an initial cache value and verify it is available.
        Cache::set(self::$phpcsFile, __METHOD__, $id, 'Test value');

        $this->assertTrue(
            Cache::isCached(self::$phpcsFile, __METHOD__, $id),
            'Cache::isCached() could not find the originally cached value'
        );

        self::$phpcsFile->fixer->enabled = true;
        self::$phpcsFile->fixer->loops   = 2;

        // Verify that the original cache is disregarded.
        $this->assertFalse(
            Cache::isCached(self::$phpcsFile, __METHOD__, $id),
            'Cache::isCached() still found the originally cached value'
        );
    }

    /**
     * Test that data cached during a previous loop will not be returned once we're in a different loop.
     *
     * @covers ::get
     *
     * @return void
     */
    public function testGetWillNotConfuseDataFromDifferentLoops()
    {
        $id = 872;

        // Set an initial cache value and verify it is available.
        Cache::set(self::$phpcsFile, __METHOD__, $id, 'Test value');

        $this->assertTrue(
            Cache::isCached(self::$phpcsFile, __METHOD__, $id),
            'Cache::isCached() could not find the originally cached value'
        );

        self::$phpcsFile->fixer->enabled = true;
        self::$phpcsFile->fixer->loops   = 4;

        // Verify that the original cache is disregarded.
        $this->assertNull(
            Cache::get(self::$phpcsFile, __METHOD__, $id),
            'Cache::get() still returned the originally cached value'
        );
    }

    /**
     * Test that data cached during a previous loop will not be returned once we're in a different loop.
     *
     * @covers ::getForFile
     *
     * @return void
     */
    public function testGetForFileWillNotConfuseDataFromDifferentLoops()
    {
        $id = 233;

        // Set an initial cache value and verify it is available.
        Cache::set(self::$phpcsFile, __METHOD__, $id, 'Test value');

        $this->assertTrue(
            Cache::isCached(self::$phpcsFile, __METHOD__, $id),
            'Cache::isCached() could not find the originally cached value'
        );

        self::$phpcsFile->fixer->enabled = true;
        self::$phpcsFile->fixer->loops   = 1;

        // Verify that the original cache is disregarded.
        $this->assertSame(
            [],
            Cache::getForFile(self::$phpcsFile, __METHOD__),
            'Cache::getForFile() still returned the originally cached value'
        );
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
        Cache::clear();

        $this->assertFalse(Cache::isCached(self::$phpcsFile, 'Utility1', $id));
        $this->assertFalse(Cache::isCached(self::$phpcsFile, 'Utility2', $id));
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
