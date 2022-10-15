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
        return [
            'all-named-non-standard-order' => [
                'testMarker' => '/* testAllParamsNamedNonStandardOrder */',
                'expected'   => [
                    'name_start' => 46,
                    'name_end'   => 49,
                    'name'       => 'value',
                    'start'      => 50,
                    'end'        => 51,
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
                    'name_end'   => 5,
                    'name'       => 'name',
                    'start'      => 6,
                    'end'        => 7,
                    'raw'        => "'name'",
                ],
                'expectedExpires'  => [
                    'name_start' => 16,
                    'name_end'   => 19,
                    'name'       => 'expires_or_options',
                    'start'      => 20,
                    'end'        => 37,
                    'raw'        => 'time() + (60 * 60 * 24)',
                ],
                'expectedHttpOnly' => [
                    'name_start' => 60,
                    'name_end'   => 63,
                    'name'       => 'httponly',
                    'start'      => 64,
                    'end'        => 66,
                    'raw'        => 'false',
                ],
            ],
            'all-params-all-named-random-order' => [
                'testMarker'       => '/* testAllParamsNamedNonStandardOrder */',
                'expectedName'     => [
                    'name_start' => 32,
                    'name_end'   => 35,
                    'name'       => 'name',
                    'start'      => 36,
                    'end'        => 37,
                    'raw'        => "'name'",
                ],
                'expectedExpires'  => [
                    'name_start' => 2,
                    'name_end'   => 5,
                    'name'       => 'expires_or_options',
                    'start'      => 6,
                    'end'        => 23,
                    'raw'        => 'time() + (60 * 60 * 24)',
                ],
                'expectedHttpOnly' => [
                    'name_start' => 53,
                    'name_end'   => 56,
                    'name'       => 'httponly',
                    'start'      => 57,
                    'end'        => 58,
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
                    'name_start' => 44,
                    'name_end'   => 47,
                    'name'       => 'httponly',
                    'start'      => 48,
                    'end'        => 49,
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
                    'name_end'   => 9,
                    'name'       => 'expires_or_options',
                    'start'      => 10,
                    'end'        => 27,
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
                    'name_end'   => 9,
                    'name'       => 'expires',
                    'start'      => 10,
                    'end'        => 27,
                    'raw'        => 'time() + (60 * 60 * 24)',
                ],
                'expectedHttpOnly' => false,
            ],
        ];
    }
}
