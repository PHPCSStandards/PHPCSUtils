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

use PHPCSUtils\Internal\Cache;
use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\Arrays;
use PHPCSUtils\Utils\PassedParameters;

/**
 * Tests for the \PHPCSUtils\Utils\Arrays::getDoubleArrowPtr() method.
 *
 * @covers \PHPCSUtils\Utils\Arrays::getDoubleArrowPtr
 *
 * @since 1.0.0
 */
final class GetDoubleArrowPtrTest extends PolyfilledTestCase
{

    /**
     * Cache for the parsed parameters array.
     *
     * @var array<string, array<string, int|string>>
     */
    private static $parameters = [];

    /**
     * Set up the parameters cache for the tests.
     *
     * Retrieves the parameters array only once and caches it as it won't change
     * between the tests anyway.
     *
     * @before
     *
     * @return void
     */
    protected function setUpCache()
    {
        if (empty(self::$parameters) === true) {
            $target     = $this->getTargetToken('/* testGetDoubleArrowPtr */', [\T_OPEN_SHORT_ARRAY]);
            $parameters = PassedParameters::getParameters(self::$phpcsFile, $target);

            foreach ($parameters as $values) {
                \preg_match('`^(/\* test[^*]+ \*/)`', $values['raw'], $matches);
                if (empty($matches[1]) === false) {
                    self::$parameters[$matches[1]] = $values;
                }
            }
        }
    }

    /**
     * Test receiving an expected exception when an invalid start position is passed.
     *
     * @return void
     */
    public function testNonIntegerStartException()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #2 ($start) must be of type integer, boolean given');

