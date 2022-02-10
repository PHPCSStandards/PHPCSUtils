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
class GetParametersNamedTest extends UtilityMethodTestCase
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
        /*
         * Work arounds to account for:
         * 1. The different tokenization of namespaces names in PHP 8 and different PHPCS versions.
         * 2. The different token positions due to the old tokenization to T_GOTO_LABEL
         *    which joins two tokens into one (incorrectly).
         * 3. The new `match` keyword being recognized on PHP 8, but not before, while
         *    the `match` control structure is not supported in PHPCS yet.
         */
        $php8Names          = parent::usesPhp8NameTokens();
        $namedParamsInPhpcs = \version_compare(Helper::getVersion(), '3.6.0', '>=');
        $matchIsKeyword     = \version_compare(\PHP_VERSION_ID, '80000', '>=');

        return [
            'only-positional-args' => [
                '/* testPositionalArgs */',
                \T_STRING,
                [
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
                '/* testNamedArgs */',
                \T_STRING,
                [
                    1 => [
                        'name_start' => 2,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 3 : 2,
                        'name'       => 'start_index',
                        'start'      => ($namedParamsInPhpcs === true) ? 4 : 3,
                        'end'        => ($namedParamsInPhpcs === true) ? 5 : 4,
                        'raw'        => '0',
                    ],
                    2 => [
                        'name_start' => ($namedParamsInPhpcs === true) ? 7 : 6,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 9 : 7,
                        'name'       => 'count',
                        'start'      => ($namedParamsInPhpcs === true) ? 10 : 8,
                        'end'        => ($namedParamsInPhpcs === true) ? 11 : 9,
                        'raw'        => '100',
                    ],
                    3 => [
                        'name_start' => ($namedParamsInPhpcs === true) ? 13 : 11,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 15 : 12,
                        'name'       => 'value',
                        'start'      => ($namedParamsInPhpcs === true) ? 16 : 13,
                        'end'        => ($namedParamsInPhpcs === true) ? 17 : 14,
                        'raw'        => '50',
                    ],
                ],
            ],
            'named-args-multiline' => [
                '/* testNamedArgsMultiline */',
                \T_STRING,
                [
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
                '/* testNamedArgsWithWhitespaceAndComments */',
                \T_STRING,
                [
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
                        'name_end'   => ($namedParamsInPhpcs === true) ? 23 : 22,
                        'name'       => 'value',
                        'start'      => ($namedParamsInPhpcs === true) ? 24 : 23,
                        'end'        => ($namedParamsInPhpcs === true) ? 25 : 24,
                        'raw'        => '50',
                    ],
                ],
            ],
            'mixed-positional-and-named-args' => [
                '/* testMixedPositionalAndNamedArgs */',
                \T_STRING,
                [
                    1 => [
                        'start'      => 2,
                        'end'        => 2,
                        'raw'        => '$string',
                    ],
                    2 => [
                        'name_start' => 4,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 6 : 5,
                        'name'       => 'double_encode',
                        'start'      => ($namedParamsInPhpcs === true) ? 7 : 6,
                        'end'        => ($namedParamsInPhpcs === true) ? 8 : 7,
                        'raw'        => 'false',
                    ],
                ],
            ],
            'named-args-nested-function-call-outer' => [
                '/* testNestedFunctionCallOuter */',
                \T_STRING,
                [
                    1 => [
                        'name_start' => 2,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 5 : 4,
                        'name'       => 'start_index',
                        'start'      => ($namedParamsInPhpcs === true) ? 6 : 5,
                        'end'        => ($namedParamsInPhpcs === true) ? 17 : 15,
                        'raw'        => '/* testNestedFunctionCallInner1 */ $obj->getPos(skip: false)',
                    ],
                    2 => [
                        'name_start' => ($namedParamsInPhpcs === true) ? 19 : 17,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 22 : 19,
                        'name'       => 'count',
                        'start'      => ($namedParamsInPhpcs === true) ? 23 : 20,
                        'end'        => ($namedParamsInPhpcs === true) ? 32 : 28,
                        'raw'        => '/* testNestedFunctionCallInner2 */ count(array_or_countable: $array)',
                    ],
                    3 => [
                        'name_start' => ($namedParamsInPhpcs === true) ? 34 : 30,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 37 : 32,
                        'name'       => 'value',
                        'start'      => ($namedParamsInPhpcs === true) ? 38 : 33,
                        'end'        => ($namedParamsInPhpcs === true) ? 40 : 35,
                        'raw'        => '50',
                    ],
                ],
            ],
            'named-args-nested-function-call-inner-1' => [
                '/* testNestedFunctionCallInner1 */',
                \T_STRING,
                [
                    1 => [
                        'name_start' => 2,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 3 : 2,
                        'name'       => 'skip',
                        'start'      => ($namedParamsInPhpcs === true) ? 4 : 3,
                        'end'        => ($namedParamsInPhpcs === true) ? 5 : 4,
                        'raw'        => 'false',
                    ],
                ],
            ],
            'named-args-nested-function-call-inner-2' => [
                '/* testNestedFunctionCallInner2 */',
                \T_STRING,
                [
                    1 => [
                        'name_start' => 2,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 3 : 2,
                        'name'       => 'array_or_countable',
                        'start'      => ($namedParamsInPhpcs === true) ? 4 : 3,
                        'end'        => ($namedParamsInPhpcs === true) ? 5 : 4,
                        'raw'        => '$array',
                    ],
                ],
            ],
            'named-args-in-fqn-function-call' => [
                '/* testNamespacedFQNFunction */',
                ($php8Names === true) ? \T_NAME_FULLY_QUALIFIED : \T_STRING,
                [
                    1 => [
                        'name_start' => 2,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 3 : 2,
                        'name'       => 'label',
                        'start'      => ($namedParamsInPhpcs === true) ? 4 : 3,
                        'end'        => ($namedParamsInPhpcs === true) ? 5 : 4,
                        'raw'        => '$string',
                    ],
                    2 => [
                        'name_start' => ($namedParamsInPhpcs === true) ? 7 : 6,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 9 : 7,
                        'name'       => 'more',
                        'start'      => ($namedParamsInPhpcs === true) ? 10 : 8,
                        'end'        => ($namedParamsInPhpcs === true) ? 10 : 8,
                        'raw'        => 'false',
                    ],
                ],
                ($php8Names === true) ? null : 'function_name',
            ],
            'named-args-in-variable-function-call' => [
                '/* testVariableFunction */',
                \T_VARIABLE,
                [
                    1 => [
                        'name_start' => 2,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 3 : 2,
                        'name'       => 'label',
                        'start'      => ($namedParamsInPhpcs === true) ? 4 : 3,
                        'end'        => ($namedParamsInPhpcs === true) ? 5 : 4,
                        'raw'        => '$string',
                    ],
                    2 => [
                        'name_start' => ($namedParamsInPhpcs === true) ? 7 : 6,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 9 : 7,
                        'name'       => 'more',
                        'start'      => ($namedParamsInPhpcs === true) ? 10 : 8,
                        'end'        => ($namedParamsInPhpcs === true) ? 10 : 8,
                        'raw'        => 'false',
                    ],
                ],
            ],
            'named-args-in-class-instantiation-with-static' => [
                '/* testClassInstantiationStatic */',
                \T_STATIC,
                [
                    1 => [
                        'name_start' => 2,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 3 : 2,
                        'name'       => 'label',
                        'start'      => ($namedParamsInPhpcs === true) ? 4 : 3,
                        'end'        => ($namedParamsInPhpcs === true) ? 5 : 4,
                        'raw'        => '$string',
                    ],
                    2 => [
                        'name_start' => ($namedParamsInPhpcs === true) ? 7 : 6,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 9 : 7,
                        'name'       => 'more',
                        'start'      => ($namedParamsInPhpcs === true) ? 10 : 8,
                        'end'        => ($namedParamsInPhpcs === true) ? 10 : 8,
                        'raw'        => 'false',
                    ],
                ],
            ],
            'named-args-in-anon-class-instantiation' => [
                '/* testAnonClass */',
                \T_ANON_CLASS,
                [
                    1 => [
                        'name_start' => 2,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 3 : 2,
                        'name'       => 'label',
                        'start'      => ($namedParamsInPhpcs === true) ? 4 : 3,
                        'end'        => ($namedParamsInPhpcs === true) ? 5 : 4,
                        'raw'        => '$string',
                    ],
                    2 => [
                        'name_start' => ($namedParamsInPhpcs === true) ? 7 : 6,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 9 : 7,
                        'name'       => 'more',
                        'start'      => ($namedParamsInPhpcs === true) ? 10 : 8,
                        'end'        => ($namedParamsInPhpcs === true) ? 11 : 9,
                        'raw'        => 'false',
                    ],
                ],
            ],
            'named-args-non-ascii-names' => [
                '/* testNonAsciiNames */',
                \T_STRING,
                [
                    1 => [
                        'name_start' => 2,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 3 : 2,
                        'name'       => '💩💩💩',
                        'start'      => ($namedParamsInPhpcs === true) ? 4 : 3,
                        'end'        => ($namedParamsInPhpcs === true) ? 6 : 5,
                        'raw'        => '[]',
                    ],
                    2 => [
                        'name_start' => ($namedParamsInPhpcs === true) ? 8 : 7,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 10 : 8,
                        'name'       => 'Пасха',
                        'start'      => ($namedParamsInPhpcs === true) ? 11 : 9,
                        'end'        => ($namedParamsInPhpcs === true) ? 12 : 10,
                        'raw'        => "'text'",
                    ],
                    3 => [
                        'name_start' => ($namedParamsInPhpcs === true) ? 14 : 12,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 16 : 13,
                        'name'       => '_valid',
                        'start'      => ($namedParamsInPhpcs === true) ? 17 : 14,
                        'end'        => ($namedParamsInPhpcs === true) ? 18 : 15,
                        'raw'        => '123',
                    ],
                ],
            ],
            'mixed-positional-and-named-args-with-ternary' => [
                '/* testMixedPositionalAndNamedArgsWithTernary */',
                \T_STRING,
                [
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
                '/* testNamedArgWithTernary */',
                \T_STRING,
                [
                    1 => [
                        'name_start' => 2,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 4 : 3,
                        'name'       => 'label',
                        'start'      => ($namedParamsInPhpcs === true) ? 5 : 4,
                        'end'        => ($namedParamsInPhpcs === true) ? 14 : 13,
                        'raw'        => '$cond ? true : false',
                    ],
                    2 => [
                        'name_start' => ($namedParamsInPhpcs === true) ? 16 : 15,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 18 : 17,
                        'name'       => 'more',
                        'start'      => ($namedParamsInPhpcs === true) ? 19 : 18,
                        'end'        => ($namedParamsInPhpcs === true) ? 29 : 28,
                        'raw'        => '$cond ? CONSTANT_A : CONSTANT_B',
                    ],
                ],
            ],
            'ternary-with-function-call-in-then' => [
                '/* testTernaryWithFunctionCallsInThenElse */',
                \T_STRING,
                [
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
                '/* testTernaryWithFunctionCallsInElse */',
                \T_STRING,
                [
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
                '/* testCompileErrorNamedBeforePositional */',
                \T_STRING,
                [
                    1 => [
                        'name_start' => 2,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 3 : 2,
                        'name'       => 'param',
                        'start'      => ($namedParamsInPhpcs === true) ? 4 : 3,
                        'end'        => ($namedParamsInPhpcs === true) ? 5 : 4,
                        'raw'        => '$bar',
                    ],
                    2 => [
                        'start'      => ($namedParamsInPhpcs === true) ? 7 : 6,
                        'end'        => ($namedParamsInPhpcs === true) ? 8 : 7,
                        'raw'        => '$foo',
                    ],
                ],
            ],
            'named-args-error-exception-duplicate-name' => [
                '/* testDuplicateName */',
                \T_STRING,
                [
                    1 => [
                        'name_start' => 2,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 3 : 2,
                        'name'       => 'param',
                        'start'      => ($namedParamsInPhpcs === true) ? 4 : 3,
                        'end'        => ($namedParamsInPhpcs === true) ? 5 : 4,
                        'raw'        => '1',
                    ],
                    2 => [
                        'name_start' => ($namedParamsInPhpcs === true) ? 7 : 6,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 9 : 7,
                        'name'       => 'param',
                        'start'      => ($namedParamsInPhpcs === true) ? 10 : 8,
                        'end'        => ($namedParamsInPhpcs === true) ? 11 : 9,
                        'raw'        => '2',
                    ],
                ],
            ],
            'named-args-error-exception-incorrect-order-variadic' => [
                '/* testIncorrectOrderWithVariadic */',
                \T_STRING,
                [
                    1 => [
                        'name_start' => 2,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 3 : 2,
                        'name'       => 'start_index',
                        'start'      => ($namedParamsInPhpcs === true) ? 4 : 3,
                        'end'        => ($namedParamsInPhpcs === true) ? 5 : 4,
                        'raw'        => '0',
                    ],
                    2 => [
                        'start'      => ($namedParamsInPhpcs === true) ? 7 : 6,
                        'end'        => ($namedParamsInPhpcs === true) ? 14 : 13,
                        'raw'        => '...[100, 50]',
                    ],
                ],
            ],
            'named-args-compile-error-incorrect-order-variadic' => [
                '/* testCompileErrorIncorrectOrderWithVariadic */',
                \T_STRING,
                [
                    1 => [
                        'start'      => 2,
                        'end'        => 3,
                        'raw'        => '...$values',
                    ],
                    2 => [
                        'name_start' => 5,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 7 : 6,
                        'name'       => 'param',
                        'start'      => ($namedParamsInPhpcs === true) ? 8 : 7,
                        'end'        => ($namedParamsInPhpcs === true) ? 9 : 8,
                        'raw'        => '$value',
                    ],
                ],
            ],
            'named-args-parse-error-dynamic-name' => [
                '/* testParseErrorDynamicName */',
                \T_STRING,
                [
                    1 => [
                        'start'      => 2,
                        'end'        => 5,
                        'raw'        => '$variableStoringParamName: $value',
                    ],
                ],
            ],
            'named-args-using-reserved-keywords' => [
                '/* testReservedKeywordAsName */',
                \T_STRING,
                [
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
                        'name_end'   => ($namedParamsInPhpcs === true) ? 33 : 32,
                        'name'       => 'iterable',
                        'start'      => ($namedParamsInPhpcs === true) ? 34 : 33,
                        'end'        => ($namedParamsInPhpcs === true) ? 35 : 34,
                        'raw'        => '$value',
                    ],
                    6 => [
                        'name_start' => ($namedParamsInPhpcs === true) ? 37 : 36,
                        'name_end'   => ($namedParamsInPhpcs === true) ? 40 : (($matchIsKeyword === true) ? 39 : 38),
                        'name'       => 'match',
                        'start'      => ($namedParamsInPhpcs === true) ? 41 : (($matchIsKeyword === true) ? 40 : 39),
                        'end'        => ($namedParamsInPhpcs === true) ? 42 : (($matchIsKeyword === true) ? 41 : 40),
                        'raw'        => '$value',
                    ],
                    7 => [
                        'name_start' => ($namedParamsInPhpcs === true) ? 44 : (($matchIsKeyword === true) ? 43 : 42),
                        'name_end'   => ($namedParamsInPhpcs === true) ? 47 : (($matchIsKeyword === true) ? 46 : 45),
                        'name'       => 'protected',
                        'start'      => ($namedParamsInPhpcs === true) ? 48 : (($matchIsKeyword === true) ? 47 : 46),
                        'end'        => ($namedParamsInPhpcs === true) ? 49 : (($matchIsKeyword === true) ? 48 : 47),
                        'raw'        => '$value',
                    ],
                    8 => [
                        'name_start' => ($namedParamsInPhpcs === true) ? 51 : (($matchIsKeyword === true) ? 50 : 49),
                        'name_end'   => ($namedParamsInPhpcs === true) ? 54 : (($matchIsKeyword === true) ? 52 : 51),
                        'name'       => 'object',
                        'start'      => ($namedParamsInPhpcs === true) ? 55 : (($matchIsKeyword === true) ? 53 : 52),
                        'end'        => ($namedParamsInPhpcs === true) ? 56 : (($matchIsKeyword === true) ? 54 : 53),
                        'raw'        => '$value',
                    ],
                    9 => [
                        'name_start' => ($namedParamsInPhpcs === true) ? 58 : (($matchIsKeyword === true) ? 56 : 55),
                        'name_end'   => ($namedParamsInPhpcs === true) ? 61 : (($matchIsKeyword === true) ? 58 : 57),
                        'name'       => 'parent',
                        'start'      => ($namedParamsInPhpcs === true) ? 62 : (($matchIsKeyword === true) ? 59 : 58),
                        'end'        => ($namedParamsInPhpcs === true) ? 63 : (($matchIsKeyword === true) ? 60 : 59),
                        'raw'        => '$value',
                    ],
                ],
            ],
        ];
    }
}
