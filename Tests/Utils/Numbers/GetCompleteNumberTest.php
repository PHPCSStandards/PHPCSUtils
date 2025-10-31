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

use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\Numbers;

/**
 * Tests for the \PHPCSUtils\Utils\Numbers::getCompleteNumber() method.
 *
 * @covers \PHPCSUtils\Utils\Numbers::getCompleteNumber
 *
 * @since 1.0.0
 */
final class GetCompleteNumberTest extends PolyfilledTestCase
{

    /**
     * Test receiving an exception when a non-integer stackPtr is passed to the method.
     *
     * @return void
     */
    public function testNonIntegerTokenException()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type integer, double given');

        Numbers::getCompleteNumber(self::$phpcsFile, 1.5);
    }

    /**
     * Test receiving an exception when a non-existent token is passed to the method.
     *
     * @return void
     */
    public function testNonExistentTokenException()
    {
        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object, 100000 given'
        );

        Numbers::getCompleteNumber(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an exception when a non-numeric token is passed to the method.
     *
     * @return void
     */
    public function testNotANumberException()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type T_LNUMBER or T_DNUMBER;');

        $stackPtr = $this->getTargetToken('/* testNotAnLNumber */', \T_STRING);
        Numbers::getCompleteNumber(self::$phpcsFile, $stackPtr);
    }

    /**
     * Test correctly identifying all tokens belonging to a numeric literal.
     *
     * @dataProvider dataGetCompleteNumber
     *
     * @param string                    $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, int|string> $expected   Expected function return value.
     *
     * @return void
     */
    public function testGetCompleteNumber($testMarker, $expected)
    {
        $stackPtr                = $this->getTargetToken($testMarker, [\T_LNUMBER, \T_DNUMBER]);
        $expected['last_token'] += $stackPtr;

        // Allow for 32 vs 64-bit systems with different maximum integer size.
        if ($expected['code'] === \T_LNUMBER && ($expected['decimal'] + 0) > \PHP_INT_MAX) {
            $expected['code'] = \T_DNUMBER;
            $expected['type'] = 'T_DNUMBER';
        }

        $result = Numbers::getCompleteNumber(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data Provider.
     *
     * @see testGetCompleteNumber() For the array format.
     *
     * @return array<string, array<string, string|array<string, int|string>>>
     */
    public static function dataGetCompleteNumber()
    {
        /*
         * Disabling the hexnumeric string detection for the rest of the file.
         * These are only strings within the context of PHPCS and need to be tested as such.
         *
         * @phpcs:disable PHPCompatibility.Numbers.RemovedHexadecimalNumericStrings.Found
         */

        return [
            // Ordinary numbers.
            'normal-integer-decimal' => [
                'testMarker' => '/* testIntDecimal */',
                'expected'   => [
                    'orig_content' => '1000000000',
                    'content'      => '1000000000',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '1000000000',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'normal-integer-larger-than-intmax-is-float' => [
                'testMarker' => '/* testIntLargerThanIntMaxIsFloat */',
                'expected'   => [
                    'orig_content' => '10223372036854775810',
                    'content'      => '10223372036854775810',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '10223372036854775810',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'normal-float' => [
                'testMarker' => '/* testFloat */',
                'expected'   => [
                    'orig_content' => '107925284.88',
                    'content'      => '107925284.88',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '107925284.88',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'normal-float-negative' => [
                'testMarker' => '/* testFloatNegative */',
                'expected'   => [
                    'orig_content' => '58987.789',
                    'content'      => '58987.789',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '58987.789',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'normal-integer-binary' => [
                'testMarker' => '/* testIntBinary */',
                'expected'   => [
                    'orig_content' => '0b1',
                    'content'      => '0b1',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '1',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'normal-integer-hex' => [
                'testMarker' => '/* testIntHex */',
                'expected'   => [
                    'orig_content' => '0xA',
                    'content'      => '0xA',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '10',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'normal-integer-octal' => [
                'testMarker' => '/* testIntOctal */',
                'expected'   => [
                    'orig_content' => '052',
                    'content'      => '052',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '42',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],

            // Parse error.
            'parse-error' => [
                'testMarker' => '/* testParseError */',
                'expected'   => [
                    'orig_content' => '100',
                    'content'      => '100',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '100',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],

            // Numeric literal with underscore.
            'php-7.4-integer-decimal-multi-underscore' => [
                'testMarker' => '/* testPHP74IntDecimalMultiUnderscore */',
                'expected'   => [
                    'orig_content' => '1_000_000_000',
                    'content'      => '1000000000',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '1000000000',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-integer-larger-than-intmax-is-float' => [
                'testMarker' => '/* testPHP74IntLargerThanIntMaxIsFloat */',
                'expected'   => [
                    'orig_content' => '10_223_372_036_854_775_810',
                    'content'      => '10223372036854775810',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '10223372036854775810',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-float' => [
                'testMarker' => '/* testPHP74Float */',
                'expected'   => [
                    'orig_content' => '107_925_284.88',
                    'content'      => '107925284.88',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '107925284.88',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-integer-decimal-single-underscore' => [
                'testMarker' => '/* testPHP74IntDecimalSingleUnderscore */',
                'expected'   => [
                    'orig_content' => '135_00',
                    'content'      => '13500',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '13500',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-float-exponent-negative' => [
                'testMarker' => '/* testPHP74FloatExponentNegative */',
                'expected'   => [
                    'orig_content' => '6.674_083e-11',
                    'content'      => '6.674083e-11',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '6.674083e-11',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-float-exponent-positive' => [
                'testMarker' => '/* testPHP74FloatExponentPositive */',
                'expected'   => [
                    'orig_content' => '6.674_083e+11',
                    'content'      => '6.674083e+11',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '6.674083e+11',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-integer-decimal-multi-underscore-2' => [
                'testMarker' => '/* testPHP74IntDecimalMultiUnderscore2 */',
                'expected'   => [
                    'orig_content' => '299_792_458',
                    'content'      => '299792458',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '299792458',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-integer-hex' => [
                'testMarker' => '/* testPHP74IntHex */',
                'expected'   => [
                    'orig_content' => '0xCAFE_F00D',
                    'content'      => '0xCAFEF00D',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '3405705229',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-integer-binary' => [
                'testMarker' => '/* testPHP74IntBinary */',
                'expected'   => [
                    'orig_content' => '0b0101_1111',
                    'content'      => '0b01011111',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '95',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-integer-octal' => [
                'testMarker' => '/* testPHP74IntOctal */',
                'expected'   => [
                    'orig_content' => '0137_041',
                    'content'      => '0137041',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '48673',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-float-exponent-multi-underscore' => [
                'testMarker' => '/* testPHP74FloatExponentMultiUnderscore */',
                'expected'   => [
                    'orig_content' => '1_2.3_4e1_23',
                    'content'      => '12.34e123',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '12.34e123',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],

            // Make sure the backfill doesn't do more than it should.
            'php-7.4-integer-calculation-1' => [
                'testMarker' => '/* testPHP74IntCalc1 */',
                'expected'   => [
                    'orig_content' => '667_083',
                    'content'      => '667083',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '667083',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-integer-calculation-2' => [
                'testMarker' => '/* testPHP74IntCalc2 */',
                'expected'   => [
                    'orig_content' => '74_083',
                    'content'      => '74083',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '74083',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-float-calculation-1' => [
                'testMarker' => '/* testPHP74FloatCalc1 */',
                'expected'   => [
                    'orig_content' => '6.674_08e3',
                    'content'      => '6.67408e3',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '6.67408e3',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-float-calculation-2' => [
                'testMarker' => '/* testPHP74FloatCalc2 */',
                'expected'   => [
                    'orig_content' => '6.674_08e3',
                    'content'      => '6.67408e3',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '6.67408e3',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-integer-whitespace' => [
                'testMarker' => '/* testPHP74IntWhitespace */',
                'expected'   => [
                    'orig_content' => '107_925_284',
                    'content'      => '107925284',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '107925284',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-float-comments' => [
                'testMarker' => '/* testPHP74FloatComments */',
                'expected'   => [
                    'orig_content' => '107_925_284',
                    'content'      => '107925284',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '107925284',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],

            // Invalid numeric literal with underscore.
            'php-7.4-invalid-1' => [
                'testMarker' => '/* testPHP74Invalid1 */',
                'expected'   => [
                    'orig_content' => '100',
                    'content'      => '100',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '100',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-invalid-2' => [
                'testMarker' => '/* testPHP74Invalid2 */',
                'expected'   => [
                    'orig_content' => '1',
                    'content'      => '1',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '1',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-invalid-3' => [
                'testMarker' => '/* testPHP74Invalid3 */',
                'expected'   => [
                    'orig_content' => '1',
                    'content'      => '1',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '1',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-invalid-4' => [
                'testMarker' => '/* testPHP74Invalid4 */',
                'expected'   => [
                    'orig_content' => '1.',
                    'content'      => '1.',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '1.',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-invalid-5' => [
                'testMarker' => '/* testPHP74Invalid5 */',
                'expected'   => [
                    'orig_content' => '0',
                    'content'      => '0',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '0',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-invalid-6' => [
                'testMarker' => '/* testPHP74Invalid6 */',
                'expected'   => [
                    'orig_content' => '0',
                    'content'      => '0',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '0',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-invalid-7' => [
                'testMarker' => '/* testPHP74Invalid7 */',
                'expected'   => [
                    'orig_content' => '1',
                    'content'      => '1',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '1',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-invalid-8' => [
                'testMarker' => '/* testPHP74Invalid8 */',
                'expected'   => [
                    'orig_content' => '1',
                    'content'      => '1',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '1',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],

            // PHP 8.1 explicit octal notation.
            'php-8.1-explicit-octal-lowercase' => [
                'testMarker' => '/* testPHP81ExplicitOctal */',
                'expected'   => [
                    'orig_content' => '0o137041',
                    'content'      => '0o137041',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '48673',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-8.1-explicit-octal-uppercase' => [
                'testMarker' => '/* testPHP81ExplicitOctalUppercase */',
                'expected'   => [
                    'orig_content' => '0O137041',
                    'content'      => '0O137041',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '48673',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-8.1-explicit-octal-with-separator' => [
                'testMarker' => '/* testPHP81ExplicitOctalWithSeparator */',
                'expected'   => [
                    'orig_content' => '0o137_041',
                    'content'      => '0o137041',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '48673',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-8.1-invalid-octal-1' => [
                'testMarker' => '/* testPHP81InvalidExplicitOctal1 */',
                'expected'   => [
                    'orig_content' => '0',
                    'content'      => '0',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '0',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-8.1-invalid-octal-2' => [
                'testMarker' => '/* testPHP81InvalidExplicitOctal2 */',
                'expected'   => [
                    'orig_content' => '0O2',
                    'content'      => '0O2',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '2',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-8.1-invalid-octal-3' => [
                'testMarker' => '/* testPHP81InvalidExplicitOctal3 */',
                'expected'   => [
                    'orig_content' => '0o2',
                    'content'      => '0o2',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '2',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-8.1-invalid-octal-4' => [
                'testMarker' => '/* testPHP81InvalidExplicitOctal4 */',
                'expected'   => [
                    'orig_content' => '0o2',
                    'content'      => '0o2',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '2',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-8.1-invalid-explicit-octal' => [
                'testMarker' => '/* testPHP74PHP81InvalidExplicitOctal */',
                'expected'   => [
                    'orig_content' => '0',
                    'content'      => '0',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '0',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],

            'live-coding' => [
                'testMarker' => '/* testLiveCoding */',
                'expected'   => [
                    'orig_content' => '100',
                    'content'      => '100',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '100',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
        ];
    }
}
