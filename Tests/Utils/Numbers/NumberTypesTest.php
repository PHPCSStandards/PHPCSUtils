<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Numbers;

use PHPCSUtils\Utils\Numbers;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHPCSUtils\Utils\Numbers::isDecimalInt(),
 * \PHPCSUtils\Utils\Numbers::isHexidecimalInt(),
 * \PHPCSUtils\Utils\Numbers::isBinaryInt(),
 * \PHPCSUtils\Utils\Numbers::isOctalInt() and the
 * \PHPCSUtils\Utils\Numbers::isFloat() method.
 *
 * @coversDefaultClass \PHPCSUtils\Utils\Numbers
 *
 * @since 1.0.0
 */
final class NumberTypesTest extends TestCase
{

    /**
     * Test correctly rejecting non-string input.
     *
     * @dataProvider dataNotAString
     * @covers       ::isDecimalInt
     *
     * @param mixed $input The input data.
     *
     * @return void
     */
    public function testIsDecimalIntInvalidInput($input)
    {
        $this->assertFalse(Numbers::isDecimalInt($input));
    }

    /**
     * Test correctly rejecting non-string input.
     *
     * @dataProvider dataNotAString
     * @covers       ::isHexidecimalInt
     *
     * @param mixed $input The input data.
     *
     * @return void
     */
    public function testIsHexidecimalIntInvalidInput($input)
    {
        $this->assertFalse(Numbers::isHexidecimalInt($input));
    }

    /**
     * Test correctly rejecting non-string input.
     *
     * @dataProvider dataNotAString
     * @covers       ::isBinaryInt
     *
     * @param mixed $input The input data.
     *
     * @return void
     */
    public function testIsBinaryIntInvalidInput($input)
    {
        $this->assertFalse(Numbers::isBinaryInt($input));
    }

    /**
     * Test correctly rejecting non-string input.
     *
     * @dataProvider dataNotAString
     * @covers       ::isOctalInt
     *
     * @param mixed $input The input data.
     *
     * @return void
     */
    public function testIsOctalIntInvalidInput($input)
    {
        $this->assertFalse(Numbers::isOctalInt($input));
    }

    /**
     * Test correctly rejecting non-string input.
     *
     * @dataProvider dataNotAString
     * @covers       ::isFloat
     *
     * @param mixed $input The input data.
     *
     * @return void
     */
    public function testIsFloatInvalidInput($input)
    {
        $this->assertFalse(Numbers::isFloat($input));
    }

    /**
     * Data Provider.
     *
     * @see testIsDecimalInt()     For the array format.
     * @see testIsHexidecimalInt() For the array format.
     * @see testIsBinaryInt()      For the array format.
     * @see testIsOctalInt()       For the array format.
     * @see testIsDecimalFloat()   For the array format.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dataNotAString()
    {
        return [
            'not-a-string-bool' => [
                'input' => true,
            ],
            'not-a-string-int' => [
                'input' => 10,
            ],
        ];
    }

    /**
     * Test correctly recognizing an arbitrary string representing a decimal integer.
     *
     * @dataProvider dataNumbers
     * @covers       ::isDecimalInt
     *
     * @param string              $input    The input string.
     * @param array<string, bool> $expected The expected output for the various functions.
     *
     * @return void
     */
    public function testIsDecimalInt($input, $expected)
    {
        $this->assertSame($expected['decimal'], Numbers::isDecimalInt($input));
    }

    /**
     * Test correctly recognizing an arbitrary string representing a hexidecimal integer.
     *
     * @dataProvider dataNumbers
     * @covers       ::isHexidecimalInt
     *
     * @param string              $input    The input string.
     * @param array<string, bool> $expected The expected output for the various functions.
     *
     * @return void
     */
    public function testIsHexidecimalInt($input, $expected)
    {
        $this->assertSame($expected['hex'], Numbers::isHexidecimalInt($input));
    }

