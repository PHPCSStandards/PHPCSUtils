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
 * Tests for the \PHPCSUtils\Utils\PassedParameters::getParameters() and
 * \PHPCSUtils\Utils\PassedParameters::getParameter() methods.
 *
 * @covers \PHPCSUtils\Utils\PassedParameters::getParameters
 * @covers \PHPCSUtils\Utils\PassedParameters::getParameter
 * @covers \PHPCSUtils\Utils\PassedParameters::getParameterFromStack
 * @covers \PHPCSUtils\Utils\PassedParameters::hasParameters
 *
 * @since 1.0.0
 */
final class GetParametersTest extends UtilityMethodTestCase
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
     * @param string                                       $testMarker The comment which prefaces the target token in
     *                                                                 the test file.
     * @param int|string                                   $targetType The type of token to look for.
     * @param array<int|string, array<string, int|string>> $expected   The expected parameter array.
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
     * @return array<string, array<string, int|string|array<int|string, array<string, int|string>>>>
     */
    public static function dataGetParameters()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'function-call' => [
                'testMarker' => '/* testFunctionCall */',
                'targetType' => \T_STRING,
                'expected'   => [
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
                'testMarker' => '/* testFunctionCallNestedFunctionCall */',
                'targetType' => \T_STRING,
                'expected'   => [
                    1 => [
                        'start' => 2,
                        'end'   => 9,
                        'raw'   => 'dirname( __FILE__ )',
                    ],
                ],
            ],
            'another-function-call' => [
                'testMarker' => '/* testAnotherFunctionCall */',
                'targetType' => \T_STRING,
                'expected'   => [
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
                'testMarker' => '/* testFunctionCallTrailingComma */',
                'targetType' => \T_STRING,
                'expected'   => [
                    1 => [
                        'start' => 2,
                        'end'   => 5,
                        'raw'   => 'array()',
                    ],
                ],
            ],
            'function-call-nested-short-array' => [
                'testMarker' => '/* testFunctionCallNestedShortArray */',
                'targetType' => \T_STRING,
                'expected'   => [
                    1 => [
                        'start' => 2,
                        'end'   => 34,
                        'raw'   => '[\'a\' => $a,] + (isset($b) ? [\'b\' => $b,] : [])',
                    ],
                ],
            ],
            'function-call-nested-array-nested-closure-with-commas' => [
                'testMarker' => '/* testFunctionCallNestedArrayNestedClosureWithCommas */',
                'targetType' => \T_STRING,
                'expected'   => [
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
                'testMarker' => '/* testLongArrayNestedFunctionCalls */',
                'targetType' => \T_ARRAY,
                'expected'   => [
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
                'testMarker' => '/* testShortArrayNestedFunctionCalls */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'expected'   => [
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
                'testMarker' => '/* testShortArrayWithKeysTernaryAndNullCoalesce */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'expected'   => [
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
                        'end'   => 51,
                        'raw'   => '\'hey\' => $baz ??
        [\'one\'] ??
        [\'two\']',
                    ],
                ],
            ],

            // Nested arrays.
            'nested-arrays-top-level' => [
                'testMarker' => '/* testNestedArraysToplevel */',
                'targetType' => \T_ARRAY,
                'expected'   => [
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
                'testMarker' => '/* testShortArrayNestedClosureWithCommas */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'expected'   => [
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
                'testMarker' => '/* testShortArrayNestedAnonClass */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'expected'   => [
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
                'testMarker' => '/* testLongArrayArrowFunctionWithYield */',
                'targetType' => \T_ARRAY,
                'expected'   => [
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
                'testMarker' => '/* testVariableFunctionCall */',
                'targetType' => \T_VARIABLE,
                'expected'   => [
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
                'testMarker' => '/* testStaticVariableFunctionCall */',
                'targetType' => \T_VARIABLE,
                'expected'   => [
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
                'testMarker' => '/* testIsset */',
                'targetType' => \T_ISSET,
                'expected'   => [
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
                'testMarker' => '/* testUnset */',
                'targetType' => \T_UNSET,
                'expected'   => [
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
            'die' => [
                'testMarker' => '/* testDie */',
                'targetType' => \T_EXIT,
                'expected'   => [
                    1 => [
                        'start' => 2,
                        'end'   => 4,
                        'raw'   => '$status',
                    ],
                ],
            ],

            'anon-class' => [
                'testMarker' => '/* testAnonClass */',
                'targetType' => \T_ANON_CLASS,
                'expected'   => [
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

            // PHP 7.4 argument unpacking array expressions.
            'long-array-with-argument-unpacking-via-spread-operator' => [
                'testMarker' => '/* testPHP74UnpackingInLongArrayExpression */',
                'targetType' => \T_ARRAY,
                'expected'   => [
                    1 => [
                        'start' => 2,
                        'end'   => 3,
                        'raw'   => '...$arr1',
                    ],
                    2 => [
                        'start' => 5,
                        'end'   => 9,
                        'raw'   => '...arrGen()',
                    ],
                    3 => [
                        'start' => 11,
                        'end'   => 26,
                        'raw'   => "...new ArrayIterator(['a', 'b', 'c'])",
                    ],
                ],
            ],
            'short-array-with-argument-unpacking-via-spread-operator' => [
                'testMarker' => '/* testPHP74UnpackingInShortArrayExpression */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'expected'   => [
                    1 => [
                        'start' => 1,
                        'end'   => 1,
                        'raw'   => "'banana'",
                    ],
                    2 => [
                        'start' => 3,
                        'end'   => 5,
                        'raw'   => '...$parts',
                    ],
                    3 => [
                        'start' => 7,
                        'end'   => 8,
                        'raw'   => "'watermelon'",
                    ],
                    4 => [
                        'start' => 10,
                        'end'   => 18,
                        'raw'   => '...["a" => 2]',
                    ],
                ],
            ],

            // PHP 8.0: class instantiation in attributes.
            'class-instantiation-within-an-attribute-1' => [
                'testMarker' => '/* testPHP80ClassInstantiationInAttribute1 */',
                'targetType' => \T_STRING,
                'expected'   => [
                    1 => [
                        'start' => 2,
                        'end'   => 10,
                        'raw'   => '[1, 2, 3]',
                    ],
                ],
            ],
            'class-instantiation-within-an-attribute-2' => [
                'testMarker' => '/* testPHP80ClassInstantiationInAttribute2 */',
                'targetType' => \T_STRING,
                'expected'   => [
                    1 => [
                        'start' => 2,
                        'end'   => 2,
                        'raw'   => '1',
                    ],
                    2 => [
                        'start' => 4,
                        'end'   => 7,
                        'raw'   => 'self::Foo',
                    ],
                    3 => [
                        'start' => 9,
                        'end'   => 10,
                        'raw'   => "'string'",
                    ],
                ],
            ],
            'class-instantiation-within-a-multi-attribute' => [
                'testMarker' => '/* testPHP80ClassInstantiationInMultiAttribute */',
                'targetType' => ($php8Names === true) ? \T_NAME_FULLY_QUALIFIED : \T_STRING,
                'expected'   => [
                    1 => [
                        'start' => 2,
                        'end'   => 2,
                        'raw'   => '1',
                    ],
                    2 => [
                        'start' => 4,
                        'end'   => 7,
                        'raw'   => 'self::Foo',
                    ],
                ],
            ],

            // PHP 8.0: skipping over attributes.
            'function-call-with-attributes-attached-to-passed-closure' => [
                'testMarker' => '/* testPHP80SkippingOverAttributes */',
                'targetType' => \T_STRING,
                'expected'   => [
                    1 => [
                        'start' => 2,
                        'end'   => 4,
                        'raw'   => '$value',
                    ],
                    2 => [
                        'start' => 6,
                        'end'   => 39,
                        'raw'   => '#[MyAttribute()]
    #[AnotherAttribute([1, 2, 3])]
    function() { /* do something */}',
                    ],
                ],
            ],

            'function-call-with-missing-param-midway' => [
                'testMarker' => '/* testMissingParam */',
                'targetType' => \T_STRING,
                'expected'   => [
                    1 => [
                        'start' => 2,
                        'end'   => 3,
                        'raw'   => '$value',
                    ],
                    2 => [
                        'start' => 5,
                        'end'   => 6,
                        'raw'   => '/* todo */',
                    ],
                    3 => [
                        'start' => 8,
                        'end'   => 9,
                        'raw'   => '$anotherValue',
                    ],
                ],
            ],
        ];
    }

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @return void
     */
    public function testGetParametersResultIsCached()
    {
        // The test case used is specifically selected as the raw and the clean param values will be the same.
        $methodName = 'PHPCSUtils\\Utils\\PassedParameters::getParameters';
        $cases      = $this->dataGetParameters();
        $testMarker = $cases['isset']['testMarker'];
        $targetType = $cases['isset']['targetType'];
        $expected   = $cases['isset']['expected'];

        $stackPtr = $this->getTargetToken($testMarker, [$targetType]);

        // Translate offsets to exact token positions and set the "clean" key.
        foreach ($expected as $key => $value) {
            $expected[$key]['start'] = ($stackPtr + $value['start']);
            $expected[$key]['end']   = ($stackPtr + $value['end']);
            $expected[$key]['clean'] = $value['raw'];
        }

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = PassedParameters::getParameters(self::$phpcsFile, $stackPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, "$stackPtr-0");
        $resultSecondRun = PassedParameters::getParameters(self::$phpcsFile, $stackPtr);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }

    /**
     * Test retrieving the details for a specific parameter from a function call or construct.
     *
     * @dataProvider dataGetParameter
     *
     * @param string                    $testMarker    The comment which prefaces the target token in the test file.
     * @param int|string                $targetType    The type of token to look for.
     * @param int                       $paramPosition The position of the parameter we want to retrieve the details for.
     * @param array<string, int|string> $expected      The expected array for the specific parameter.
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
     * @return array<string, array<string, int|string|array<string, int|string>>>
     */
    public static function dataGetParameter()
    {
        return [
            'function-call-param-4' => [
                'testMarker'    => '/* testFunctionCall */',
                'targetType'    => \T_STRING,
                'paramPosition' => 4,
                'expected'      => [
                    'start' => 11,
                    'end'   => 12,
                    'raw'   => '4',
                ],
            ],
            'function-call-nested-param-1' => [
                'testMarker'    => '/* testFunctionCallNestedFunctionCall */',
                'targetType'    => \T_STRING,
                'paramPosition' => 1,
                'expected'      => [
                    'start' => 2,
                    'end'   => 9,
                    'raw'   => 'dirname( __FILE__ )',
                ],
            ],
            'another-function-call-param-1' => [
                'testMarker'    => '/* testAnotherFunctionCall */',
                'targetType'    => \T_STRING,
                'paramPosition' => 1,
                'expected'      => [
                    'start' => 2,
                    'end'   => 2,
                    'raw'   => '$stHour',
                ],
            ],
            'another-function-call-param-6' => [
                'testMarker'    => '/* testAnotherFunctionCall */',
                'targetType'    => \T_STRING,
                'paramPosition' => 6,
                'expected'      => [
                    'start' => 22,
                    'end'   => 26,
                    'raw'   => '$arrStDt[2]',
                ],
            ],
            'long-array-nested-function-calls-param-3' => [
                'testMarker'    => '/* testLongArrayNestedFunctionCalls */',
                'targetType'    => \T_ARRAY,
                'paramPosition' => 3,
                'expected'      => [
                    'start' => 16,
                    'end'   => 26,
                    'raw'   => 'why(5, 1, 2)',
                ],
            ],
            'simple-long-array-param-1' => [
                'testMarker'    => '/* testSimpleLongArray */',
                'targetType'    => \T_ARRAY,
                'paramPosition' => 1,
                'expected'      => [
                    'start' => 2,
                    'end'   => 3,
                    'raw'   => '1',
                ],
            ],
            'simple-long-array-param-7' => [
                'testMarker'    => '/* testSimpleLongArray */',
                'targetType'    => \T_ARRAY,
                'paramPosition' => 7,
                'expected'      => [
                    'start' => 20,
                    'end'   => 22,
                    'raw'   => 'true',
                ],
            ],
            'long-array-with-keys-param-' => [
                'testMarker'    => '/* testLongArrayWithKeys */',
                'targetType'    => \T_ARRAY,
                'paramPosition' => 2,
                'expected'      => [
                    'start' => 8,
                    'end'   => 13,
                    'raw'   => '\'b\' => $b',
                ],
            ],
            'short-array-more-nested-function-calls-param-1' => [
                'testMarker'    => '/* testShortArrayMoreNestedFunctionCalls */',
                'targetType'    => \T_OPEN_SHORT_ARRAY,
                'paramPosition' => 1,
                'expected'      => [
                    'start' => 1,
                    'end'   => 13,
                    'raw'   => 'str_replace("../", "/", trim($value))',
                ],
            ],
            'short-array-with-keys-and-ternary-param-3' => [
                'testMarker'    => '/* testShortArrayWithKeysAndTernary */',
                'targetType'    => \T_OPEN_SHORT_ARRAY,
                'paramPosition' => 3,
                'expected'      => [
                    'start' => 14,
                    'end'   => 32,
                    'raw'   => '6 => (isset($c) ? $c : null)',
                ],
            ],
            'nested-arrays-level-2-param-1' => [
                'testMarker'    => '/* testNestedArraysLevel2 */',
                'targetType'    => \T_ARRAY,
                'paramPosition' => 1,
                'expected'      => [
                    'start' => 2,
                    'end'   => 2,
                    'raw'   => '1',
                ],
            ],
            'nested-arrays-level-1-param-2' => [
                'testMarker'    => '/* testNestedArraysLevel1 */',
                'targetType'    => \T_OPEN_SHORT_ARRAY,
                'paramPosition' => 2,
                'expected'      => [
                    'start' => 9,
                    'end'   => 21,
                    'raw'   => '1 => [1,2,3]',
                ],
            ],
        ];
    }
}
