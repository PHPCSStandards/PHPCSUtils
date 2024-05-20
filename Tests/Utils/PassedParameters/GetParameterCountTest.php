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
 * @since 1.0.0
 */
final class GetParameterCountTest extends UtilityMethodTestCase
{

    /**
     * Test correctly counting the number of passed parameters.
     *
     * @dataProvider dataGetParameterCount
     *
     * @param string      $testMarker    The comment which prefaces the target token in the test file.
     * @param int         $expected      The expected parameter count.
     * @param string|null $targetContent Optional. The content of the target token to find.
     *                                   Defaults to null (ignore content).
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
     * @return array<string, array<string, string|int|null>>
     */
    public static function dataGetParameterCount()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'function-call-0' => [
                'testMarker' => '/* testFunctionCall0 */',
                'expected'   => 0,
            ],
            'function-call-1' => [
                'testMarker' => '/* testFunctionCall1 */',
                'expected'   => 1,
            ],
            'function-call-2' => [
                'testMarker' => '/* testFunctionCall2 */',
                'expected'   => 2,
            ],
            'function-call-3' => [
                'testMarker' => '/* testFunctionCall3 */',
                'expected'   => 3,
            ],
            'function-call-4' => [
                'testMarker' => '/* testFunctionCall4 */',
                'expected'   => 4,
            ],
            'function-call-5' => [
                'testMarker' => '/* testFunctionCall5 */',
                'expected'   => 5,
            ],
            'function-call-6' => [
                'testMarker' => '/* testFunctionCall6 */',
                'expected'   => 6,
            ],
            'function-call-7' => [
                'testMarker' => '/* testFunctionCall7 */',
                'expected'   => 7,
            ],
            'function-call-8' => [
                'testMarker' => '/* testFunctionCall8 */',
                'expected'   => 1,
            ],
            'function-call-9' => [
                'testMarker' => '/* testFunctionCall9 */',
                'expected'   => 1,
            ],
            'function-call-10' => [
                'testMarker' => '/* testFunctionCall10 */',
                'expected'   => 1,
            ],
            'function-call-11' => [
                'testMarker' => '/* testFunctionCall11 */',
                'expected'   => 2,
            ],
            'function-call-12' => [
                'testMarker' => '/* testFunctionCall12 */',
                'expected'   => 1,
            ],
            'function-call-13' => [
                'testMarker' => '/* testFunctionCall13 */',
                'expected'   => 1,
            ],
            'function-call-14' => [
                'testMarker' => '/* testFunctionCall14 */',
                'expected'   => 1,
            ],
            'function-call-15' => [
                'testMarker' => '/* testFunctionCall15 */',
                'expected'   => 2,
            ],
            'function-call-16' => [
                'testMarker' => '/* testFunctionCall16 */',
                'expected'   => 6,
            ],
            'function-call-17' => [
                'testMarker' => '/* testFunctionCall17 */',
                'expected'   => 6,
            ],
            'function-call-18' => [
                'testMarker' => '/* testFunctionCall18 */',
                'expected'   => 6,
            ],
            'function-call-19' => [
                'testMarker' => '/* testFunctionCall19 */',
                'expected'   => 6,
            ],
            'function-call-20' => [
                'testMarker' => '/* testFunctionCall20 */',
                'expected'   => 6,
            ],
            'function-call-21' => [
                'testMarker' => '/* testFunctionCall21 */',
                'expected'   => 6,
            ],
            'function-call-22' => [
                'testMarker' => '/* testFunctionCall22 */',
                'expected'   => 6,
            ],
            'function-call-23' => [
                'testMarker' => '/* testFunctionCall23 */',
                'expected'   => 3,
            ],
            'function-call-24' => [
                'testMarker' => '/* testFunctionCall24 */',
                'expected'   => 1,
            ],
            'function-call-25' => [
                'testMarker' => '/* testFunctionCall25 */',
                'expected'   => 1,
            ],
            'function-call-26' => [
                'testMarker' => '/* testFunctionCall26 */',
                'expected'   => 1,
            ],
            'function-call-27' => [
                'testMarker' => '/* testFunctionCall27 */',
                'expected'   => 1,
            ],
            'function-call-28' => [
                'testMarker' => '/* testFunctionCall28 */',
                'expected'   => 1,
            ],
            'function-call-29' => [
                'testMarker' => '/* testFunctionCall29 */',
                'expected'   => 1,
            ],
            'function-call-30' => [
                'testMarker' => '/* testFunctionCall30 */',
                'expected'   => 1,
            ],
            'function-call-31' => [
                'testMarker' => '/* testFunctionCall31 */',
                'expected'   => 1,
            ],
            'function-call-32' => [
                'testMarker' => '/* testFunctionCall32 */',
                'expected'   => 1,
            ],
            'function-call-33' => [
                'testMarker' => '/* testFunctionCall33 */',
                'expected'   => 1,
            ],
            'function-call-34' => [
                'testMarker' => '/* testFunctionCall34 */',
                'expected'   => 1,
            ],
            'function-call-35' => [
                'testMarker' => '/* testFunctionCall35 */',
                'expected'   => 1,
            ],
            'function-call-36' => [
                'testMarker' => '/* testFunctionCall36 */',
                'expected'   => 1,
            ],
            'function-call-37' => [
                'testMarker' => '/* testFunctionCall37 */',
                'expected'   => 1,
            ],
            'function-call-38' => [
                'testMarker' => '/* testFunctionCall38 */',
                'expected'   => 1,
            ],
            'function-call-39' => [
                'testMarker' => '/* testFunctionCall39 */',
                'expected'   => 1,
            ],
            'function-call-40' => [
                'testMarker' => '/* testFunctionCall40 */',
                'expected'   => 1,
            ],
            'function-call-41' => [
                'testMarker' => '/* testFunctionCall41 */',
                'expected'   => 1,
            ],
            'function-call-42' => [
                'testMarker' => '/* testFunctionCall42 */',
                'expected'   => 1,
            ],
            'function-call-43' => [
                'testMarker' => '/* testFunctionCall43 */',
                'expected'   => 1,
            ],
            'function-call-44' => [
                'testMarker' => '/* testFunctionCall44 */',
                'expected'   => 1,
            ],
            'function-call-45' => [
                'testMarker' => '/* testFunctionCall45 */',
                'expected'   => 1,
            ],
            'function-call-46' => [
                'testMarker' => '/* testFunctionCall46 */',
                'expected'   => 1,
            ],
            'function-call-47' => [
                'testMarker' => '/* testFunctionCall47 */',
                'expected'   => 1,
            ],
            'function-call-fully-qualified' => [
                'testMarker'    => '/* testFunctionCallFullyQualified */',
                'expected'      => 1,
                'targetContent' => ($php8Names === true) ? null : 'myfunction',
            ],
            'function-call-fully-qualified-with-namespace' => [
                'testMarker'    => '/* testFunctionCallFullyQualifiedWithNamespace */',
                'expected'      => 1,
                'targetContent' => ($php8Names === true) ? null : 'myfunction',
            ],
            'function-call-partially-qualified' => [
                'testMarker'    => '/* testFunctionCallPartiallyQualified */',
                'expected'      => 1,
                'targetContent' => ($php8Names === true) ? null : 'myfunction',
            ],
            'function-call-namespace-operator' => [
                'testMarker'    => '/* testFunctionCallNamespaceOperator */',
                'expected'      => 1,
                'targetContent' => ($php8Names === true) ? null : 'myfunction',
            ],
            'function-call-named-params-duplicate-name' => [
                'testMarker'    => '/* testFunctionCallNamedParamsDuplicateName */',
                'expected'      => 2,
            ],