    /**
     * Test correctly recognizing an arbitrary string representing a binary integer.
     *
     * @dataProvider dataNumbers
     * @covers       ::isBinaryInt
     *
     * @param string              $input    The input string.
     * @param array<string, bool> $expected The expected output for the various functions.
     *
     * @return void
     */
    public function testIsBinaryInt($input, $expected)
    {
        $this->assertSame($expected['binary'], Numbers::isBinaryInt($input));
    }

    /**
     * Test correctly recognizing an arbitrary string representing an octal integer.
     *
     * @dataProvider dataNumbers
     * @covers       ::isOctalInt
     *
     * @param string              $input    The input string.
     * @param array<string, bool> $expected The expected output for the various functions.
     *
     * @return void
     */
    public function testIsOctalInt($input, $expected)
    {
        $this->assertSame($expected['octal'], Numbers::isOctalInt($input));
    }

    /**
     * Test correctly recognizing an arbitrary string representing a decimal float.
     *
     * @dataProvider dataNumbers
     * @covers       ::isFloat
     *
     * @param string              $input    The input string.
     * @param array<string, bool> $expected The expected output for the various functions.
     *
     * @return void
     */
    public function testIsFloat($input, $expected)
    {
        $this->assertSame($expected['float'], Numbers::isFloat($input));
    }

