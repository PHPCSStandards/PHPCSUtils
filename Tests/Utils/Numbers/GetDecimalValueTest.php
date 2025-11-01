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
 * Tests for the \PHPCSUtils\Utils\Numbers::getDecimalValue() method.
 *
 * @covers \PHPCSUtils\Utils\Numbers::getDecimalValue
 *
 * @since 1.0.0
 */
final class GetDecimalValueTest extends TestCase
{

    /**
     * Test determining the decimal value of an arbitrary numeric string.
     *
     * @dataProvider dataGetDecimalValue
     *
     * @param string $input    The input string.
     * @param string $expected The expected function output.
     *
     * @return void
     */
    public function testGetDecimalValue($input, $expected)
    {
        $this->assertSame($expected, Numbers::getDecimalValue($input));
    }

    /**
     * Data Provider.
     *
     * @see testGetDecimalValue() For the array format.
     *
     * @return array<string, array<string>>
     */
    public static function dataGetDecimalValue()
    {
        return [
            // Decimal integers.
            'single-digit-zero'                             => ['0', '0'],
            'single-digit-nine'                             => ['9', '9'],
            'multi-digit-decimal-int'                       => ['123', '123'],
            'multi-digit-decimal-php-7.4'                   => ['12_34_56', '123456'],

            // Floats.
            'multi-digit-decimal-float'                     => ['0.123', '0.123'],
            'multi-digit-float-scientific-uppercase-e'      => ['01E3', '01E3'],
            'multi-digit-float-scientific-lowercase-e'      => ['01e3', '01e3'],
            'multi-digit-decimal-float-php-7.4'             => ['0.123_456', '0.123456'],
            'multi-digit-scientific-float-with-underscores' => ['6.674_083e+11', '6.674083e+11'],

            // Hex.
            // phpcs:disable PHPCompatibility.Numbers.RemovedHexadecimalNumericStrings.Found
            'hex-int-no-numbers'                            => ['0xA', '10'],
            'hex-int-all-numbers'                           => ['0x400', '1024'],
            'hex-int-mixed-uppercase-x'                     => ['0XAB953C', '11244860'],
            'hex-int-mixed-php-7.4'                         => ['0xAB_95_3C', '11244860'],
            // phpcs:enable

            // Binary.
            'binary-int-10'                                 => ['0b1010', '10'],
            'binary-int-1024-uppercase-b'                   => ['0B10000000000', '1024'],
            'binary-int-1024-php-7.4'                       => ['0b100_000_000_00', '1024'],

            // Octal.
            'octal-int-10'                                  => ['012', '10'],
            'octal-int-1024'                                => ['02000', '1024'],
            'octal-int-1024-php-7.4'                        => ['020_00', '1024'],

            // Octal PHP 8.1 explicit notation.
            'explicit-octal-int-10'                         => ['0o12', '10'],
            'explicit-octal-int-1024'                       => ['0O2000', '1024'],
            'explicit-octal-int-1024-php-7.4'               => ['0o20_00', '1024'],
        ];
    }

    /**
     * Test that the method returns false when a non-string, a non-numeric string or an
     * invalid numeric format is received.
     *
     * @dataProvider dataGetDecimalValueInvalid
     *
     * @param mixed $input The input value.
     *
     * @return void
     */
    public function testGetDecimalValueInvalid($input)
    {
        $this->assertFalse(Numbers::getDecimalValue($input));
    }

    /**
     * Data Provider.
     *
     * @see testGetDecimalValueInvalid() For the array format.
     *
     * @return array<string, array<mixed>>
     */
    public static function dataGetDecimalValueInvalid()
    {
        return [
            'not-a-string-bool'                             => [true],
            'not-a-string-int'                              => [10],
            'not-a-string-float'                            => [1.23],
            'empty-string'                                  => [''],
            'string-not-a-number'                           => ['foobar'],
            'string-not-a-number-with-full-stop'            => ['foo? bar.'],

            // Invalid formats.
            'invalid-hex'                                   => ['0xZBHI28'],
            'invalid-binary'                                => ['0b121457182'],
            'invalid-octal'                                 => ['0289'],
            'invalid-octal-explicit-notation'               => ['0o289'],
        ];
    }
}
