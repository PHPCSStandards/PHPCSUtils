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
use PHPCSUtils\Utils\PassedParameters;

/**
 * Tests the support for the PHP 8.0 named parameters feature in
 * \PHPCSUtils\Utils\PassedParameters::getParameters() and
 * \PHPCSUtils\Utils\PassedParameters::getParameter() methods.
 *
 * @covers \PHPCSUtils\Utils\PassedParameters::getParameters
 * @covers \PHPCSUtils\Utils\PassedParameters::getParameter
 * @covers \PHPCSUtils\Utils\PassedParameters::getParameterFromStack
 * @covers \PHPCSUtils\Utils\PassedParameters::hasParameters
 *
 * @since 1.0.0
 */
final class GetParametersNamedTest extends UtilityMethodTestCase
{

    /**
     * Test retrieving the parameter details from a function call or construct.
     *
     * @dataProvider dataGetParameters
     *
     * @param string                                       $testMarker    The comment which prefaces the target token
     *                                                                    in the test file.
     * @param int|string                                   $targetType    The type of token to look for.
     * @param array<int|string, array<string, int|string>> $expected      The expected parameter array.
     * @param string|null                                  $targetContent Optional. The token content to look for.
     *
     * @return void
     */
    public function testGetParameters($testMarker, $targetType, $expected, $targetContent = null)
    {
        $stackPtr = $this->getTargetToken($testMarker, [$targetType], $targetContent);

        // Start/end token position values in the expected array are set as offsets
        // in relation to the target token.
        // Change these to exact positions based on the retrieved stackPtr.
        foreach ($expected as $key => $value) {
            $expected[$key]['start'] += $stackPtr;
            $expected[$key]['end']   += $stackPtr;

            if (isset($value['name_token'])) {
                $expected[$key]['name_token'] += $stackPtr;
            }
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
     * @return array<string, array<string, int|string|array<int|string, array<string, int|string>>|null>>
     */
    public static function dataGetParameters()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'only-positional-args' => [
                'testMarker' => '/* testPositionalArgs */',
                'targetType' => \T_STRING,
                'expected'   => [
                    1 => [
                        'start' => 2,
                        'end'   => 2,
                        'raw'   => 'START_INDEX',
                    ],
                    2 => [
                        'start' => 4,
                        'end'   => ($php8Names === true) ? 5 : 6,
                        'raw'   => '\COUNT',
                    ],
                    3 => [
                        'start' => ($php8Names === true) ? 7 : 8,
                        'end'   => ($php8Names === true) ? 8 : 11,
                        'raw'   => 'MyNS\VALUE',
                    ],
                ],
            ],
            'named-args' => [
                'testMarker' => '/* testNamedArgs */',
                'targetType' => \T_STRING,
                'expected'   => [
                    'start_index' => [
                        'name'       => 'start_index',
                        'name_token' => 2,
                        'start'      => 4,
                        'end'        => 5,
                        'raw'        => '0',
                    ],
                    'count' => [
                        'name'       => 'count',
                        'name_token' => 8,
                        'start'      => 10,
                        'end'        => 11,
                        'raw'        => '100',
                    ],
                    'value' => [
                        'name'       => 'value',
                        'name_token' => 14,
                        'start'      => 16,
                        'end'        => 17,
                        'raw'        => '50',
                    ],
                ],
            ],
            'named-args-multiline' => [
                'testMarker' => '/* testNamedArgsMultiline */',
                'targetType' => \T_STRING,
                'expected'   => [
                    'start_index' => [
                        'name'       => 'start_index',
                        'name_token' => 4,
                        'start'      => 7,
                        'end'        => 8,
                        'raw'        => '0',
                    ],
                    'count' => [
                        'name'       => 'count',
                        'name_token' => 12,
                        'start'      => 15,
                        'end'        => 16,
                        'raw'        => '100',
                    ],
                    'value' => [
                        'name'       => 'value',
                        'name_token' => 20,
                        'start'      => 23,
                        'end'        => 24,
                        'raw'        => '50',
                    ],
                ],
            ],
            'named-args-whitespace-comments' => [
                'testMarker' => '/* testNamedArgsWithWhitespaceAndComments */',
                'targetType' => \T_STRING,
                'expected'   => [
                    'start_index' => [
                        'name'       => 'start_index',
                        'name_token' => 4,
                        'start'      => 7,
                        'end'        => 8,
                        'raw'        => '0',
                    ],
                    'count' => [
                        'name'       => 'count',
                        'name_token' => 13,
                        'start'      => 18,
                        'end'        => 19,
                        'raw'        => '100',
                    ],
                    'value' => [
                        'name'       => 'value',
                        'name_token' => 22,
                        'start'      => 24,
                        'end'        => 25,
                        'raw'        => '50',
                    ],
                ],
            ],
            'mixed-positional-and-named-args' => [
                'testMarker' => '/* testMixedPositionalAndNamedArgs */',
                'targetType' => \T_STRING,
                'expected'   => [
                    1 => [
                        'start'      => 2,
                        'end'        => 2,
                        'raw'        => '$string',
                    ],
                    'double_encode' => [
                        'name'       => 'double_encode',
                        'name_token' => 5,
                        'start'      => 7,
                        'end'        => 8,
                        'raw'        => 'false',
                    ],
                ],
            ],
            'named-args-nested-function-call-outer' => [
                'testMarker' => '/* testNestedFunctionCallOuter */',
                'targetType' => \T_STRING,
                'expected'   => [
                    'start_index' => [
                        'name'       => 'start_index',
                        'name_token' => 4,
                        'start'      => 6,
                        'end'        => 17,
                        'raw'        => '/* testNestedFunctionCallInner1 */ $obj->getPos(skip: false)',
                    ],
                    'count' => [
                        'name'       => 'count',
                        'name_token' => 21,
                        'start'      => 23,
                        'end'        => 32,
                        'raw'        => '/* testNestedFunctionCallInner2 */ count(array_or_countable: $array)',
                    ],
                    'value' => [
                        'name'       => 'value',
                        'name_token' => 36,
                        'start'      => 38,
                        'end'        => 40,
                        'raw'        => '50',
                    ],
                ],
            ],
            'named-args-nested-function-call-inner-1' => [
                'testMarker' => '/* testNestedFunctionCallInner1 */',
                'targetType' => \T_STRING,
                'expected'   => [
                    'skip' => [
                        'name'       => 'skip',
                        'name_token' => 2,
                        'start'      => 4,
                        'end'        => 5,
                        'raw'        => 'false',
                    ],
                ],
            ],
            'named-args-nested-function-call-inner-2' => [
                'testMarker' => '/* testNestedFunctionCallInner2 */',
                'targetType' => \T_STRING,
                'expected'   => [
                    'array_or_countable' => [
                        'name'       => 'array_or_countable',
                        'name_token' => 2,
                        'start'      => 4,
                        'end'        => 5,
                        'raw'        => '$array',
                    ],
                ],
            ],
            'named-args-in-fqn-function-call' => [
                'testMarker'    => '/* testNamespacedFQNFunction */',
                'targetType'    => ($php8Names === true) ? \T_NAME_FULLY_QUALIFIED : \T_STRING,
                'expected'      => [
                    'label' => [
                        'name'       => 'label',
                        'name_token' => 2,
                        'start'      => 4,
                        'end'        => 5,
                        'raw'        => '$string',
                    ],
                    'more' => [
                        'name'       => 'more',
                        'name_token' => 8,
                        'start'      => 10,
                        'end'        => 10,
                        'raw'        => 'false',
                    ],
                ],
                'targetContent' => ($php8Names === true) ? null : 'function_name',
            ],
            'named-args-in-variable-function-call' => [
                'testMarker' => '/* testVariableFunction */',
                'targetType' => \T_VARIABLE,
                'expected'   => [
                    'label' => [
                        'name'       => 'label',
                        'name_token' => 2,
                        'start'      => 4,
                        'end'        => 5,
                        'raw'        => '$string',
                    ],
                    'more' => [
                        'name'       => 'more',
                        'name_token' => 8,
                        'start'      => 10,
                        'end'        => 10,
                        'raw'        => 'false',
                    ],
                ],
            ],
            'named-args-in-class-instantiation-with-static' => [
                'testMarker' => '/* testClassInstantiationStatic */',
                'targetType' => \T_STATIC,
                'expected'   => [
                    'label' => [
                        'name'       => 'label',
                        'name_token' => 2,
                        'start'      => 4,
                        'end'        => 5,
                        'raw'        => '$string',
                    ],
                    'more' => [
                        'name'       => 'more',
                        'name_token' => 8,
                        'start'      => 10,
                        'end'        => 10,
                        'raw'        => 'false',
                    ],
                ],
            ],
            'named-args-in-anon-class-instantiation' => [
                'testMarker' => '/* testAnonClass */',
                'targetType' => \T_ANON_CLASS,
                'expected'   => [
                    'label' => [
                        'name'       => 'label',
                        'name_token' => 2,
                        'start'      => 4,
                        'end'        => 5,
                        'raw'        => '$string',
                    ],
                    'more' => [
                        'name'       => 'more',
                        'name_token' => 8,
                        'start'      => 10,
                        'end'        => 11,
                        'raw'        => 'false',
                    ],
                ],
            ],
            'named-args-non-ascii-names' => [
                'testMarker' => '/* testNonAsciiNames */',
                'targetType' => \T_STRING,
                'expected'   => [
                    '💩💩💩' => [
                        'name'       => '💩💩💩',
                        'name_token' => 2,
                        'start'      => 4,
                        'end'        => 6,
                        'raw'        => '[]',
                    ],
                    'Пасха' => [
                        'name'       => 'Пасха',
                        'name_token' => 9,
                        'start'      => 11,
                        'end'        => 12,
                        'raw'        => "'text'",
                    ],
                    '_valid' => [
                        'name'       => '_valid',
                        'name_token' => 15,
                        'start'      => 17,
                        'end'        => 18,
                        'raw'        => '123',
                    ],
                ],
            ],
            'mixed-positional-and-named-args-with-ternary' => [
                'testMarker' => '/* testMixedPositionalAndNamedArgsWithTernary */',
                'targetType' => \T_STRING,
                'expected'   => [
                    1 => [
                        'start'      => 2,
                        'end'        => 11,
                        'raw'        => '$cond ? true : false',
                    ],
                    'name' => [
                        'name'       => 'name',
                        'name_token' => 14,
                        'start'      => 16,
                        'end'        => 18,
                        'raw'        => '$value2',
                    ],
                ],
            ],
            'named-args-with-ternary' => [
                'testMarker' => '/* testNamedArgWithTernary */',
                'targetType' => \T_STRING,
                'expected'   => [
                    'label' => [
                        'name'       => 'label',
                        'name_token' => 3,
                        'start'      => 5,
                        'end'        => 14,
                        'raw'        => '$cond ? true : false',
                    ],
                    'more' => [
                        'name'       => 'more',
                        'name_token' => 17,
                        'start'      => 19,
                        'end'        => 29,
                        'raw'        => '$cond ? CONSTANT_A : CONSTANT_B',
                    ],
                ],
            ],
            'ternary-with-function-call-in-then' => [
                'testMarker' => '/* testTernaryWithFunctionCallsInThenElse */',
                'targetType' => \T_STRING,
                'expected'   => [
                    'label' => [
                        'name'       => 'label',
                        'name_token' => 3,
                        'start'      => 5,
                        'end'        => 7,
                        'raw'        => '$something',
                    ],
                ],
            ],
            'ternary-with-function-call-in-else' => [
                'testMarker' => '/* testTernaryWithFunctionCallsInElse */',
                'targetType' => \T_STRING,
                'expected'   => [
                    'more' => [
                        'name'       => 'more',
                        'name_token' => 3,
                        'start'      => 5,
                        'end'        => 7,
                        'raw'        => '$something_else',
                    ],
                ],
            ],
            'named-args-for-exit' => [
                'testMarker' => '/* testExitWithNamedParam */',
                'targetType' => \T_EXIT,
                'expected'   => [
                    'status' => [
                        'name'       => 'status',
                        'name_token' => 3,
                        'start'      => 5,
                        'end'        => 7,
                        'raw'        => '1',
                    ],
                ],
            ],
            'named-args-for-FQN-die' => [
                'testMarker' => '/* testFQNDieWithNamedParam */',
                'targetType' => \T_EXIT,
                'expected'   => [
                    'status' => [
                        'name'       => 'status',
                        'name_token' => 3,
                        'start'      => 5,
                        'end'        => 7,
                        'raw'        => '1',
                    ],
                ],
            ],

            'named-args-compile-error-named-before-positional' => [
                'testMarker' => '/* testCompileErrorNamedBeforePositional */',
                'targetType' => \T_STRING,
                'expected'   => [
                    'param' => [
                        'name'       => 'param',
                        'name_token' => 2,
                        'start'      => 4,
                        'end'        => 5,
                        'raw'        => '$bar',
                    ],
                    2 => [
                        'start'      => 7,
                        'end'        => 8,
                        'raw'        => '$foo',
                    ],
                ],
            ],
            'named-args-error-exception-duplicate-name' => [
                'testMarker' => '/* testDuplicateName */',
                'targetType' => \T_STRING,
                'expected'   => [
                    'param' => [
                        'name'       => 'param',
                        'name_token' => 2,
                        'start'      => 4,
                        'end'        => 5,
                        'raw'        => '1',
                    ],
                    2 => [
                        'name'       => 'param',
                        'name_token' => 8,
                        'start'      => 10,
                        'end'        => 11,
                        'raw'        => '2',
                    ],
                ],
            ],
            'named-args-error-exception-incorrect-order-variadic' => [
                'testMarker' => '/* testIncorrectOrderWithVariadic */',
                'targetType' => \T_STRING,
                'expected'   => [
                    'start_index' => [
                        'name'       => 'start_index',
                        'name_token' => 2,
                        'start'      => 4,
                        'end'        => 5,
                        'raw'        => '0',
                    ],
                    2 => [
                        'start'      => 7,
                        'end'        => 14,
                        'raw'        => '...[100, 50]',
                    ],
                ],
            ],
            // Prior to PHP 8.1, this was a compile error, but this is now supported.
            'named-args-after-variadic' => [
                'testMarker' => '/* testPHP81NamedParamAfterVariadic */',
                'targetType' => \T_STRING,
                'expected'   => [
                    1 => [
                        'start'      => 2,
                        'end'        => 3,
                        'raw'        => '...$values',
                    ],
                    'param' => [
                        'name'       => 'param',
                        'name_token' => 6,
                        'start'      => 8,
                        'end'        => 9,
                        'raw'        => '$value',
                    ],
                ],
            ],
            'named-args-parse-error-dynamic-name' => [
                'testMarker' => '/* testParseErrorDynamicName */',
                'targetType' => \T_STRING,
                'expected'   => [
                    1 => [
                        'start'      => 2,
                        'end'        => 5,
                        'raw'        => '$variableStoringParamName: $value',
                    ],
                ],
            ],
            'named-args-using-reserved-keywords' => [
                'testMarker' => '/* testReservedKeywordAsName */',
                'targetType' => \T_STRING,
                'expected'   => [
                    'abstract' => [
                        'name'       => 'abstract',
                        'name_token' => 4,
                        'start'      => 6,
                        'end'        => 7,
                        'raw'        => '$value',
                    ],
                    'class' => [
                        'name'       => 'class',
                        'name_token' => 11,
                        'start'      => 13,
                        'end'        => 14,
                        'raw'        => '$value',
                    ],
                    'const' => [
                        'name'       => 'const',
                        'name_token' => 18,
                        'start'      => 20,
                        'end'        => 21,
                        'raw'        => '$value',
                    ],
                    'function' => [
                        'name'       => 'function',
                        'name_token' => 25,
                        'start'      => 27,
                        'end'        => 28,
                        'raw'        => '$value',
                    ],
                    'iterable' => [
                        'name'       => 'iterable',
                        'name_token' => 32,
                        'start'      => 34,
                        'end'        => 35,
                        'raw'        => '$value',
                    ],
                    'match' => [
                        'name'       => 'match',
                        'name_token' => 39,
                        'start'      => 41,
                        'end'        => 42,
                        'raw'        => '$value',
                    ],
                    'protected' => [
                        'name'       => 'protected',
                        'name_token' => 46,
                        'start'      => 48,
                        'end'        => 49,
                        'raw'        => '$value',
                    ],
                    'object' => [
                        'name'       => 'object',
                        'name_token' => 53,
                        'start'      => 55,
                        'end'        => 56,
                        'raw'        => '$value',
                    ],
                    'parent' => [
                        'name'       => 'parent',
                        'name_token' => 60,
                        'start'      => 62,
                        'end'        => 63,
                        'raw'        => '$value',
                    ],
                ],
            ],
        ];
    }
}
