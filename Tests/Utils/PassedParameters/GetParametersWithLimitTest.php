<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\PassedParameters;

use PHPCSUtils\Internal\Cache;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\PassedParameters;

/**
 * Tests for the \PHPCSUtils\Utils\PassedParameters::getParameters() methods for
 * when the $limit parameter has been passed.
 *
 * @covers \PHPCSUtils\Utils\PassedParameters::getParameters
 *
 * @since 1.0.0
 */
final class GetParametersWithLimitTest extends UtilityMethodTestCase
{

    /**
     * Test retrieving the parameter details with a limit from an array without parameters.
     *
     * @covers \PHPCSUtils\Utils\PassedParameters::getParameters
     * @covers \PHPCSUtils\Utils\PassedParameters::getParameter
     *
     * @return void
     */
    public function testGetParametersWithLimitNoParams()
    {
        $stackPtr = $this->getTargetToken('/* testNoParams */', \T_ARRAY);

        $result = PassedParameters::getParameters(self::$phpcsFile, $stackPtr, 3);
        $this->assertSame([], $result);
        $this->assertCount(0, $result);

        // Limit is automatically applied to getParameter().
        $result = PassedParameters::getParameter(self::$phpcsFile, $stackPtr, 3);
        $this->assertFalse($result);
    }

