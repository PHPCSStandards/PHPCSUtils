<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
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
 * @group numbers
 *
 * @since 1.0.0
 */
class NumberTypesTest extends TestCase
{

    /**
     * Test correctly recognizing an arbitrary string representing a decimal integer.
     *
     * @dataProvider dataNumbers
     * @covers       \PHPCSUtils\Utils\Numbers::isDecimalInt
     *
     * @param string $input    The input string.
     * @param string $expected The expected output for the various functions.
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
     * @covers       \PHPCSUtils\Utils\Numbers::isHexidecimalInt
     *
     * @param string $input    The input string.
     * @param string $expected The expected output for the various functions.
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
     * @covers       \PHPCSUtils\Utils\Numbers::isBinaryInt
     *
     * @param string $input    The input string.
     * @param string $expected The expected output for the various functions.
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
     * @covers       \PHPCSUtils\Utils\Numbers::isOctalInt
     *
     * @param string $input    The input string.
     * @param string $expected The expected output for the various functions.
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
     * @covers       \PHPCSUtils\Utils\Numbers::isFloat
     *
     * @param string $input    The input string.
     * @param string $expected The expected output for the various functions.
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
     * @return array
     */
    public static function dataNumbers()
    {
        return [
            // Not strings.
            'not-a-string-bool' => [
                true,
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'not-a-string-int' => [
                10,
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],

            // Not numeric strings.
            'empty-string' => [
                '',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'string-not-a-number' => [
                'foobar',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'string-not-a-number-with-full-stop' => [
                'foo. bar',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-hex' => [
                '0xZBHI28',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-binary' => [
                '0b121457182',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-octal' => [
                // Note: in PHP 5.x this would still be accepted, though not interpreted correctly.
                '0289',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-two-decimal-points' => [
                '1.287.2763',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-plus-no-exponent' => [
                '1.287+2763',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-minus-no-exponent' => [
                '1287-2763',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-exponent-no-multiplier' => [
                '2872e',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-exponent-plus-no-multiplier' => [
                '1.2872e+',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-exponent-minus-no-multiplier' => [
                '1.2872e-',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-exponent-multiplier-float' => [
                '376e2.3',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-exponent-plus-multiplier-float' => [
                '3.76e+2.3',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-exponent-minus-multiplier-float' => [
                '37.6e-2.3',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-exponent-plus-minus-multiplier' => [
                '37.6e+-2',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'invalid-float-double-exponent' => [
                '37.6e2e6',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],

            // Decimal numeric strings.
            'decimal-single-digit-zero' => [
                '0',
                [
                    'decimal' => true,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'decimal-single-digit' => [
                '9',
                [
                    'decimal' => true,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'decimal-multi-digit' => [
                '123456',
                [
                    'decimal' => true,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'decimal-multi-digit-php-7.4' => [
                '12_34_56',
                [
                    'decimal' => true,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],

            // Hexidecimal numeric strings.
            // phpcs:disable PHPCompatibility.Miscellaneous.ValidIntegers.HexNumericStringFound
            'hexidecimal-single-digit-zero' => [
                '0x0',
                [
                    'decimal' => false,
                    'hex'     => true,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'hexidecimal-single-digit' => [
                '0xA',
                [
                    'decimal' => false,
                    'hex'     => true,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'hexidecimal-multi-digit-all-numbers' => [
                '0x123456',
                [
                    'decimal' => false,
                    'hex'     => true,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'hexidecimal-multi-digit-no-numbers' => [
                '0xABCDEF',
                [
                    'decimal' => false,
                    'hex'     => true,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'hexidecimal-multi-digit-mixed' => [
                '0xAB02F6',
                [
                    'decimal' => false,
                    'hex'     => true,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'hexidecimal-multi-digit-mixed-uppercase-x' => [
                '0XAB953C',
                [
                    'decimal' => false,
                    'hex'     => true,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'hexidecimal-multi-digit-php-7.4' => [
                '0x23_6A_3C',
                [
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
                '0b0',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => true,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'binary-single-digit-one' => [
                '0b1',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => true,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'binary-multi-digit' => [
                '0b1010',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => true,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'binary-multi-digit-uppercase-b' => [
                '0B1000100100000',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => true,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],
            'binary-multi-digit-php-7.4' => [
                '0b100_000_000_00',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => true,
                    'octal'   => false,
                    'float'   => false,
                ],
            ],

            // Octal numeric strings.
            'octal-single-digit-zero' => [
                '00',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => true,
                    'float'   => false,
                ],
            ],
            'octal-single-digit' => [
                '07',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => true,
                    'float'   => false,
                ],
            ],
            'octal-multi-digit' => [
                '076543210',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => true,
                    'float'   => false,
                ],
            ],
            'octal-multi-digit-php-7.4' => [
                '020_631_542',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => true,
                    'float'   => false,
                ],
            ],

            // Floating point numeric strings. Also see: decimal numeric strings.
            'float-single-digit-dot-zero' => [
                '0.',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-single-digit-dot' => [
                '1.',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-multi-digit-dot' => [
                '56458.',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-multi-digit-dot-leading-zero' => [
                '0023.',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-multi-digit-dot-php-7.4' => [
                '521_879.',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],

            'float-dot-single-digit-zero' => [
                '.0',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-dot-single-digit' => [
                '.2',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-dot-multi-digit' => [
                '.232746',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-dot-multi-digit-trailing-zero' => [
                '.345300',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-dot-multi-digit-php-7.4' => [
                '.421_789_8',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],

            'float-digit-dot-digit-single-zero' => [
                '0.0',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-digit-dot-digit-single' => [
                '9.1',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-digit-dot-digit-multi' => [
                '7483.2182',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-digit-dot-digit-multi-leading-zero' => [
                '002781.21928173',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-digit-dot-digit-multi-trailing-zero' => [
                '213.2987000',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-digit-dot-digit-multi-leading-zero-trailing-zero' => [
                '07262.2760',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-digit-dot-digit-multi--php-7.4' => [
                '07_262.276_720',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],

            'float-exponent-digit-dot-digit-zero-exp-single-digit' => [
                '0.0e1',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-single-digit-dot-exp-double-digit' => [
                '1.e28',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-multi-digit-dot-exp-plus-digit' => [
                '56458.e+2',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-multi-digit-dot-leading-zero-exp-minus-digit' => [
                '0023.e-44',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],

            'float-exponent-dot-single-digit-zero-exp-minus-digit' => [
                '.0e-1',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-dot-single-digit-exp-plus-digit-zero' => [
                '.2e+0',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-dot-multi-digit-exp-multi-digit' => [
                '.232746e41',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-dot-multi-digit-trailing-zero-exp-multi-digit' => [
                '.345300e87',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],

            'float-exponent-digit-dot-digit-single-zero-exp-uppercase' => [
                '0.0E2',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-digit-dot-digit-single-exp-uppercase' => [
                '9.1E47',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-digit-dot-digit-multi-exp-minus-digit' => [
                '7483.2182e-3',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-digit-dot-digit-multi-leading-zero-exp-uppercase' => [
                '002781.21928173E+56',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-digit-dot-digit-multi-trailing-zero-exp-plus-digit' => [
                '213.2987000e+2',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-digit-dot-digit-multi-leading-zero-trailing-zero-exp-digit' => [
                '07262.2760e4',
                [
                    'decimal' => false,
                    'hex'     => false,
                    'binary'  => false,
                    'octal'   => false,
                    'float'   => true,
                ],
            ],
            'float-exponent-digit-dot-digit-exp-digit-php-7.4' => [
                '6.674_083e+1_1',
                [
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
