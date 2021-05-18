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
 * Tests for the \PHPCSUtils\Utils\PassedParameters::getParameters() methods for
 * when the $limit parameter has been passed.
 *
 * @covers \PHPCSUtils\Utils\PassedParameters::getParameters
 *
 * @group passedparameters
 *
 * @since 1.0.0
 */
class GetParametersWithLimitTest extends UtilityMethodTestCase
{

    /**
     * Test retrieving the parameter details with a limit from an array without parameters.
     *
     * @covers \PHPCSUtils\Utils\PassedParameters::getParameters
     * @covers \PHPCSUtils\Utils\PassedParameters::getParameter
     *
     * @return void
     */
    public function testGetParametersWithLimitNoParams()
    {
        $stackPtr = $this->getTargetToken('/* testNoParams */', \T_ARRAY);

        $result = PassedParameters::getParameters(self::$phpcsFile, $stackPtr, 3);
        $this->assertSame([], $result);
        $this->assertCount(0, $result);

        // Limit is automatically applied to getParameter().
        $result = PassedParameters::getParameter(self::$phpcsFile, $stackPtr, 3);
        $this->assertFalse($result);
    }

    /**
     * Test passing an invalid limit.
     *
     * @dataProvider dataGetParametersWithIneffectiveLimit
     *
     * @param mixed $limit Parameter value for the $limit parameter.
     *
     * @return void
     */
    public function testGetParametersWithIneffectiveLimit($limit)
    {
        $stackPtr = $this->getTargetToken('/* testFunctionCall */', \T_STRING);

        $result = PassedParameters::getParameters(self::$phpcsFile, $stackPtr, $limit);
        $this->assertNotEmpty($result);
        $this->assertCount(7, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetParametersWithIneffectiveLimit() For the array format.
     *
     * @return array
     */
    public function dataGetParametersWithIneffectiveLimit()
    {
        return [
            'invalid-limit-wrong-type-null'       => [null],
            'invalid-limit-wrong-type-bool'       => [true],
            'invalid-limit-wrong-type-string'     => ['10'],
            'invalid-limit-negative-int'          => [-10],
            'valid-limit-set-to-0 = no-limit'     => [0],
            'valid-limit-higher-than-param-count' => [10],
        ];
    }

    /**
     * Test retrieving the parameter details from a function call or construct.
     *
     * @dataProvider dataGetParametersWithLimit
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType The type of token to look for.
     * @param array      $limit      The number of parameters to limit this call to.
     *                               Should match the expected count.
     * @param array      $expected   Optional. The expected return value. Only tested when not empty.
     *
     * @return void
     */
    public function testGetParametersWithLimit($testMarker, $targetType, $limit, $expected = [])
    {
        $stackPtr = $this->getTargetToken($testMarker, [$targetType]);

        $result = PassedParameters::getParameters(self::$phpcsFile, $stackPtr, $limit);
        $this->assertNotEmpty($result);
        $this->assertCount($limit, $result);

        if (empty($expected) === true) {
            return;
        }

        // Start/end token position values in the expected array are set as offsets
        // in relation to the target token.
        // Change these to exact positions based on the retrieved stackPtr.
        foreach ($expected as $key => $value) {
            $expected[$key]['start'] = ($stackPtr + $value['start']);
            $expected[$key]['end']   = ($stackPtr + $value['end']);
        }

        foreach ($result as $key => $value) {
            // The GetTokensAsString functions have their own tests, no need to duplicate it here.
            unset($result[$key]['clean']);
        }

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetParametersWithLimit() For the array format.
     *
     * @return array
     */
    public function dataGetParametersWithLimit()
    {
        return [
            'function-call' => [
                '/* testFunctionCall */',
                \T_STRING,
                2,
            ],
            'long-array-no-keys' => [
                '/* testSimpleLongArray */',
                \T_ARRAY,
                1,
                [
                    1 => [
                        'start' => 2,
                        'end'   => 3,
                        'raw'   => '1',
                    ],
                ],
            ],
            'short-array-no-keys' => [
                '/* testSimpleShortArray */',
                \T_OPEN_SHORT_ARRAY,
                5,
            ],
            'long-array-with-keys' => [
                '/* testLongArrayWithKeys */',
                \T_ARRAY,
                7,
            ],
            'short-array-with-keys' => [
                '/* testShortArrayWithKeys */',
                \T_OPEN_SHORT_ARRAY,
                4,
            ],
        ];
    }
}
