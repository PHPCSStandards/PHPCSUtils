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

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\PassedParameters;

/**
 * Tests for the \PHPCSUtils\Utils\PassedParameters::getParameterCount() method.
 *
 * @covers \PHPCSUtils\Utils\PassedParameters::getParameterCount
 * @covers \PHPCSUtils\Utils\PassedParameters::getParameters
 * @covers \PHPCSUtils\Utils\PassedParameters::hasParameters
 *
 * @group passedparameters
 *
 * @since 1.0.0
 */
class GetParameterCountTest extends UtilityMethodTestCase
{

    /**
     * Test correctly counting the number of passed parameters.
     *
     * @dataProvider dataGetParameterCount
     *
     * @param string $testMarker    The comment which prefaces the target token in the test file.
     * @param int    $expected      The expected parameter count.
     * @param string $targetContent Optional. The content of the target token to find.
     *                              Defaults to null (ignore content).
     *
     * @return void
     */
    public function testGetParameterCount($testMarker, $expected, $targetContent = null)
    {
        $targetTypes                      = Collections::nameTokens();
        $targetTypes[\T_ARRAY]            = \T_ARRAY;
        $targetTypes[\T_OPEN_SHORT_ARRAY] = \T_OPEN_SHORT_ARRAY;
        $targetTypes[\T_ISSET]            = \T_ISSET;
        $targetTypes[\T_UNSET]            = \T_UNSET;

        $stackPtr = $this->getTargetToken($testMarker, $targetTypes, $targetContent);
        $result   = PassedParameters::getParameterCount(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetParameterCount() For the array format.
     *
     * @return array
     */
    public function dataGetParameterCount()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'function-call-0' => [
                '/* testFunctionCall0 */',
                0,
            ],
            'function-call-1' => [
                '/* testFunctionCall1 */',
                1,
            ],
            'function-call-2' => [
                '/* testFunctionCall2 */',
                2,
            ],
            'function-call-3' => [
                '/* testFunctionCall3 */',
                3,
            ],
            'function-call-4' => [
                '/* testFunctionCall4 */',
                4,
            ],
            'function-call-5' => [
                '/* testFunctionCall5 */',
                5,
            ],
            'function-call-6' => [
                '/* testFunctionCall6 */',
                6,
            ],
            'function-call-7' => [
                '/* testFunctionCall7 */',
                7,
            ],
            'function-call-8' => [
                '/* testFunctionCall8 */',
                1,
            ],
            'function-call-9' => [
                '/* testFunctionCall9 */',
                1,
            ],
            'function-call-10' => [
                '/* testFunctionCall10 */',
                1,
            ],
            'function-call-11' => [
                '/* testFunctionCall11 */',
                2,
            ],
            'function-call-12' => [
                '/* testFunctionCall12 */',
                1,
            ],
            'function-call-13' => [
                '/* testFunctionCall13 */',
                1,
            ],
            'function-call-14' => [
                '/* testFunctionCall14 */',
                1,
            ],
            'function-call-15' => [
                '/* testFunctionCall15 */',
                2,
            ],
            'function-call-16' => [
                '/* testFunctionCall16 */',
                6,
            ],
            'function-call-17' => [
                '/* testFunctionCall17 */',
                6,
            ],
            'function-call-18' => [
                '/* testFunctionCall18 */',
                6,
            ],
            'function-call-19' => [
                '/* testFunctionCall19 */',
                6,
            ],
            'function-call-20' => [
                '/* testFunctionCall20 */',
                6,
            ],
            'function-call-21' => [
                '/* testFunctionCall21 */',
                6,
            ],
            'function-call-22' => [
                '/* testFunctionCall22 */',
                6,
            ],
            'function-call-23' => [
                '/* testFunctionCall23 */',
                3,
            ],
            'function-call-24' => [
                '/* testFunctionCall24 */',
                1,
            ],
            'function-call-25' => [
                '/* testFunctionCall25 */',
                1,
            ],
            'function-call-26' => [
                '/* testFunctionCall26 */',
                1,
            ],
            'function-call-27' => [
                '/* testFunctionCall27 */',
                1,
            ],
            'function-call-28' => [
                '/* testFunctionCall28 */',
                1,
            ],
            'function-call-29' => [
                '/* testFunctionCall29 */',
                1,
            ],
            'function-call-30' => [
                '/* testFunctionCall30 */',
                1,
            ],
            'function-call-31' => [
                '/* testFunctionCall31 */',
                1,
            ],
            'function-call-32' => [
                '/* testFunctionCall32 */',
                1,
            ],
            'function-call-33' => [
                '/* testFunctionCall33 */',
                1,
            ],
            'function-call-34' => [
                '/* testFunctionCall34 */',
                1,
            ],
            'function-call-35' => [
                '/* testFunctionCall35 */',
                1,
            ],
            'function-call-36' => [
                '/* testFunctionCall36 */',
                1,
            ],
            'function-call-37' => [
                '/* testFunctionCall37 */',
                1,
            ],
            'function-call-38' => [
                '/* testFunctionCall38 */',
                1,
            ],
            'function-call-39' => [
                '/* testFunctionCall39 */',
                1,
            ],
            'function-call-40' => [
                '/* testFunctionCall40 */',
                1,
            ],
            'function-call-41' => [
                '/* testFunctionCall41 */',
                1,
            ],
            'function-call-42' => [
                '/* testFunctionCall42 */',
                1,
            ],
            'function-call-43' => [
                '/* testFunctionCall43 */',
                1,
            ],
            'function-call-44' => [
                '/* testFunctionCall44 */',
                1,
            ],
            'function-call-45' => [
                '/* testFunctionCall45 */',
                1,
            ],
            'function-call-46' => [
                '/* testFunctionCall46 */',
                1,
            ],
            'function-call-47' => [
                '/* testFunctionCall47 */',
                1,
            ],
            'function-call-fully-qualified' => [
                '/* testFunctionCallFullyQualified */',
                1,
                ($php8Names === true) ? null : 'myfunction',
            ],
            'function-call-fully-qualified-with-namespace' => [
                '/* testFunctionCallFullyQualifiedWithNamespace */',
                1,
                ($php8Names === true) ? null : 'myfunction',
            ],
            'function-call-partially-qualified' => [
                '/* testFunctionCallPartiallyQualified */',
                1,
                ($php8Names === true) ? null : 'myfunction',
            ],
            'function-call-namespace-operator' => [
                '/* testFunctionCallNamespaceOperator */',
                1,
                ($php8Names === true) ? null : 'myfunction',
            ],

            // Long arrays.
            'long-array-1' => [
                '/* testLongArray1 */',
                7,
            ],
            'long-array-2' => [
                '/* testLongArray2 */',
                1,
            ],
            'long-array-3' => [
                '/* testLongArray3 */',
                6,
            ],
            'long-array-4' => [
                '/* testLongArray4 */',
                6,
            ],
            'long-array-5' => [
                '/* testLongArray5 */',
                6,
            ],
            'long-array-6' => [
                '/* testLongArray6 */',
                3,
            ],
            'long-array-7' => [
                '/* testLongArray7 */',
                3,
            ],
            'long-array-8' => [
                '/* testLongArray8 */',
                3,
            ],

            // Short arrays.
            'short-array-1' => [
                '/* testShortArray1 */',
                7,
            ],
            'short-array-2' => [
                '/* testShortArray2 */',
                1,
            ],
            'short-array-3' => [
                '/* testShortArray3 */',
                6,
            ],
            'short-array-4' => [
                '/* testShortArray4 */',
                6,
            ],
            'short-array-5' => [
                '/* testShortArray5 */',
                6,
            ],
            'short-array-6' => [
                '/* testShortArray6 */',
                3,
            ],
            'short-array-7' => [
                '/* testShortArray7 */',
                3,
            ],
            'short-array-8' => [
                '/* testShortArray8 */',
                3,
            ],

            'array-with-empty-item' => [
                '/* testArrayWithEmptyItem */',
                3,
            ],
        ];
    }
}