    /**
     * Data Provider.
     *
     * @see testIsDecimalInt()     For the array format.
     * @see testIsHexidecimalInt() For the array format.
     * @see testIsBinaryInt()      For the array format.
     * @see testIsOctalInt()       For the array format.
     * @see testIsDecimalFloat()   For the array format.
     *
     * @return array<string, array<string, string|array<string, bool>>>
     */
    public static function dataNumbers()
    {
        return [
            // Not numeric strings.
            'empty-string' => [
                'input'    => '',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'string-not-a-number' => [
                'input'    => 'foobar',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'string-not-a-number-with-full-stop' => [
                'input'    => 'foo. bar',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-hex' => [
                'input'    => '0xZBHI28',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-binary' => [
                'input'    => '0b121457182',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-octal' => [
                // Note: in PHP 5.x this would still be accepted, though not interpreted correctly.
                'input'    => '0289',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-explicit-octal' => [
                'input'    => '0o289',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-two-decimal-points' => [
                'input'    => '1.287.2763',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-plus-no-exponent' => [
                'input'    => '1.287+2763',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-minus-no-exponent' => [
                'input'    => '1287-2763',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-exponent-no-multiplier' => [
                'input'    => '2872e',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-exponent-plus-no-multiplier' => [
                'input'    => '1.2872e+',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-exponent-minus-no-multiplier' => [
                'input'    => '1.2872e-',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-exponent-multiplier-float' => [
                'input'    => '376e2.3',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-exponent-plus-multiplier-float' => [
                'input'    => '3.76e+2.3',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-exponent-minus-multiplier-float' => [
                'input'    => '37.6e-2.3',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-exponent-plus-minus-multiplier' => [
                'input'    => '37.6e+-2',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-double-exponent' => [
                'input'    => '37.6e2e6',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],

            // Invalid numeric literal with separator look-a-like strings
            'invalid-numeric-literal-leading-underscore-decimal' => [
                'input'    => '_1',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-trailing-underscore-decimal' => [
                'input'    => '1_',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-leading-and-trailing-underscore-decimal' => [
                'input'    => '_1_',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-leading-underscore-octal' => [
                'input'    => '_072',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-leading-underscore-after-prefix-octal' => [
                'input'    => '0o_1256',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-trailing-underscore-octal' => [
                'input'    => '0O271_',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-leading-and-trailing-underscore-octal' => [
                'input'    => '_0o_1561_',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-leading-underscore-binary' => [
                'input'    => '_0b01',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-leading-underscore-after-prefix-binary' => [
                'input'    => '0b_101011',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-trailing-underscore-binary' => [
                'input'    => '0b10001_',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-leading-and-trailing-underscore-binary' => [
                'input'    => '_0b_00101_',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-leading-underscore-hex' => [
                'input'    => '_0xAF451212',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-leading-underscore-after-prefix-hex' => [
                'input'    => '0x_241FB4C',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-trailing-underscore-hex' => [
                'input'    => '0x12_',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-leading-and-trailing-underscore-hex' => [
                'input'    => '_0x_213CAD1_',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],

            'invalid-numeric-literal-leading-underscore-next-to-leading-zero-float' => [
                'input'    => '_012.12',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-leading-underscore-next-to-number-float' => [
                'input'    => '_12.12',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-leading-underscore-next-to-decimalpoint-float' => [
                'input'    => '_.1212',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-trailing-underscore-next-to-number-float' => [
                'input'    => '12.21378_',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-trailing-underscore-next-to-zero-float' => [
                'input'    => '12.700_',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-trailing-underscore-next-to-decimalpoint-float' => [
                'input'    => '12._',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-underscore-before-decimalpoint-float' => [
                'input'    => '1_.0',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-trailing-underscore-after-decimalpoint-float' => [
                'input'    => '1._0',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-underscore-before-e-float' => [
                'input'    => '1_e2',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-trailing-underscore-after-e-float' => [
                'input'    => '1e_2',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-numeric-literal-underscores-everywhere-float' => [
                'input'    => '_002781_._219_28173_E_+56_',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],

            // Decimal numeric strings.
            'decimal-single-digit-zero' => [
                'input'    => '0',
                'expected' => [
                    'decimal' => true,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'decimal-single-digit' => [
                'input'    => '9',
                'expected' => [
                    'decimal' => true,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'decimal-multi-digit' => [
                'input'    => '123456',
                'expected' => [
                    'decimal' => true,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'decimal-multi-digit-php-7.4' => [
                'input'    => '12_34_56',
                'expected' => [
                    'decimal' => true,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],

            // Hexidecimal numeric strings.
            // phpcs:disable PHPCompatibility.Numbers.RemovedHexadecimalNumericStrings.Found
            'hexidecimal-single-digit-zero' => [
                'input'    => '0x0',
                'expected' => [
                    'decimal' => false,
                    'hex'     => true,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'hexidecimal-single-digit' => [
                'input'    => '0xA',
                'expected' => [
                    'decimal' => false,
                    'hex'     => true,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'hexidecimal-multi-digit-all-numbers' => [
                'input'    => '0x123456',
                'expected' => [
                    'decimal' => false,
                    'hex'     => true,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'hexidecimal-multi-digit-no-numbers' => [
                'input'    => '0xABCDEF',
                'expected' => [
                    'decimal' => false,
                    'hex'     => true,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'hexidecimal-multi-digit-mixed' => [
                'input'    => '0xAB02F6',
                'expected' => [
                    'decimal' => false,
                    'hex'     => true,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'hexidecimal-multi-digit-mixed-uppercase-x' => [
                'input'    => '0XAB953C',
                'expected' => [
                    'decimal' => false,
                    'hex'     => true,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'hexidecimal-multi-digit-php-7.4' => [
                'input'    => '0x23_6A_3C',
                'expected' => [
                    'decimal' => false,
                    'hex'     => true,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            // phpcs:enable

            // Binary numeric strings.
            'binary-single-digit-zero' => [
                'input'    => '0b0',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => true,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'binary-single-digit-one' => [
                'input'    => '0b1',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => true,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'binary-multi-digit' => [
                'input'    => '0b1010',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => true,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'binary-multi-digit-uppercase-b' => [
                'input'    => '0B1000100100000',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => true,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'binary-multi-digit-php-7.4' => [
                'input'    => '0b100_000_000_00',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => true,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],

            // Octal numeric strings.
            'octal-single-digit-zero' => [
                'input'    => '00',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => true,
                    'float'   => false,
                ],
            ],
            'octal-single-digit' => [
                'input'    => '07',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => true,
                    'float'   => false,
                ],
            ],
            'octal-multi-digit' => [
                'input'    => '076543210',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => true,
                    'float'   => false,
                ],
            ],
            'octal-multi-digit-php-7.4' => [
                'input'    => '020_631_542',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => true,
                    'float'   => false,
                ],
            ],

            // Octal numeric strings using PHP 8.1 explicit octal notation.
            'explicit-octal-single-digit-zero' => [
                'input'    => '0o0',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => true,
                    'float'   => false,
                ],
            ],
            'explicit-octal-single-digit' => [
                'input'    => '0O7',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => true,
                    'float'   => false,
                ],
            ],
            'explicit-octal-multi-digit' => [
                'input'    => '0o76543210',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => true,
                    'float'   => false,
                ],
            ],
            'explicit-octal-multi-digit-php-7.4' => [
                'input'    => '0O20_631_542',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => true,
                    'float'   => false,
                ],
            ],

            // Floating point numeric strings. Also see: decimal numeric strings.
            'float-single-digit-dot-zero' => [
                'input'    => '0.',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-single-digit-dot' => [
                'input'    => '1.',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-multi-digit-dot' => [
                'input'    => '56458.',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-multi-digit-dot-leading-zero' => [
                'input'    => '0023.',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-multi-digit-dot-php-7.4' => [
                'input'    => '521_879.',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],

            'float-dot-single-digit-zero' => [
                'input'    => '.0',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-dot-single-digit' => [
                'input'    => '.2',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-dot-multi-digit' => [
                'input'    => '.232746',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-dot-multi-digit-trailing-zero' => [
                'input'    => '.345300',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-dot-multi-digit-php-7.4' => [
                'input'    => '.421_789_8',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],

            'float-digit-dot-digit-single-zero' => [
                'input'    => '0.0',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-digit-dot-digit-single' => [
                'input'    => '9.1',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-digit-dot-digit-multi' => [
                'input'    => '7483.2182',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-digit-dot-digit-multi-leading-zero' => [
                'input'    => '002781.21928173',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-digit-dot-digit-multi-trailing-zero' => [
                'input'    => '213.2987000',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-digit-dot-digit-multi-leading-zero-trailing-zero' => [
                'input'    => '07262.2760',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-digit-dot-digit-multi--php-7.4' => [
                'input'    => '07_262.276_720',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],

            'float-exponent-digit-dot-digit-zero-exp-single-digit' => [
                'input'    => '0.0e1',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-single-digit-dot-exp-double-digit' => [
                'input'    => '1.e28',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-multi-digit-dot-exp-plus-digit' => [
                'input'    => '56458.e+2',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-multi-digit-dot-leading-zero-exp-minus-digit' => [
                'input'    => '0023.e-44',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],

            'float-exponent-dot-single-digit-zero-exp-minus-digit' => [
                'input'    => '.0e-1',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-dot-single-digit-exp-plus-digit-zero' => [
                'input'    => '.2e+0',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-dot-multi-digit-exp-multi-digit' => [
                'input'    => '.232746e41',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-dot-multi-digit-trailing-zero-exp-multi-digit' => [
                'input'    => '.345300e87',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],

            'float-exponent-digit-dot-digit-single-zero-exp-uppercase' => [
                'input'    => '0.0E2',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-digit-dot-digit-single-exp-uppercase' => [
                'input'    => '9.1E47',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-digit-dot-digit-multi-exp-minus-digit' => [
                'input'    => '7483.2182e-3',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-digit-dot-digit-multi-leading-zero-exp-uppercase' => [
                'input'    => '002781.21928173E+56',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-digit-dot-digit-multi-trailing-zero-exp-plus-digit' => [
                'input'    => '213.2987000e+2',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-digit-dot-digit-multi-leading-zero-trailing-zero-exp-digit' => [
                'input'    => '07262.2760e4',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-digit-dot-digit-exp-digit-php-7.4' => [
                'input'    => '6.674_083e+1_1',
                'expected' => [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
        ];
    }
}