        Arrays::getDoubleArrowPtr(self::$phpcsFile, false, 10);
    }

    /**
     * Test receiving an expected exception when an invalid end position is passed.
     *
     * @return void
     */
    public function testNonIntegerEndException()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #3 ($end) must be of type integer, boolean given');

        Arrays::getDoubleArrowPtr(self::$phpcsFile, 0, false);
    }

    /**
     * Test receiving an expected exception when an invalid start position is passed.
     *
     * @return void
     */
    public function testInvalidStartPositionException()
    {
        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage(
            'Argument #2 ($start) must be a stack pointer which exists in the $phpcsFile object'
        );

        Arrays::getDoubleArrowPtr(self::$phpcsFile, -10, 10);
    }

    /**
     * Test receiving an expected exception when an invalid end position is passed.
     *
     * @return void
     */
    public function testInvalidEndPositionException()
    {
        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage(
            'Argument #3 ($end) must be a stack pointer which exists in the $phpcsFile object'
        );

        Arrays::getDoubleArrowPtr(self::$phpcsFile, 0, 100000);
    }

    /**
     * Test receiving an expected exception when the start position is after the end position.
     *
     * @return void
     */
    public function testInvalidStartEndPositionException()
    {
        $this->expectException('PHPCSUtils\Exceptions\LogicException');
        $this->expectExceptionMessage(
            'The $start token must be before the $end token. Received: $start 10, $end 5'
        );

        Arrays::getDoubleArrowPtr(self::$phpcsFile, 10, 5);
    }

    /**
     * Test retrieving the position of the double arrow for an array parameter.
     *
     * @dataProvider dataGetDoubleArrowPtr
     *
     * @param string   $testMarker The comment which is part of the target array item in the test file.
     * @param int|bool $expected   The expected function call result.
     *
     * @return void
     */
    public function testGetDoubleArrowPtr($testMarker, $expected)
    {
        if (isset(self::$parameters[$testMarker]) === false) {
            $this->fail('Test case not found for ' . $testMarker);
        }

        $start = self::$parameters[$testMarker]['start'];
        $end   = self::$parameters[$testMarker]['end'];

        // Change double arrow position from offset to exact position.
        if ($expected !== false) {
            $expected += $start;
        }

        $result = Arrays::getDoubleArrowPtr(self::$phpcsFile, $start, $end);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * The double arrow positions are provided as offsets from the $start stackPtr.
     *
     * @see testGetDoubleArrowPtr()
     *
     * @return array<string, array<string, int|string|false>>
     */
    public static function dataGetDoubleArrowPtr()
    {
        return [
            'test-no-arrow' => [
                'testMarker' => '/* testValueNoArrow */',
                'expected'   => false,
            ],
            'test-arrow-numeric-index' => [
                'testMarker' => '/* testArrowNumericIndex */',
                'expected'   => 8,
            ],
            'test-arrow-string-index' => [
                'testMarker' => '/* testArrowStringIndex */',
                'expected'   => 8,
            ],
            'test-arrow-multi-token-index' => [
                'testMarker' => '/* testArrowMultiTokenIndex */',
                'expected'   => 12,
            ],
            'test-no-arrow-value-short-array' => [
                'testMarker' => '/* testNoArrowValueShortArray */',
                'expected'   => false,
            ],
            'test-no-arrow-value-long-array' => [
                'testMarker' => '/* testNoArrowValueLongArray */',
                'expected'   => false,
            ],
            'test-no-arrow-value-nested-arrays' => [
                'testMarker' => '/* testNoArrowValueNestedArrays */',
                'expected'   => false,
            ],
            'test-no-arrow-value-closure' => [
                'testMarker' => '/* testNoArrowValueClosure */',
                'expected'   => false,
            ],
            'test-arrow-value-short-array' => [
                'testMarker' => '/* testArrowValueShortArray */',
                'expected'   => 8,
            ],
            'test-arrow-value-long-array' => [
                'testMarker' => '/* testArrowValueLongArray */',
                'expected'   => 8,
            ],
            'test-arrow-value-closure' => [
                'testMarker' => '/* testArrowValueClosure */',
                'expected'   => 8,
            ],
            'test-no-arrow-value-anon-class-with-foreach' => [
                'testMarker' => '/* testNoArrowValueAnonClassForeach */',
                'expected'   => false,
            ],
            'test-no-arrow-value-closure-with-keyed-yield' => [
                'testMarker' => '/* testNoArrowValueClosureYieldWithKey */',
                'expected'   => false,
            ],
            'test-arrow-key-closure-with-keyed-yield' => [
                'testMarker' => '/* testArrowKeyClosureYieldWithKey */',
                'expected'   => 25,
            ],
            'test-arrow-value-fn-function' => [
                'testMarker' => '/* testFnFunctionWithKey */',
                'expected'   => 8,
            ],
            'test-no-arrow-value-fn-function' => [
                'testMarker' => '/* testNoArrowValueFnFunction */',
                'expected'   => false,
            ],
            'test-arrow-tstring-key-not-fn-function' => [
                'testMarker' => '/* testTstringKeyNotFnFunction */',
                'expected'   => 8,
            ],
            // Test specifically for PHPCS 3.5.3 and 3.5.4 in which all "fn" tokens were tokenized as T_FN.
            // While these PHPCS versions are no longer supported, the test remains to safeguard against
            // tokenizer regressions.
            'test-arrow-access-to-property-named-fn-as-key-phpcs-3.5.3-3.5.4' => [
                'testMarker' => '/* testKeyPropertyAccessFnPHPCS353-354 */',
                'expected'   => 12,
            ],
            'test-double-arrow-incorrectly-tokenized-phpcs-issue-2865' => [
                'testMarker' => '/* testDoubleArrowTokenizedAsTstring-PHPCS2865 */',
                'expected'   => 10,
            ],

            // Safeguard that PHP 8.0 match expressions are handled correctly.
            'test-no-arrow-value-match-expression' => [
                'testMarker' => '/* testNoArrowValueMatchExpr */',
                'expected'   => false,
            ],
            'test-double-arrow-value-match-expression' => [
                'testMarker' => '/* testArrowValueMatchExpr */',
                'expected'   => 8,
            ],
            'test-double-arrow-key-match-expression' => [
                'testMarker' => '/* testArrowKeyMatchExpr */',
                'expected'   => 38,
            ],

            // Safeguard that PHP 7.2 keyed lists in values are handled correctly.
            'test-no-arrow-value-keyed-long-list' => [
                'testMarker' => '/* testNoArrowKeyedLongListInValue */',
                'expected'   => false,
            ],
            'test-no-arrow-value-keyed-short-list' => [
                'testMarker' => '/* testNoArrowKeyedShortListInValue */',
                'expected'   => false,
            ],

            // Safeguard that double arrows in PHP 8.0 attributes are disregarded.
            'test-no-arrow-value-closure-with-attached-attribute-containing-arrow' => [
                'testMarker' => '/* testNoArrowValueClosureWithAttribute */',
                'expected'   => false,
            ],
            'test-double-arrow-key-closure-with-attached-attribute-containing-arrow' => [
                'testMarker' => '/* testArrowKeyClosureWithAttribute */',
                'expected'   => 31,
            ],

            'test-empty-array-item' => [
                'testMarker' => '/* testEmptyArrayItem */',
                'expected'   => false,
            ],
        ];
    }

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @return void
     */
    public function testResultIsCached()
    {
        $methodName = 'PHPCSUtils\\Utils\\Arrays::getDoubleArrowPtr';
        $cases      = $this->dataGetDoubleArrowPtr();
        $testMarker = $cases['test-arrow-value-short-array']['testMarker'];
        $expected   = $cases['test-arrow-value-short-array']['expected'];

        if (isset(self::$parameters[$testMarker]) === false) {
            $this->fail('Test case not found for ' . $testMarker);
        }

        $start = self::$parameters[$testMarker]['start'];
        $end   = self::$parameters[$testMarker]['end'];

        // Change double arrow position from offset to exact position.
        if ($expected !== false) {
            $expected += $start;
        }

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = Arrays::getDoubleArrowPtr(self::$phpcsFile, $start, $end);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, "$start-$end");
        $resultSecondRun = Arrays::getDoubleArrowPtr(self::$phpcsFile, $start, $end);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }
}
