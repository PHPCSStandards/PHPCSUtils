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
 * Tests for the caching functionality.
 *
 * @group cache
 *
 * @covers \PHPCSUtils\Internal\Cache::isCached
 * @covers \PHPCSUtils\Internal\Cache::get
 * @covers \PHPCSUtils\Internal\Cache::set
 *
 * @since 1.0.0
 */
final class SetTest extends UtilityMethodTestCase
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
     * Test that every data type is accepted as a cachable value, including `null`, that the
     * `Cache::isCached()` function recognizes a set value correctly and that all values can be retrieved.
     *
     * @dataProvider dataEveryTypeOfInput
     *
     * @param mixed $input Value to cache.
     *
     * @return void
     */
    public function testSetAcceptsEveryTypeOfInput($input)
    {
        $id = \base_convert((string) \rand((int) 10e13, (int) 10e17), 10, 36);
        Cache::set(self::$phpcsFile, __METHOD__, $id, $input);

        $this->assertTrue(
            Cache::isCached(self::$phpcsFile, __METHOD__, $id),
            'Cache::isCached() could not find the cached value'
        );

        $this->assertSame(
            $input,
            Cache::get(self::$phpcsFile, __METHOD__, $id),
            'Value retrieved via Cache::get() did not match expectations'
        );
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dataEveryTypeOfInput()
    {
        return TypeProviderHelper::getAll();
    }

    /**
     * Verify that all supported types of IDs are accepted.
     *
     * Note: this doesn't test the unhappy path of passing an unsupported type of key, but
     * non-int/string keys are not accepted by PHP for arrays anyway, so that should result
     * in PHP warnings/errors anyway and as this is an internal class, I'm not too concerned
     * about that kind of mistake being made.
     *
     * @dataProvider dataSetAcceptsIntAndStringIdKeys
     *
     * @param int|string $id ID for the cache.
     *
     * @return void
     */
    public function testSetAcceptsIntAndStringIdKeys($id)
    {
        $value = 'value' . $id;

        Cache::set(self::$phpcsFile, __METHOD__, $id, $value);

        $this->assertTrue(
            Cache::isCached(self::$phpcsFile, __METHOD__, $id),
            'Cache::isCached() could not find the cached value'
        );

        $this->assertSame(
            $value,
            Cache::get(self::$phpcsFile, __METHOD__, $id),
            'Value retrieved via Cache::get() did not match expectations'
        );
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataSetAcceptsIntAndStringIdKeys()
    {
        return [
            'ID: int zero' => [
                'id' => 0,
            ],
            'ID: positive int' => [
                'id' => 12832,
            ],
            'ID: negative int' => [
                'id' => -274,
            ],
            'ID: string' => [
                'id' => 'string ID',
            ],
        ];
    }

    /**
     * Verify that a previously set cached value will be overwritten when set() is called again
     * with the same file, ID and key.
     *
     * @return void
     */
    public function testSetWillOverwriteExistingValue()
    {
        $id        = 'my key';
        $origValue = 'original value';

        Cache::set(self::$phpcsFile, __METHOD__, $id, $origValue);

        // Verify that the original value was set correctly.
        $this->assertTrue(
            Cache::isCached(self::$phpcsFile, __METHOD__, $id),
            'Cache::isCached() could not find the originally cached value'
        );
        $this->assertSame(
            $origValue,
            Cache::get(self::$phpcsFile, __METHOD__, $id),
            'Original value retrieved via Cache::get() did not match expectations'
        );

        $newValue = 'new value';
        Cache::set(self::$phpcsFile, __METHOD__, $id, $newValue);

        // Verify the overwrite happened.
        $this->assertTrue(
            Cache::isCached(self::$phpcsFile, __METHOD__, $id),
            'Cache::isCached() could not find the newly cached value'
        );
        $this->assertSame(
            $newValue,
            Cache::get(self::$phpcsFile, __METHOD__, $id),
            'New value retrieved via Cache::get() did not match expectations'
        );
    }

    /**
     * Verify that disabling the cache will short-circuit caching (and not eat memory).
     *
     * @return void
     */
    public function testSetWillNotSaveDataWhenCachingIsDisabled()
    {
        $id    = 'id';
        $value = 'value';

        $wasEnabled     = Cache::$enabled;
        Cache::$enabled = false;

        Cache::set(self::$phpcsFile, __METHOD__, $id, $value);

        Cache::$enabled = $wasEnabled;

        $this->assertFalse(
            Cache::isCached(self::$phpcsFile, __METHOD__, $id),
            'Cache::isCached() found a cache which was set while caching was disabled'
        );

        $this->assertNull(
            Cache::get(self::$phpcsFile, __METHOD__, $id),
            'Value retrieved via Cache::get() did not match expectations'
        );
    }

    /**
     * Test that previously cached data is no longer available if the fixer has moved on to the next loop.
     *
     * @return void
     */
    public function testSetDoesNotClearCacheWhenFixerDisabled()
    {
        $idA = 50;
        $idB = 52;

        // Set an initial cache value and verify it is available.
        Cache::set(self::$phpcsFile, __METHOD__, $idA, 'Test value');

        $this->assertTrue(
            Cache::isCached(self::$phpcsFile, __METHOD__, $idA),
            'Cache::isCached() could not find the originally cached value'
        );

        // Changing loops without the fixer being available should have no effect.
        self::$phpcsFile->fixer->loops = 5;
        Cache::set(self::$phpcsFile, 'Another method', $idB, 'Test value');

        // Verify the original cache is still available.
        $this->assertTrue(
            Cache::isCached(self::$phpcsFile, __METHOD__, $idA),
            'Cache::isCached() could not find the originally cached value'
        );
        // ... as well as the newly cached value
        $this->assertTrue(
            Cache::isCached(self::$phpcsFile, 'Another method', $idB),
            'Cache::isCached() could not find the newly cached value'
        );
    }

    /**
     * Test that previously cached data is no longer available if the fixer has moved on to the next loop.
     *
     * @return void
     */
    public function testSetClearsCacheOnDifferentLoop()
    {
        $idA = 123;
        $idB = 276;

        // Set an initial cache value and verify it is available.
        Cache::set(self::$phpcsFile, __METHOD__, $idA, 'Test value');

        $this->assertTrue(
            Cache::isCached(self::$phpcsFile, __METHOD__, $idA),
            'Cache::isCached() could not find the originally cached value'
        );

        // Set a different cache on a different loop, which should clear the original cache.
        self::$phpcsFile->fixer->enabled = true;
        self::$phpcsFile->fixer->loops   = 5;
        Cache::set(self::$phpcsFile, 'Another method', $idB, 'Test value');

        // Verify the original cache is no longer available.
        $this->assertFalse(
            Cache::isCached(self::$phpcsFile, __METHOD__, $idA),
            'Cache::isCached() still found the originally cached value'
        );
    }
}
