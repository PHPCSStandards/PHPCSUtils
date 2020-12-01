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

use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\PassedParameters;

/**
 * Tests for the \PHPCSUtils\Utils\PassedParameters::getParameters() and
 * \PHPCSUtils\Utils\PassedParameters::getParameter() methods.
 *
 * @covers \PHPCSUtils\Utils\PassedParameters::getParameters
 * @covers \PHPCSUtils\Utils\PassedParameters::getParameter
 * @covers \PHPCSUtils\Utils\PassedParameters::getParameterFromStack
 * @covers \PHPCSUtils\Utils\PassedParameters::hasParameters
 *
 * @group passedparameters
 *
 * @since 1.0.0
 */
class GetParametersTest extends UtilityMethodTestCase
{

    /**
     * Test retrieving the parameter details from a function call without parameters.
     *
     * @return void
     */
    public function testGetParametersNoParams()
    {
        $stackPtr = $this->getTargetToken('/* testNoParams */', \T_STRING);

        $result = PassedParameters::getParameters(self::$phpcsFile, $stackPtr);
        $this->assertSame([], $result);

        $result = PassedParameters::getParameter(self::$phpcsFile, $stackPtr, 2);
        $this->assertFalse($result);
    }

    /**
     * Test retrieving the parameter details from a function call or construct.
     *
     * @dataProvider dataGetParameters
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType The type of token to look for.
     * @param array      $expected   The expected parameter array.
     *
     * @return void
     */
    public function testGetParameters($testMarker, $targetType, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [$targetType]);

        // Start/end token position values in the expected array are set as offsets
        // in relation to the target token.
        // Change these to exact positions based on the retrieved stackPtr.
        foreach ($expected as $key => $value) {
            $expected[$key]['start'] = ($stackPtr + $value['start']);
            $expected[$key]['end']   = ($stackPtr + $value['end']);
        }

        $result = PassedParameters::getParameters(self::$phpcsFile, $stackPtr);