    /**
     * Test passing an invalid limit.
     *
     * @dataProvider dataGetParametersWithIneffectiveLimit
     *
     * @param mixed $limit Parameter value for the $limit parameter.
     *
     * @return void
     */
    public function testGetParametersWithIneffectiveLimit($limit)
    {
        $stackPtr = $this->getTargetToken('/* testFunctionCall */', \T_STRING);

        $result = PassedParameters::getParameters(self::$phpcsFile, $stackPtr, $limit);
        $this->assertNotEmpty($result);
        $this->assertCount(7, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetParametersWithIneffectiveLimit() For the array format.
     *
     * @return array<string, array<mixed>>
     */
    public static function dataGetParametersWithIneffectiveLimit()
    {
        return [
            'invalid-limit-wrong-type-null'       => [null],
            'invalid-limit-wrong-type-bool'       => [true],
            'invalid-limit-wrong-type-string'     => ['10'],
            'invalid-limit-negative-int'          => [-10],
            'valid-limit-set-to-0 = no-limit'     => [0],
            'valid-limit-higher-than-param-count' => [10],
        ];
    }

    /**
     * Test retrieving the parameter details from a function call or construct.
     *
     * @dataProvider dataGetParametersWithLimit
     *
     * @param string                                $testMarker The comment which prefaces the target token in the test file.
     * @param int|string                            $targetType The type of token to look for.
     * @param int                                   $limit      The number of parameters to limit this call to.
     *                                                          Should match the expected count.
     * @param array<int, array<string, int|string>> $expected   Optional. The expected return value. Only tested when
     *                                                          not empty.
     *
     * @return void
     */
    public function testGetParametersWithLimit($testMarker, $targetType, $limit, $expected = [])
    {
        $stackPtr = $this->getTargetToken($testMarker, [$targetType]);

        $result = PassedParameters::getParameters(self::$phpcsFile, $stackPtr, $limit);
        $this->assertNotEmpty($result);
        $this->assertCount($limit, $result);

        if (empty($expected) === true) {
            return;
        }

        // Start/end token position values in the expected array are set as offsets
        // in relation to the target token.
        // Change these to exact positions based on the retrieved stackPtr.
        foreach ($expected as $key => $value) {
            $expected[$key]['start'] = ($stackPtr + $value['start']);
            $expected[$key]['end']   = ($stackPtr + $value['end']);
        }

        foreach ($result as $key => $value) {
            // The GetTokensAsString functions have their own tests, no need to duplicate it here.
            unset($result[$key]['clean']);
        }

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetParametersWithLimit() For the array format.
     *
     * @return array<string, array<string, int|string|array<int, array<string, int|string>>>>
     */
    public static function dataGetParametersWithLimit()
    {
        return [
            'function-call' => [
                'testMarker' => '/* testFunctionCall */',
                'targetType' => \T_STRING,
                'limit'      => 2,
            ],
            'long-array-no-keys' => [
                'testMarker' => '/* testSimpleLongArray */',
                'targetType' => \T_ARRAY,
                'limit'      => 1,
                'expected'   => [
                    1 => [
                        'start' => 2,
                        'end'   => 3,
                        'raw'   => '1',
                    ],
                ],
            ],
            'short-array-no-keys' => [
                'testMarker' => '/* testSimpleShortArray */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'limit'      => 5,
            ],
            'long-array-with-keys' => [
                'testMarker' => '/* testLongArrayWithKeys */',
                'targetType' => \T_ARRAY,
                'limit'      => 7,
            ],
            'short-array-with-keys' => [
                'testMarker' => '/* testShortArrayWithKeys */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'limit'      => 4,
            ],
        ];
    }

    /**
     * Verify that the build-in caching is used when caching is enabled and that the caching keeps track
     * of the used limit.
     *
     * @return void
     */
    public function testResultIsCachedWithLimit()
    {
        // Can't re-use test cases for these tests, as in that case, the cache _may_ already be set (for 0 limit).
        $methodName = 'PHPCSUtils\\Utils\\PassedParameters::getParameters';
        $testMarker = '/* testCachedWithLimit */';
        $stackPtr   = $this->getTargetToken($testMarker, \T_ARRAY);
        $limit      = 1;
        $expected   = [
            1 => [
                'start' => 2 + $stackPtr,
                'end'   => 3 + $stackPtr,
                'raw'   => '1',
                'clean' => '1',
            ],
        ];

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = PassedParameters::getParameters(self::$phpcsFile, $stackPtr, $limit);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, "$stackPtr-$limit");
        $resultSecondRun = PassedParameters::getParameters(self::$phpcsFile, $stackPtr, $limit);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }

    /**
     * Verify that when a previous query for the same token was made with a limit which was more than
     * the item count, the result was cached as if no limit was set.
     *
     * @return void
     */
    public function testResultIsCachedWithoutLimitWhenTotalItemsLessThanLimit()
    {
        // Can't re-use test cases for these tests, as in that case, the cache _may_ already be set (for 0 limit).
        $methodName = 'PHPCSUtils\\Utils\\PassedParameters::getParameters';
        $testMarker = '/* testCachedWithoutLimitWhenTotalItemsLessThanLimit */';
        $stackPtr   = $this->getTargetToken($testMarker, \T_ARRAY);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun       = PassedParameters::getParameters(self::$phpcsFile, $stackPtr, 10);
        $isCachedWithLimit    = Cache::isCached(self::$phpcsFile, $methodName, "$stackPtr-10");
        $isCachedWithoutLimit = Cache::isCached(self::$phpcsFile, $methodName, "$stackPtr-0");
        $resultSecondRun      = PassedParameters::getParameters(self::$phpcsFile, $stackPtr, 10);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertCount(7, $resultFirstRun, 'Count of first result did not match expectation');
        $this->assertFalse($isCachedWithLimit, 'Cache::isCached() found a cached value with key "ptr-10"');
        $this->assertTrue($isCachedWithoutLimit, 'Cache::isCached() did not find a cached value with key "ptr-0"');
        $this->assertCount(7, $resultSecondRun, 'Count of second result did not match expectation');
    }

    /**
     * Verify that when a previous query for the same token was made with a limit which matched the item count,
     * the result was cached as if no limit was set.
     *
     * @return void
     */
    public function testResultIsCachedWithoutLimitWhenTotalItemsEqualsLimit()
    {
        // Can't re-use test cases for these tests, as in that case, the cache _may_ already be set (for 0 limit).
        $methodName = 'PHPCSUtils\\Utils\\PassedParameters::getParameters';
        $testMarker = '/* testCachedWithoutLimitWhenTotalItemsEqualsLimit */';
        $stackPtr   = $this->getTargetToken($testMarker, \T_OPEN_SHORT_ARRAY);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun       = PassedParameters::getParameters(self::$phpcsFile, $stackPtr, 7);
        $isCachedWithLimit    = Cache::isCached(self::$phpcsFile, $methodName, "$stackPtr-7");
        $isCachedWithoutLimit = Cache::isCached(self::$phpcsFile, $methodName, "$stackPtr-0");
        $resultSecondRun      = PassedParameters::getParameters(self::$phpcsFile, $stackPtr, 7);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertCount(7, $resultFirstRun, 'Count of first result did not match expectation');
        $this->assertFalse($isCachedWithLimit, 'Cache::isCached() found a cached value with key "ptr-7"');
        $this->assertTrue($isCachedWithoutLimit, 'Cache::isCached() did not find a cached value with key "ptr-0"');
        $this->assertCount(7, $resultSecondRun, 'Count of second result did not match expectation');
    }

    /**
     * Verify that when a previous query for the same token without limit was made, the result
     * cache is used to retrieve the limited result.
     *
     * @return void
     */
    public function testResultIsRetrievedFromCacheWhenCachePreviouslySetWithoutLimit()
    {
        // Can't re-use test cases for these tests, as in that case, the cache _may_ already be set (for 0 limit).
        $methodName = 'PHPCSUtils\\Utils\\PassedParameters::getParameters';
        $testMarker = '/* testRetrievedFromCacheWhenCachePreviouslySetWithoutLimit */';
        $stackPtr   = $this->getTargetToken($testMarker, \T_STRING);
        $limit      = 2;
        $expected   = [
            1 => [
                'start' => 2 + $stackPtr,
                'end'   => 3 + $stackPtr,
                'raw'   => '1',
                'clean' => '1',
            ],
            2 => [
                'start' => 5 + $stackPtr,
                'end'   => 6 + $stackPtr,
                'raw'   => '2',
                'clean' => '2',
            ],
        ];

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = PassedParameters::getParameters(self::$phpcsFile, $stackPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, "$stackPtr-0");
        $resultSecondRun = PassedParameters::getParameters(self::$phpcsFile, $stackPtr, $limit);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertCount(7, $resultFirstRun, 'Count of first result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() did not find a cached value with key "ptr-0"');
        $this->assertSame($expected, $resultSecondRun, 'Second result did not match the expected result');
    }

    /**
     * Verify that when a previous query for the same token was made with a limit which was less than the total
     * number of items and the current limit does not match, the function will not use the cache.
     *
     * @return void
     */
    public function testResultIsRetrievedFromScratchIfNoSuitableCacheFound()
    {
        static $firstRun = true;

        // Can't re-use test cases for this test, as in that case, the cache _may_ already be set (for 0 limit).
        $methodName = 'PHPCSUtils\\Utils\\PassedParameters::getParameters';
        $testMarker = '/* testRetrievedFromScratchIfNoSuitableCacheFound */';
        $stackPtr   = $this->getTargetToken($testMarker, \T_STRING);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun         = PassedParameters::getParameters(self::$phpcsFile, $stackPtr, 2);
        $isCachedWithLimit2     = Cache::isCached(self::$phpcsFile, $methodName, "$stackPtr-2");
        $isCachedWithLimit4pre  = Cache::isCached(self::$phpcsFile, $methodName, "$stackPtr-4");
        $isCachedWithoutLimit   = Cache::isCached(self::$phpcsFile, $methodName, "$stackPtr-0");
        $resultSecondRun        = PassedParameters::getParameters(self::$phpcsFile, $stackPtr, 4);
        $isCachedWithLimit4post = Cache::isCached(self::$phpcsFile, $methodName, "$stackPtr-4");

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertCount(2, $resultFirstRun, 'Count of first result did not match expectation');
        $this->assertTrue($isCachedWithLimit2, 'Cache::isCached() did not find a cached value with key "ptr-2"');
        if ($firstRun === true) {
            $this->assertFalse($isCachedWithLimit4pre, 'Cache::isCached() found a cached value with key "ptr-4"');
        } else {
            $this->assertTrue($isCachedWithLimit4pre, 'Cache::isCached() did not find a cached value with key "ptr-4"');
        }
        $this->assertFalse($isCachedWithoutLimit, 'Cache::isCached() found a cached value with key "ptr-0"');
        $this->assertCount(4, $resultSecondRun, 'Count of second result did not match expectation');
        $this->assertTrue($isCachedWithLimit4post, 'Cache::isCached() did not find a cached value with key "ptr-4"');

        $firstRun = false;
    }
}
