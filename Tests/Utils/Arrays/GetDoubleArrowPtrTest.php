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

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Arrays;
use PHPCSUtils\Utils\PassedParameters;

/**
 * Tests for the \PHPCSUtils\Utils\Arrays::getDoubleArrowPtr() method.
 *
 * @covers \PHPCSUtils\Utils\Arrays::getDoubleArrowPtr
 *
 * @group arrays
 *
 * @since 1.0.0
 */
class GetDoubleArrowPtrTest extends UtilityMethodTestCase
{

    /**
     * Cache for the parsed parameters array.
     *
     * @var array <string> => <int>
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

            foreach ($parameters as $index => $values) {
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
    public function testInvalidStartPositionException()
    {
        $this->expectPhpcsException(
            'Invalid start and/or end position passed to getDoubleArrowPtr(). Received: $start -10, $end 10'
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
        $this->expectPhpcsException(
            'Invalid start and/or end position passed to getDoubleArrowPtr(). Received: $start 0, $end 100000'
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
        $this->expectPhpcsException(
            'Invalid start and/or end position passed to getDoubleArrowPtr(). Received: $start 10, $end 5'
        );

        Arrays::getDoubleArrowPtr(self::$phpcsFile, 10, 5);
    }

    /**
     * Test retrieving the position of the double arrow for an array parameter.
     *
     * @dataProvider dataGetDoubleArrowPtr
     *
     * @param string $testMarker The comment which is part of the target array item in the test file.
     * @param array  $expected   The expected function call result.
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
     * @return array
     */
    public function dataGetDoubleArrowPtr()
    {
        return [
            'test-no-arrow' => [
                '/* testValueNoArrow */',
                false,
            ],
            'test-arrow-numeric-index' => [
                '/* testArrowNumericIndex */',
                8,
            ],
            'test-arrow-string-index' => [
                '/* testArrowStringIndex */',
                8,
            ],
            'test-arrow-multi-token-index' => [
                '/* testArrowMultiTokenIndex */',
                12,
            ],
            'test-no-arrow-value-short-array' => [
                '/* testNoArrowValueShortArray */',
                false,
            ],
            'test-no-arrow-value-long-array' => [
                '/* testNoArrowValueLongArray */',
                false,
            ],
            'test-no-arrow-value-nested-arrays' => [
                '/* testNoArrowValueNestedArrays */',
                false,
            ],
            'test-no-arrow-value-closure' => [
                '/* testNoArrowValueClosure */',
                false,
            ],
            'test-arrow-value-short-array' => [
                '/* testArrowValueShortArray */',
                8,
            ],
            'test-arrow-value-long-array' => [
                '/* testArrowValueLongArray */',
                8,
            ],
            'test-arrow-value-closure' => [
                '/* testArrowValueClosure */',
                8,
            ],
            'test-no-arrow-value-anon-class-with-foreach' => [
                '/* testNoArrowValueAnonClassForeach */',
                false,
            ],
            'test-no-arrow-value-closure-with-keyed-yield' => [
                '/* testNoArrowValueClosureYieldWithKey */',
                false,
            ],
            'test-arrow-key-closure-with-keyed-yield' => [
                '/* testArrowKeyClosureYieldWithKey */',
                24,
            ],
            'test-arrow-value-fn-function' => [
                '/* testFnFunctionWithKey */',
                8,
            ],
            'test-no-arrow-value-fn-function' => [
                '/* testNoArrowValueFnFunction */',
                false,
            ],
            'test-arrow-tstring-key-not-fn-function' => [
                '/* testTstringKeyNotFnFunction */',
                8,
            ],
            // Test specifically for PHPCS 3.5.3 and 3.5.4 in which all "fn" tokens were tokenized as T_FN.
            'test-arrow-access-to-property-named-fn-as-key-phpcs-3.5.3-3.5.4' => [
                '/* testKeyPropertyAccessFnPHPCS353-354 */',
                12,
            ],
            'test-double-arrow-incorrectly-tokenized-phpcs-issue-2865' => [
                '/* testDoubleArrowTokenizedAsTstring-PHPCS2865 */',
                10,
            ],
        ];
    }
}
