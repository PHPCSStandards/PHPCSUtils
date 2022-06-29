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
 * Tests the support for the PHP 8.0 named parameters feature in the
 * \PHPCSUtils\Utils\PassedParameters::getParameter() and
 * \PHPCSUtils\Utils\PassedParameters::getParameterFromStack() methods.
 *
 * @covers \PHPCSUtils\Utils\PassedParameters::getParameter
 * @covers \PHPCSUtils\Utils\PassedParameters::getParameterFromStack
 *
 * @group passedparameters
 *
 * @since 1.0.0
 */
class GetParameterFromStackTest extends UtilityMethodTestCase
{

    /**
     * Test retrieving the parameter details from a function call without parameters.
     *
     * @return void
     */
    public function testGetParameterNoParams()
    {
        $stackPtr = $this->getTargetToken('/* testNoParams */', \T_STRING);

        $result = PassedParameters::getParameter(self::$phpcsFile, $stackPtr, 2, 'value');
        $this->assertFalse($result);
    }

    /**
     * Test retrieving the parameter details from a non-function call without passing a valid name
     * to make sure that no exception is thrown for the missing parameter name.
     *
     * @dataProvider dataGetParameterNonFunctionCallNoParamName
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType The type of token to look for.
     *
     * @return void
     */
    public function testGetParameterNonFunctionCallNoParamName($testMarker, $targetType)
    {
        $stackPtr = $this->getTargetToken($testMarker, $targetType);
        $expected = [
            'start' => ($stackPtr + 5),
            'end'   => ($stackPtr + 6),
            'raw'   => '$var2',
            'clean' => '$var2',
        ];

        $result = PassedParameters::getParameter(self::$phpcsFile, $stackPtr, 2);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetParameterNonFunctionCallNoParamName() For the array format.
     *
     * @return array
     */
    public function dataGetParameterNonFunctionCallNoParamName()
    {
        return [
            'isset' => [
                'testMarker' => '/* testIsset */',
                'targetType' => \T_ISSET,
            ],
            'array' => [
                'testMarker' => '/* testArray */',
                'targetType' => \T_ARRAY,
            ],
        ];
    }

    /**
     * Test retrieving the parameter details from a function call with only positional parameters
     * without passing a valid name to make sure no exception is thrown.
     *
     * @return void
     */
    public function testGetParameterFunctionCallPositionalNoParamName()
    {
        $stackPtr = $this->getTargetToken('/* testAllParamsPositional */', \T_STRING);
        $expected = [
            'start' => ($stackPtr + 5),
            'end'   => ($stackPtr + 6),
            'raw'   => "'value'",
            'clean' => "'value'",
        ];

        $result = PassedParameters::getParameter(self::$phpcsFile, $stackPtr, 2);
        $this->assertSame($expected, $result);
    }

    /**
     * Test receiving an expected exception when trying to retrieve the parameter details
     * from a function call with only named parameters without passing a valid name.
     *
     * @return void
     */
    public function testGetParameterFunctionCallMissingParamName()
    {
        $this->expectPhpcsException(
            'To allow for support for PHP 8 named parameters, the $paramNames parameter must be passed.'
        );

        $stackPtr = $this->getTargetToken('/* testAllParamsNamedStandardOrder */', \T_STRING);

        PassedParameters::getParameter(self::$phpcsFile, $stackPtr, 2);
    }

    /**
     * Test receiving an expected exception when trying to retrieve the parameter details
     * from a function call with only positional parameters without passing a valid name with
     * the requested parameter offset not being set.
     *
     * @return void
     */
    public function testGetParameterFunctionCallPositionalMissingParamNameNonExistentParam()
    {
        $this->expectPhpcsException(
            'To allow for support for PHP 8 named parameters, the $paramNames parameter must be passed.'
        );

        $stackPtr = $this->getTargetToken('/* testAllParamsPositional */', \T_STRING);

        PassedParameters::getParameter(self::$phpcsFile, $stackPtr, 10);
    }

    /**
     * Verify that the $limit parameter used with `PassedParameters::getParameters()` from within the
     * `PassedParameters::getParameter()` function call does not interfer with the handling of named parameters.
     *
     * @dataProvider dataGetParameterFunctionCallWithParamName
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected function output.
     *
     * @return void
     */
    public function testGetParameterFunctionCallWithParamName($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_STRING);

        $expected['start'] += $stackPtr;
        $expected['end']   += $stackPtr;
        if (isset($expected['name_start'], $expected['name_end']) === true) {
            $expected['name_start'] += $stackPtr;
            $expected['name_end']   += $stackPtr;
        }
        $expected['clean'] = $expected['raw'];

        $result = PassedParameters::getParameter(self::$phpcsFile, $stackPtr, 2, 'value');
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetParameterFunctionCallWithParamName() For the array format.
     *
     * @return array
     */
    public function dataGetParameterFunctionCallWithParamName()
    {
        /*
         * Work around to account for the different token positions due to the old tokenization
         * to T_GOTO_LABEL which joins two tokens into one (incorrectly).
         */
        $namedParamsInPhpcs = \version_compare(Helper::getVersion(), '3.6.0', '>=');

        return [
            'all-named-non-standard-order' => [
                'testMarker' => '/* testAllParamsNamedNonStandardOrder */',
                'expected'   => [
                    'name_start' => ($namedParamsInPhpcs === true) ? 46 : 42,
                    'name_end'   => ($namedParamsInPhpcs === true) ? 49 : 44,
                    'name'       => 'value',
                    'start'      => ($namedParamsInPhpcs === true) ? 50 : 45,
                    'end'        => ($namedParamsInPhpcs === true) ? 51 : 46,
                    'raw'        => "'value'",
                ],
            ],
            'mixed-positional-and-named-target-non-named' => [
                'testMarker' => '/* testMixedPositionalAndNamedParams */',
                'expected'   => [
                    'start' => 6,
                    'end'   => 8,
                    'raw'   => "'value'",
                ],
            ],
        ];
    }

    /**
     * Test retrieving the details for a specific parameter from a function call or construct.
     *
     * @dataProvider dataGetParameterFromStack
     *
     * @param string      $testMarker       The comment which prefaces the target token in the test file.
     * @param array       $expectedName     The expected result array for the $name parameter.
     * @param array|false $expectedExpires  The expected result array for the $expires_or_options parameter.
     * @param array|false $expectedHttpOnly The expected result array for the $httponly parameter.
     *
     * @return void
     */
    public function testGetParameterFromStack($testMarker, $expectedName, $expectedExpires, $expectedHttpOnly)
    {
        $stackPtr   = $this->getTargetToken($testMarker, \T_STRING);
        $parameters = PassedParameters::getParameters(self::$phpcsFile, $stackPtr);

        /*
         * Test $name parameter. Param name passed as string.
         */
        $expected = false;
        if ($expectedName !== false) {
            // Start/end token position values in the expected array are set as offsets
            // in relation to the target token.
            // Change these to exact positions based on the retrieved stackPtr.
            $expected           = $expectedName;
            $expected['start'] += $stackPtr;
            $expected['end']   += $stackPtr;
            if (isset($expected['name_start'], $expected['name_end']) === true) {
                $expected['name_start'] += $stackPtr;
                $expected['name_end']   += $stackPtr;
            }
            $expected['clean'] = $expected['raw'];
        }

        $result = PassedParameters::getParameterFromStack($parameters, 1, 'name');
        $this->assertSame($expected, $result, 'Expected output for parameter 1 ("name") did not match');

        /*
         * Test $expires_or_options parameter. Param name passed as array with alternative names.
         */
        $expected = false;
        if ($expectedExpires !== false) {
            // Start/end token position values in the expected array are set as offsets
            // in relation to the target token.
            // Change these to exact positions based on the retrieved stackPtr.
            $expected           = $expectedExpires;
            $expected['start'] += $stackPtr;
            $expected['end']   += $stackPtr;
            if (isset($expected['name_start'], $expected['name_end']) === true) {
                $expected['name_start'] += $stackPtr;
                $expected['name_end']   += $stackPtr;
            }
            $expected['clean'] = $expected['raw'];
        }

        $result = PassedParameters::getParameterFromStack($parameters, 3, ['expires_or_options', 'expires', 'options']);
        $this->assertSame($expected, $result, 'Expected output for parameter 3 ("expires_or_options") did not match');

        /*
         * Test $httponly parameter. Param name passed as array.
         */
        $expected = false;
        if ($expectedHttpOnly !== false) {
            // Start/end token position values in the expected array are set as offsets
            // in relation to the target token.
            // Change these to exact positions based on the retrieved stackPtr.
            $expected           = $expectedHttpOnly;
            $expected['start'] += $stackPtr;
            $expected['end']   += $stackPtr;
            if (isset($expected['name_start'], $expected['name_end']) === true) {
                $expected['name_start'] += $stackPtr;
                $expected['name_end']   += $stackPtr;
            }
            $expected['clean'] = $expected['raw'];
        }

        $result = PassedParameters::getParameterFromStack($parameters, 7, ['httponly']);
        $this->assertSame($expected, $result, 'Expected output for parameter 7 ("httponly") did not match');
    }

    /**
     * Data provider.
     *
     * @see testGetParameterFromStack() For the array format.
     *
     * @return array
     */
    public function dataGetParameterFromStack()
    {
        /*
         * Work around to account for the different token positions due to the old tokenization
         * to T_GOTO_LABEL which joins two tokens into one (incorrectly).
         */
        $namedParamsInPhpcs = \version_compare(Helper::getVersion(), '3.6.0', '>=');

        return [
            'all-params-all-positional' => [
                'testMarker'       => '/* testAllParamsPositional */',
                'expectedName'     => [
                    'start' => 2,
                    'end'   => 3,
                    'raw'   => "'name'",
                ],
                'expectedExpires'  => [
                    'start' => 8,
                    'end'   => 25,
                    'raw'   => 'time() + (60 * 60 * 24)',
                ],
                'expectedHttpOnly' => [
                    'start' => 36,
                    'end'   => 38,
                    'raw'   => 'false',
                ],
            ],
            'all-params-all-named-standard-order' => [
                'testMarker'       => '/* testAllParamsNamedStandardOrder */',
                'expectedName'     => [
                    'name_start' => 2,
                    'name_end'   => ($namedParamsInPhpcs === true) ? 5 : 4,
                    'name'       => 'name',
                    'start'      => ($namedParamsInPhpcs === true) ? 6 : 5,
                    'end'        => ($namedParamsInPhpcs === true) ? 7 : 6,
                    'raw'        => "'name'",
                ],
                'expectedExpires'  => [
                    'name_start' => ($namedParamsInPhpcs === true) ? 16 : 14,
                    'name_end'   => ($namedParamsInPhpcs === true) ? 19 : 16,
                    'name'       => 'expires_or_options',
                    'start'      => ($namedParamsInPhpcs === true) ? 20 : 17,
                    'end'        => ($namedParamsInPhpcs === true) ? 37 : 34,
                    'raw'        => 'time() + (60 * 60 * 24)',
                ],
                'expectedHttpOnly' => [
                    'name_start' => ($namedParamsInPhpcs === true) ? 60 : 54,
                    'name_end'   => ($namedParamsInPhpcs === true) ? 63 : 56,
                    'name'       => 'httponly',
                    'start'      => ($namedParamsInPhpcs === true) ? 64 : 57,
                    'end'        => ($namedParamsInPhpcs === true) ? 66 : 59,
                    'raw'        => 'false',
                ],
            ],
            'all-params-all-named-random-order' => [
                'testMarker'       => '/* testAllParamsNamedNonStandardOrder */',
                'expectedName'     => [
                    'name_start' => ($namedParamsInPhpcs === true) ? 32 : 30,
                    'name_end'   => ($namedParamsInPhpcs === true) ? 35 : 32,
                    'name'       => 'name',
                    'start'      => ($namedParamsInPhpcs === true) ? 36 : 33,
                    'end'        => ($namedParamsInPhpcs === true) ? 37 : 34,
                    'raw'        => "'name'",
                ],
                'expectedExpires'  => [
                    'name_start' => 2,
                    'name_end'   => ($namedParamsInPhpcs === true) ? 5 : 4,
                    'name'       => 'expires_or_options',
                    'start'      => ($namedParamsInPhpcs === true) ? 6 : 5,
                    'end'        => ($namedParamsInPhpcs === true) ? 23 : 22,
                    'raw'        => 'time() + (60 * 60 * 24)',
                ],
                'expectedHttpOnly' => [
                    'name_start' => ($namedParamsInPhpcs === true) ? 53 : 48,
                    'name_end'   => ($namedParamsInPhpcs === true) ? 56 : 50,
                    'name'       => 'httponly',
                    'start'      => ($namedParamsInPhpcs === true) ? 57 : 51,
                    'end'        => ($namedParamsInPhpcs === true) ? 58 : 52,
                    'raw'        => 'false',
                ],
            ],
            'all-params-mixed-positional-and-named' => [
                'testMarker'       => '/* testMixedPositionalAndNamedParams */',
                'expectedName'     => [
                    'start'      => 2,
                    'end'        => 4,
                    'raw'        => "'name'",
                ],
                'expectedExpires'  => [
                    'start'      => 10,
                    'end'        => 28,
                    'raw'        => 'time() + (60 * 60 * 24)',
                ],
                'expectedHttpOnly' => [
                    'name_start' => ($namedParamsInPhpcs === true) ? 44 : 42,
                    'name_end'   => ($namedParamsInPhpcs === true) ? 47 : 44,
                    'name'       => 'httponly',
                    'start'      => ($namedParamsInPhpcs === true) ? 48 : 45,
                    'end'        => ($namedParamsInPhpcs === true) ? 49 : 46,
                    'raw'        => 'false',
                ],
            ],
            'select-params-mixed-positional-and-named' => [
                'testMarker'       => '/* testMixedPositionalAndNamedParamsNotAllOptionalSet */',
                'expectedName'     => [
                    'start'      => 2,
                    'end'        => 4,
                    'raw'        => "'name'",
                ],
                'expectedExpires'  => [
                    'name_start' => 6,
                    'name_end'   => ($namedParamsInPhpcs === true) ? 9 : 8,
                    'name'       => 'expires_or_options',
                    'start'      => ($namedParamsInPhpcs === true) ? 10 : 9,
                    'end'        => ($namedParamsInPhpcs === true) ? 27 : 26,
                    'raw'        => 'time() + (60 * 60 * 24)',
                ],
                'expectedHttpOnly' => false,
            ],
            'select-params-mixed-positional-and-named-old-name' => [
                'testMarker'       => '/* testMixedPositionalAndNamedParamsOldName */',
                'expectedName'     => [
                    'start'      => 2,
                    'end'        => 4,
                    'raw'        => "'name'",
                ],
                'expectedExpires'  => [
                    'name_start' => 6,
                    'name_end'   => ($namedParamsInPhpcs === true) ? 9 : 8,
                    'name'       => 'expires',
                    'start'      => ($namedParamsInPhpcs === true) ? 10 : 9,
                    'end'        => ($namedParamsInPhpcs === true) ? 27 : 26,
                    'raw'        => 'time() + (60 * 60 * 24)',
                ],
                'expectedHttpOnly' => false,
            ],
        ];
    }
}