        foreach ($result as $key => $value) {
            $this->assertArrayHasKey('clean', $value);

            // The GetTokensAsString functions have their own tests, no need to duplicate it here.
            unset($result[$key]['clean']);
        }

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetParameters() For the array format.
     *
     * @return array
     */
    public function dataGetParameters()
    {
        return [
            'function-call' => [
                '/* testFunctionCall */',
                \T_STRING,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 3,
                        'raw'   => '1',
                    ],
                    2 => [
                        'start' => 5,
                        'end'   => 6,
                        'raw'   => '2',
                    ],
                    3 => [
                        'start' => 8,
                        'end'   => 9,
                        'raw'   => '3',
                    ],
                    4 => [
                        'start' => 11,
                        'end'   => 12,
                        'raw'   => '4',
                    ],
                    5 => [
                        'start' => 14,
                        'end'   => 15,
                        'raw'   => '5',
                    ],
                    6 => [
                        'start' => 17,
                        'end'   => 18,
                        'raw'   => '6',
                    ],
                    7 => [
                        'start' => 20,
                        'end'   => 22,
                        'raw'   => 'true',
                    ],
                ],
            ],
            'function-call-nested' => [
                '/* testFunctionCallNestedFunctionCall */',
                \T_STRING,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 9,
                        'raw'   => 'dirname( __FILE__ )',
                    ],
                ],
            ],
            'another-function-call' => [
                '/* testAnotherFunctionCall */',
                \T_STRING,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 2,
                        'raw'   => '$stHour',
                    ],
                    2 => [
                        'start' => 4,
                        'end'   => 5,
                        'raw'   => '0',
                    ],
                    3 => [
                        'start' => 7,
                        'end'   => 8,
                        'raw'   => '0',
                    ],
                    4 => [
                        'start' => 10,
                        'end'   => 14,
                        'raw'   => '$arrStDt[0]',
                    ],
                    5 => [
                        'start' => 16,
                        'end'   => 20,
                        'raw'   => '$arrStDt[1]',
                    ],
                    6 => [
                        'start' => 22,
                        'end'   => 26,
                        'raw'   => '$arrStDt[2]',
                    ],
                ],

            ],
            'function-call-trailing-comma' => [
                '/* testFunctionCallTrailingComma */',
                \T_STRING,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 5,
                        'raw'   => 'array()',
                    ],
                ],
            ],
            'function-call-nested-short-array' => [
                '/* testFunctionCallNestedShortArray */',
                \T_STRING,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 34,
                        'raw'   => '[\'a\' => $a,] + (isset($b) ? [\'b\' => $b,] : [])',
                    ],
                ],
            ],
            'function-call-nested-array-nested-closure-with-commas' => [
                '/* testFunctionCallNestedArrayNestedClosureWithCommas */',
                \T_STRING,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 90,
                        'raw'   => '/* testShortArrayNestedClosureWithCommas */
    [
        \'~\'.$dyn.\'~J\' => function ($match) {
            echo strlen($match[0]), \' matches for "a" found\', PHP_EOL;
        },
        \'~\'.function_call().\'~i\' => function ($match) {
            echo strlen($match[0]), \' matches for "b" found\', PHP_EOL;
        },
    ]',
                    ],
                    2 => [
                        'start' => 92,
                        'end'   => 95,
                        'raw'   => '$subject',
                    ],
                ],
            ],

            // Long array.
            'long-array' => [
                '/* testLongArrayNestedFunctionCalls */',
                \T_ARRAY,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 8,
                        'raw'   => 'some_call(5, 1)',
                    ],
                    2 => [
                        'start' => 10,
                        'end'   => 14,
                        'raw'   => 'another(1)',
                    ],
                    3 => [
                        'start' => 16,
                        'end'   => 26,
                        'raw'   => 'why(5, 1, 2)',
                    ],
                    4 => [
                        'start' => 28,
                        'end'   => 29,
                        'raw'   => '4',
                    ],
                    5 => [
                        'start' => 31,
                        'end'   => 32,
                        'raw'   => '5',
                    ],
                    6 => [
                        'start' => 34,
                        'end'   => 35,
                        'raw'   => '6',
                    ],
                ],
            ],

            // Short array.
            'short-array' => [
                '/* testShortArrayNestedFunctionCalls */',
                \T_OPEN_SHORT_ARRAY,
                [
                    1 => [
                        'start' => 1,
                        'end'   => 1,
                        'raw'   => '0',
                    ],
                    2 => [
                        'start' => 3,
                        'end'   => 4,
                        'raw'   => '0',
                    ],
                    3 => [
                        'start' => 6,
                        'end'   => 13,
                        'raw'   => 'date(\'s\', $timestamp)',
                    ],
                    4 => [
                        'start' => 15,
                        'end'   => 19,
                        'raw'   => 'date(\'m\')',
                    ],
                    5 => [
                        'start' => 21,
                        'end'   => 25,
                        'raw'   => 'date(\'d\')',
                    ],
                    6 => [
                        'start' => 27,
                        'end'   => 31,
                        'raw'   => 'date(\'Y\')',
                    ],
                ],
            ],
            'short-array-with-keys-ternary-and-null-coalesce' => [
                '/* testShortArrayWithKeysTernaryAndNullCoalesce */',
                \T_OPEN_SHORT_ARRAY,
                [
                    1 => [
                        'start' => 1,
                        'end'   => 7,
                        'raw'   => "'foo' => 'foo'",
                    ],
                    2 => [
                        'start' => 9,
                        'end'   => 29,
                        'raw'   => '\'bar\' => $baz ?
        [\'abc\'] :
        [\'def\']',
                    ],
                    3 => [
                        'start' => 31,
                        // Account for null coalesce tokenization difference.
                        'end'   => (Helper::getVersion() === '2.6.0' && \PHP_VERSION_ID < 59999)
                            ? 53
                            : 51,
                        'raw'   => '\'hey\' => $baz ??
        [\'one\'] ??
        [\'two\']',
                    ],
                ],
            ],

            // Nested arrays.
            'nested-arrays-top-level' => [
                '/* testNestedArraysToplevel */',
                \T_ARRAY,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 38,
                        'raw'   => '\'1\' => array(
        0 => \'more nesting\',
        /* testNestedArraysLevel2 */
        1 => array(1,2,3),
    )',
                    ],
                    2 => [
                        'start' => 40,
                        'end'   => 74,
                        'raw'   => '/* testNestedArraysLevel1 */
    \'2\' => [
        0 => \'more nesting\',
        1 => [1,2,3],
    ]',
                    ],
                ],
            ],

            // Array containing closure.
            'short-array-nested-closure-with-commas' => [
                '/* testShortArrayNestedClosureWithCommas */',
                \T_OPEN_SHORT_ARRAY,
                [
                    1 => [
                        'start' => 1,
                        'end'   => 38,
                        'raw'   => '\'~\'.$dyn.\'~J\' => function ($match) {
            echo strlen($match[0]), \' matches for "a" found\', PHP_EOL;
        }',
                    ],
                    2 => [
                        'start' => 40,
                        'end'   => 79,
                        'raw'   => '\'~\'.function_call().\'~i\' => function ($match) {
            echo strlen($match[0]), \' matches for "b" found\', PHP_EOL;
        }',
                    ],
                ],
            ],

            // Array containing anonymous class.
            'short-array-nested-anon-class' => [
                '/* testShortArrayNestedAnonClass */',
                \T_OPEN_SHORT_ARRAY,
                [
                    1 => [
                        'start' => 1,
                        'end'   => 72,
                        'raw'   => '/**
     * Docblock to skip over.
     */
    \'class\' => new class() {
        public $prop = [1,2,3];
        public function test( $foo, $bar ) {
            echo $foo, $bar;
        }
    }',
                    ],
                    2 => [
                        'start' => 74,
                        'end'   => 129,
                        'raw'   => '/**
     * Docblock to skip over.
     */
    \'anotherclass\' => new class() {
        public function test( $foo, $bar ) {
            echo $foo, $bar;
        }
    }',
                    ],
                ],
            ],

            // Array arrow function and yield.
            'long-array-nested-arrow-function-with-yield' => [
                '/* testLongArrayArrowFunctionWithYield */',
                \T_ARRAY,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 8,
                        'raw'   => '1 => \'1\'',
                    ],
                    2 => [
                        'start' => 10,
                        'end'   => 30,
                        'raw'   => '2 => fn ($x) => yield \'a\' => $x',
                    ],
                    3 => [
                        'start' => 32,
                        'end'   => 38,
                        'raw'   => '3 => \'3\'',
                    ],
                ],
            ],

            // Function calling closure in variable.
            'variable-function-call' => [
                '/* testVariableFunctionCall */',
                \T_VARIABLE,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 2,
                        'raw'   => '$a',
                    ],
                    2 => [
                        'start' => 4,
                        'end'   => 11,
                        'raw'   => '(1 + 20)',
                    ],
                    3 => [
                        'start' => 13,
                        'end'   => 19,
                        'raw'   => '$a & $b',
                    ],
                ],
            ],
            'static-variable-function-call' => [
                '/* testStaticVariableFunctionCall */',
                \T_VARIABLE,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 4,
                        'raw'   => '$a->property',
                    ],
                    2 => [
                        'start' => 6,
                        'end'   => 12,
                        'raw'   => '$b->call()',
                    ],
                ],
            ],
            'isset' => [
                '/* testIsset */',
                \T_ISSET,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 4,
                        'raw'   => '$variable',
                    ],
                    2 => [
                        'start' => 6,
                        'end'   => 10,
                        'raw'   => '$object->property',
                    ],
                    3 => [
                        'start' => 12,
                        'end'   => 16,
                        'raw'   => 'static::$property',
                    ],
                    4 => [
                        'start' => 18,
                        'end'   => 26,
                        'raw'   => '$array[$name][$sub]',
                    ],
                ],
            ],
            'unset' => [
                '/* testUnset */',
                \T_UNSET,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 3,
                        'raw'   => '$variable',
                    ],
                    2 => [
                        'start' => 5,
                        'end'   => 8,
                        'raw'   => '$object->property',
                    ],
                    3 => [
                        'start' => 10,
                        'end'   => 13,
                        'raw'   => 'static::$property',
                    ],
                    4 => [
                        'start' => 15,
                        'end'   => 19,
                        'raw'   => '$array[$name]',
                    ],
                ],
            ],
            'anon-class' => [
                '/* testAnonClass */',
                \T_ANON_CLASS,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 3,
                        'raw'   => '$param1',
                    ],
                    2 => [
                        'start' => 5,
                        'end'   => 7,
                        'raw'   => '$param2',
                    ],
                ],
            ],
        ];
    }

    /**
     * Test retrieving the details for a specific parameter from a function call or construct.
     *
     * @dataProvider dataGetParameter
     *
     * @param string     $testMarker    The comment which prefaces the target token in the test file.
     * @param int|string $targetType    The type of token to look for.
     * @param int        $paramPosition The position of the parameter we want to retrieve the details for.
     * @param array      $expected      The expected array for the specific parameter.
     *
     * @return void
     */
    public function testGetParameter($testMarker, $targetType, $paramPosition, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [$targetType]);

        // Start/end token position values in the expected array are set as offsets
        // in relation to the target token.
        // Change these to exact positions based on the retrieved stackPtr.
        $expected['start'] += $stackPtr;
        $expected['end']   += $stackPtr;

        $result = PassedParameters::getParameter(self::$phpcsFile, $stackPtr, $paramPosition);

        $this->assertArrayHasKey('clean', $result);

        // The GetTokensAsString functions have their own tests, no need to duplicate it here.
        unset($result['clean']);

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetParameter() For the array format.
     *
     * @return array
     */
    public function dataGetParameter()
    {
        return [
            'function-call-param-4' => [
                '/* testFunctionCall */',
                \T_STRING,
                4,
                [
                    'start' => 11,
                    'end'   => 12,
                    'raw'   => '4',
                ],
            ],
            'function-call-nested-param-1' => [
                '/* testFunctionCallNestedFunctionCall */',
                \T_STRING,
                1,
                [
                    'start' => 2,
                    'end'   => 9,
                    'raw'   => 'dirname( __FILE__ )',
                ],
            ],
            'another-function-call-param-1' => [
                '/* testAnotherFunctionCall */',
                \T_STRING,
                1,
                [
                    'start' => 2,
                    'end'   => 2,
                    'raw'   => '$stHour',
                ],
            ],
            'another-function-call-param-6' => [
                '/* testAnotherFunctionCall */',
                \T_STRING,
                6,
                [
                    'start' => 22,
                    'end'   => 26,
                    'raw'   => '$arrStDt[2]',
                ],
            ],
            'long-array-nested-function-calls-param-3' => [
                '/* testLongArrayNestedFunctionCalls */',
                \T_ARRAY,
                3,
                [
                    'start' => 16,
                    'end'   => 26,
                    'raw'   => 'why(5, 1, 2)',
                ],
            ],
            'simple-long-array-param-1' => [
                '/* testSimpleLongArray */',
                \T_ARRAY,
                1,
                [
                    'start' => 2,
                    'end'   => 3,
                    'raw'   => '1',
                ],
            ],
            'simple-long-array-param-7' => [
                '/* testSimpleLongArray */',
                \T_ARRAY,
                7,
                [
                    'start' => 20,
                    'end'   => 22,
                    'raw'   => 'true',
                ],
            ],
            'long-array-with-keys-param-' => [
                '/* testLongArrayWithKeys */',
                \T_ARRAY,
                2,
                [
                    'start' => 8,
                    'end'   => 13,
                    'raw'   => '\'b\' => $b',
                ],
            ],
            'short-array-more-nested-function-calls-param-1' => [
                '/* testShortArrayMoreNestedFunctionCalls */',
                \T_OPEN_SHORT_ARRAY,
                1,
                [
                    'start' => 1,
                    'end'   => 13,
                    'raw'   => 'str_replace("../", "/", trim($value))',
                ],
            ],
            'short-array-with-keys-and-ternary-param-3' => [
                '/* testShortArrayWithKeysAndTernary */',
                \T_OPEN_SHORT_ARRAY,
                3,
                [
                    'start' => 14,
                    'end'   => 32,
                    'raw'   => '6 => (isset($c) ? $c : null)',
                ],
            ],
            'nested-arrays-level-2-param-1' => [
                '/* testNestedArraysLevel2 */',
                \T_ARRAY,
                1,
                [
                    'start' => 2,
                    'end'   => 2,
                    'raw'   => '1',
                ],
            ],
            'nested-arrays-level-1-param-2' => [
                '/* testNestedArraysLevel1 */',
                \T_OPEN_SHORT_ARRAY,
                2,
                [
                    'start' => 9,
                    'end'   => 21,
                    'raw'   => '1 => [1,2,3]',
                ],
            ],
        ];
    }
}