            // Long arrays.
            'long-array-1' => [
                'testMarker' => '/* testLongArray1 */',
                'expected'   => 7,
            ],
            'long-array-2' => [
                'testMarker' => '/* testLongArray2 */',
                'expected'   => 1,
            ],
            'long-array-3' => [
                'testMarker' => '/* testLongArray3 */',
                'expected'   => 6,
            ],
            'long-array-4' => [
                'testMarker' => '/* testLongArray4 */',
                'expected'   => 6,
            ],
            'long-array-5' => [
                'testMarker' => '/* testLongArray5 */',
                'expected'   => 6,
            ],
            'long-array-6' => [
                'testMarker' => '/* testLongArray6 */',
                'expected'   => 3,
            ],
            'long-array-7' => [
                'testMarker' => '/* testLongArray7 */',
                'expected'   => 3,
            ],
            'long-array-8' => [
                'testMarker' => '/* testLongArray8 */',
                'expected'   => 3,
            ],

            // Short arrays.
            'short-array-1' => [
                'testMarker' => '/* testShortArray1 */',
                'expected'   => 7,
            ],
            'short-array-2' => [
                'testMarker' => '/* testShortArray2 */',
                'expected'   => 1,
            ],
            'short-array-3' => [
                'testMarker' => '/* testShortArray3 */',
                'expected'   => 6,
            ],
            'short-array-4' => [
                'testMarker' => '/* testShortArray4 */',
                'expected'   => 6,
            ],
            'short-array-5' => [
                'testMarker' => '/* testShortArray5 */',
                'expected'   => 6,
            ],
            'short-array-6' => [
                'testMarker' => '/* testShortArray6 */',
                'expected'   => 3,
            ],
            'short-array-7' => [
                'testMarker' => '/* testShortArray7 */',
                'expected'   => 3,
            ],
            'short-array-8' => [
                'testMarker' => '/* testShortArray8 */',
                'expected'   => 3,
            ],

            'anon-class' => [
                'testMarker' => '/* testAnonClass */',
                'expected'   => 2,
            ],

            'class-instantiation-in-attribute' => [
                'testMarker' => '/* testPHP80ClassInstantiationInAttribute */',
                'expected'   => 3,
            ],
            'class-instantiation-in-attribute-no-params' => [
                'testMarker'    => '/* testPHP80ClassInstantiationInMultiAttribute */',
                'expected'      => 0,
                'targetContent' => 'AttributeOne',
            ],
            'class-instantiation-in-attribute-with-params' => [
                'testMarker'    => '/* testPHP80ClassInstantiationInMultiAttribute */',
                'expected'      => 2,
                'targetContent' => ($php8Names === true) ? '\AttributeTwo' : 'AttributeTwo',
            ],

            'array-with-empty-item' => [
                'testMarker' => '/* testArrayWithEmptyItem */',
                'expected'   => 3,
            ],
        ];
    }
}
