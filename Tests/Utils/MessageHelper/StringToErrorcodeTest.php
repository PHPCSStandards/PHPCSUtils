<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2021 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\MessageHelper;

use PHPCSUtils\Utils\MessageHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Utils\MessageHelper::stringToErrorcode
 *
 * @group messagehelper
 *
 * @since 1.0.0
 */
class StringToErrorcodeTest extends TestCase
{

    /**
     * Test the stringToErrorcode() method.
     *
     * @dataProvider dataStringToErrorCode
     *
     * @param string $input    The input string.
     * @param string $expected The expected function output.
     *
     * @return void
     */
    public function testStringToErrorCode($input, $expected)
    {
        $this->assertSame($expected, MessageHelper::stringToErrorCode($input));
    }

    /**
     * Data provider.
     *
     * @see testStringToErrorCode() For the array format.
     *
     * @return array
     */
    public function dataStringToErrorCode()
    {
        return [
            'no-special-chars'  => [
                'input'    => 'dir_name',
                'expected' => 'dir_name',
            ],
            'full-stop'         => [
                'input'    => 'soap.wsdl_cache',
                'expected' =>  'soap_wsdl_cache',
            ],
            'dash-and-space'    => [
                'input'    => 'arbitrary-string with space',
                'expected' =>  'arbitrary_string_with_space',
            ],
            'no-alphanum-chars' => [
                'input'    => '^%*&%*€à?',
                'expected' =>  '____________',
            ],
        ];
    }
}
