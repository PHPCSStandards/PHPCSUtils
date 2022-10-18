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
 * @group passedparameters
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
     * @param string     $testMarker    The comment which prefaces the target token in the test file.
     * @param int|string $targetType    The type of token to look for.
     * @param array      $expected      The expected parameter array.
     * @param mixed      $targetContent Optional. The token content to look for.
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
            $expected[$key]['start'] = ($stackPtr + $value['start']);
            $expected[$key]['end']   = ($stackPtr + $value['end']);

            if (isset($value['name_start'], $value['name_end']) === true) {
                $expected[$key]['name_start'] = ($stackPtr + $value['name_start']);
                $expected[$key]['name_end']   = ($stackPtr + $value['name_end']);
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
     * @return array
     */
    public function dataGetParameters()
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
                    1 => [
                        'name_start' => 2,
                        'name_end'   => 3,
                        'name'       => 'start_index',
                        'start'      => 4,
                        'end'        => 5,
                        'raw'        => '0',
                    ],
                    2 => [
                        'name_start' => 7,
                        'name_end'   => 9,
                        'name'       => 'count',
                        'start'      => 10,
                        'end'        => 11,
                        'raw'        => '100',
                    ],
                    3 => [
                        'name_start' => 13,
                        'name_end'   => 15,
                        'name'       => 'value',
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
                    1 => [
                        'name_start' => 2,
                        'name_end'   => 6,
                        'name'       => 'start_index',
                        'start'      => 7,
                        'end'        => 8,
                        'raw'        => '0',
                    ],
                    2 => [
                        'name_start' => 10,
                        'name_end'   => 14,
                        'name'       => 'count',
                        'start'      => 15,
                        'end'        => 16,
                        'raw'        => '100',
                    ],
                    3 => [
                        'name_start' => 18,
                        'name_end'   => 22,
                        'name'       => 'value',
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
                    1 => [
                        'name_start' => 3,
                        'name_end'   => 6,
                        'name'       => 'start_index',
                        'start'      => 7,
                        'end'        => 8,
                        'raw'        => '0',
                    ],
                    2 => [
                        'name_start' => 10,
                        'name_end'   => 17,
                        'name'       => 'count',
                        'start'      => 18,
                        'end'        => 19,
                        'raw'        => '100',
                    ],
                    3 => [
                        'name_start' => 21,
                        'name_end'   => 23,
                        'name'       => 'value',
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
                    2 => [
                        'name_start' => 4,
                        'name_end'   => 6,
                        'name'       => 'double_encode',
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
                    1 => [
                        'name_start' => 2,
                        'name_end'   => 5,
                        'name'       => 'start_index',
                        'start'      => 6,
                        'end'        => 17,
                        'raw'        => '/* testNestedFunctionCallInner1 */ $obj->getPos(skip: false)',
                    ],
                    2 => [
                        'name_start' => 19,
                        'name_end'   => 22,
                        'name'       => 'count',
                        'start'      => 23,
                        'end'        => 32,
                        'raw'        => '/* testNestedFunctionCallInner2 */ count(array_or_countable: $array)',
                    ],
                    3 => [
                        'name_start' => 34,
                        'name_end'   => 37,
                        'name'       => 'value',
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
                    1 => [
                        'name_start' => 2,
                        'name_end'   => 3,
                        'name'       => 'skip',
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
                    1 => [
                        'name_start' => 2,
                        'name_end'   => 3,
                        'name'       => 'array_or_countable',
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
                    1 => [
                        'name_start' => 2,
                        'name_end'   => 3,
                        'name'       => 'label',
                        'start'      => 4,
                        'end'        => 5,
                        'raw'        => '$string',
                    ],
                    2 => [
                        'name_start' => 7,
                        'name_end'   => 9,
                        'name'       => 'more',
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
                    1 => [
                        'name_start' => 2,
                        'name_end'   => 3,
                        'name'       => 'label',
                        'start'      => 4,
                        'end'        => 5,
                        'raw'        => '$string',
                    ],
                    2 => [
                        'name_start' => 7,
                        'name_end'   => 9,
                        'name'       => 'more',
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
                    1 => [
                        'name_start' => 2,
                        'name_end'   => 3,
                        'name'       => 'label',
                        'start'      => 4,
                        'end'        => 5,
                        'raw'        => '$string',
                    ],
                    2 => [
                        'name_start' => 7,
                        'name_end'   => 9,
                        'name'       => 'more',
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
                    1 => [
                        'name_start' => 2,
                        'name_end'   => 3,
                        'name'       => 'label',
                        'start'      => 4,
                        'end'        => 5,
                        'raw'        => '$string',
                    ],
                    2 => [
                        'name_start' => 7,
                        'name_end'   => 9,
                        'name'       => 'more',
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
                    1 => [
                        'name_start' => 2,
                        'name_end'   => 3,
                        'name'       => '💩💩💩',
                        'start'      => 4,
                        'end'        => 6,
                        'raw'        => '[]',
                    ],
                    2 => [
                        'name_start' => 8,
                        'name_end'   => 10,
                        'name'       => 'Пасха',
                        'start'      => 11,
                        'end'        => 12,
                        'raw'        => "'text'",
                    ],
                    3 => [
                        'name_start' => 14,
                        'name_end'   => 16,
                        'name'       => '_valid',
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
                    2 => [
                        'name_start' => 13,
                        'name_end'   => 15,
                        'name'       => 'name',
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
                    1 => [
                        'name_start' => 2,
                        'name_end'   => 4,
                        'name'       => 'label',
                        'start'      => 5,
                        'end'        => 14,
                        'raw'        => '$cond ? true : false',
                    ],
                    2 => [
                        'name_start' => 16,
                        'name_end'   => 18,
                        'name'       => 'more',
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
                    1 => [
                        'name_start' => 2,
                        'name_end'   => 4,
                        'name'       => 'label',
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
                    1 => [
                        'name_start' => 2,
                        'name_end'   => 4,
                        'name'       => 'more',
                        'start'      => 5,
                        'end'        => 7,
                        'raw'        => '$something_else',
                    ],
                ],
            ],
            'named-args-compile-error-named-before-positional' => [
                'testMarker' => '/* testCompileErrorNamedBeforePositional */',
                'targetType' => \T_STRING,
                'expected'   => [
                    1 => [
                        'name_start' => 2,
                        'name_end'   => 3,
                        'name'       => 'param',
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
                    1 => [
                        'name_start' => 2,
                        'name_end'   => 3,
                        'name'       => 'param',
                        'start'      => 4,
                        'end'        => 5,
                        'raw'        => '1',
                    ],
                    2 => [
                        'name_start' => 7,
                        'name_end'   => 9,
                        'name'       => 'param',
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
                    1 => [
                        'name_start' => 2,
                        'name_end'   => 3,
                        'name'       => 'start_index',
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
                    2 => [
                        'name_start' => 5,
                        'name_end'   => 7,
                        'name'       => 'param',
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
                    1 => [
                        'name_start' => 2,
                        'name_end'   => 5,
                        'name'       => 'abstract',
                        'start'      => 6,
                        'end'        => 7,
                        'raw'        => '$value',
                    ],
                    2 => [
                        'name_start' => 9,
                        'name_end'   => 12,
                        'name'       => 'class',
                        'start'      => 13,
                        'end'        => 14,
                        'raw'        => '$value',
                    ],
                    3 => [
                        'name_start' => 16,
                        'name_end'   => 19,
                        'name'       => 'const',
                        'start'      => 20,
                        'end'        => 21,
                        'raw'        => '$value',
                    ],
                    4 => [
                        'name_start' => 23,
                        'name_end'   => 26,
                        'name'       => 'function',
                        'start'      => 27,
                        'end'        => 28,
                        'raw'        => '$value',
                    ],
                    5 => [
                        'name_start' => 30,
                        'name_end'   => 33,
                        'name'       => 'iterable',
                        'start'      => 34,
                        'end'        => 35,
                        'raw'        => '$value',
                    ],
                    6 => [
                        'name_start' => 37,
                        'name_end'   => 40,
                        'name'       => 'match',
                        'start'      => 41,
                        'end'        => 42,
                        'raw'        => '$value',
                    ],
                    7 => [
                        'name_start' => 44,
                        'name_end'   => 47,
                        'name'       => 'protected',
                        'start'      => 48,
                        'end'        => 49,
                        'raw'        => '$value',
                    ],
                    8 => [
                        'name_start' => 51,
                        'name_end'   => 54,
                        'name'       => 'object',
                        'start'      => 55,
                        'end'        => 56,
                        'raw'        => '$value',
                    ],
                    9 => [
                        'name_start' => 58,
                        'name_end'   => 61,
                        'name'       => 'parent',
                        'start'      => 62,
                        'end'        => 63,
                        'raw'        => '$value',
                    ],
                ],
            ],
        ];
    }
}
