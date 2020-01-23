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

use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Numbers;

/**
 * Tests for the \PHPCSUtils\Utils\Numbers::getCompleteNumber() method.
 *
 * @covers \PHPCSUtils\Utils\Numbers::getCompleteNumber
 *
 * @group numbers
 *
 * @since 1.0.0
 */
class GetCompleteNumberTest extends UtilityMethodTestCase
{

    /**
     * The PHPCS version being used to run the tests.
     *
     * @var string
     */
    public static $phpcsVersion = null;

    /**
     * Whether or not the tests are being run on PHP 7.4 or higher.
     *
     * @var bool
     */
    public static $php74OrHigher = false;

    /**
     * The PHPCS version being used to run the tests.
     *
     * @var string
     */
    public static $usableBackfill = false;

    /**
     * Initialize the static properties, if not done before.
     *
     * @return void
     */
    public static function setUpStaticProperties()
    {
        if (isset(self::$phpcsVersion)) {
            return;
        }

        self::$phpcsVersion   = Helper::getVersion();
        self::$php74OrHigher  = \version_compare(\PHP_VERSION_ID, '70399', '>');
        $maxUnsupported       = \max(\array_keys(Numbers::$unsupportedPHPCSVersions));
        self::$usableBackfill = \version_compare(self::$phpcsVersion, $maxUnsupported, '>');
    }

    /**
     * Test receiving an exception when a non-numeric token is passed to the method.
     *
     * @return void
     */
    public function testNotANumberException()
    {
        $this->expectPhpcsException('Token type "T_STRING" is not T_LNUMBER or T_DNUMBER');

        $stackPtr = $this->getTargetToken('/* testNotAnLNumber */', \T_STRING);
        Numbers::getCompleteNumber(self::$phpcsFile, $stackPtr);
    }

    /**
     * Test receiving an exception when PHPCS is run on PHP < 7.4 in combination with PHPCS 3.5.3.
     *
     * @return void
     */
    public function testUnsupportedPhpcsException()
    {
        self::setUpStaticProperties();
        if (isset(Numbers::$unsupportedPHPCSVersions[self::$phpcsVersion]) === false) {
            $this->markTestSkipped('Test specific to a limited set of PHPCS versions');
        }

        $this->expectPhpcsException(
            'The PHPCSUtils\Utils\Numbers::getCompleteNumber() method does not support PHPCS '
        );

        $stackPtr = $this->getTargetToken('/* testPHP74IntDecimalMultiUnderscore */', \T_LNUMBER);
        Numbers::getCompleteNumber(self::$phpcsFile, $stackPtr);
    }

    /**
     * Test correctly identifying all tokens belonging to a numeric literal.
     *
     * @dataProvider dataGetCompleteNumber
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   Expected function return value.
     *
     * @return void
     */
    public function testGetCompleteNumber($testMarker, $expected)
    {
        // Skip the test(s) on unsupported PHPCS versions.
        self::setUpStaticProperties();
        if (isset(Numbers::$unsupportedPHPCSVersions[self::$phpcsVersion]) === true) {
            $this->markTestSkipped(
                'PHPCS ' . self::$phpcsVersion . ' is not supported due to buggy numeric string literal backfill.'
            );
        }

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
     * @return array
     */
    public function dataGetCompleteNumber()
    {
        self::setUpStaticProperties();
        $multiToken = true;
        if (self::$php74OrHigher === true || self::$usableBackfill === true) {
            $multiToken = false;
        }

        /*
         * Disabling the hexnumeric string detection for the rest of the file.
         * These are only strings within the context of PHPCS and need to be tested as such.
         *
         * @phpcs:disable PHPCompatibility.Miscellaneous.ValidIntegers.HexNumericStringFound
         */

        return [
            // Ordinary numbers.
            'normal-integer-decimal' => [
                '/* testIntDecimal */',
                [
                    'orig_content' => '1000000000',
                    'content'      => '1000000000',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '1000000000',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'normal-float' => [
                '/* testFloat */',
                [
                    'orig_content' => '107925284.88',
                    'content'      => '107925284.88',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '107925284.88',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'normal-float-negative' => [
                '/* testFloatNegative */',
                [
                    'orig_content' => '58987.789',
                    'content'      => '58987.789',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '58987.789',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'normal-integer-binary' => [
                '/* testIntBinary */',
                [
                    'orig_content' => '0b1',
                    'content'      => '0b1',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '1',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'normal-integer-hex' => [
                '/* testIntHex */',
                [
                    'orig_content' => '0xA',
                    'content'      => '0xA',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '10',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'normal-integer-octal' => [
                '/* testIntOctal */',
                [
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
                '/* testParseError */',
                [
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
                '/* testPHP74IntDecimalMultiUnderscore */',
                [
                    'orig_content' => '1_000_000_000',
                    'content'      => '1000000000',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '1000000000',
                    'last_token'   => $multiToken ? 1 : 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-float' => [
                '/* testPHP74Float */',
                [
                    'orig_content' => '107_925_284.88',
                    'content'      => '107925284.88',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '107925284.88',
                    'last_token'   => $multiToken ? 2 : 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-integer-decimal-single-underscore' => [
                '/* testPHP74IntDecimalSingleUnderscore */',
                [
                    'orig_content' => '135_00',
                    'content'      => '13500',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '13500',
                    'last_token'   => $multiToken ? 1 : 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-float-exponent-negative' => [
                '/* testPHP74FloatExponentNegative */',
                [
                    'orig_content' => '6.674_083e-11',
                    'content'      => '6.674083e-11',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '6.674083e-11',
                    'last_token'   => $multiToken ? 3 : 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-float-exponent-positive' => [
                '/* testPHP74FloatExponentPositive */',
                [
                    'orig_content' => '6.674_083e+11',
                    'content'      => '6.674083e+11',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '6.674083e+11',
                    'last_token'   => $multiToken ? 3 : 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-integer-decimal-multi-underscore-2' => [
                '/* testPHP74IntDecimalMultiUnderscore2 */',
                [
                    'orig_content' => '299_792_458',
                    'content'      => '299792458',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '299792458',
                    'last_token'   => $multiToken ? 1 : 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-integer-hex' => [
                '/* testPHP74IntHex */',
                [
                    'orig_content' => '0xCAFE_F00D',
                    'content'      => '0xCAFEF00D',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '3405705229',
                    'last_token'   => $multiToken ? 1 : 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-integer-binary' => [
                '/* testPHP74IntBinary */',
                [
                    'orig_content' => '0b0101_1111',
                    'content'      => '0b01011111',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '95',
                    'last_token'   => $multiToken ? 1 : 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-integer-octal' => [
                '/* testPHP74IntOctal */',
                [
                    'orig_content' => '0137_041',
                    'content'      => '0137041',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '48673',
                    'last_token'   => $multiToken ? 1 : 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-float-exponent-multi-underscore' => [
                '/* testPHP74FloatExponentMultiUnderscore */',
                [
                    'orig_content' => '1_2.3_4e1_23',
                    'content'      => '12.34e123',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '12.34e123',
                    'last_token'   => $multiToken ? 3 : 0, // Offset from $stackPtr.
                ],
            ],

            // Make sure the backfill doesn't do more than it should.
            'php-7.4-integer-calculation-1' => [
                '/* testPHP74IntCalc1 */',
                [
                    'orig_content' => '667_083',
                    'content'      => '667083',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '667083',
                    'last_token'   => $multiToken ? 1 : 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-integer-calculation-2' => [
                '/* testPHP74IntCalc2 */',
                [
                    'orig_content' => '74_083',
                    'content'      => '74083',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '74083',
                    'last_token'   => $multiToken ? 1 : 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-float-calculation-1' => [
                '/* testPHP74FloatCalc1 */',
                [
                    'orig_content' => '6.674_08e3',
                    'content'      => '6.67408e3',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '6.67408e3',
                    'last_token'   => $multiToken ? 1 : 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-float-calculation-2' => [
                '/* testPHP74FloatCalc2 */',
                [
                    'orig_content' => '6.674_08e3',
                    'content'      => '6.67408e3',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '6.67408e3',
                    'last_token'   => $multiToken ? 1 : 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-integer-whitespace' => [
                '/* testPHP74IntWhitespace */',
                [
                    'orig_content' => '107_925_284',
                    'content'      => '107925284',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '107925284',
                    'last_token'   => $multiToken ? 1 : 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-float-comments' => [
                '/* testPHP74FloatComments */',
                [
                    'orig_content' => '107_925_284',
                    'content'      => '107925284',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '107925284',
                    'last_token'   => $multiToken ? 1 : 0, // Offset from $stackPtr.
                ],
            ],

            // Invalid numeric literal with underscore.
            'php-7.4-invalid-1' => [
                '/* testPHP74Invalid1 */',
                [
                    'orig_content' => '100',
                    'content'      => '100',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '100',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-invalid-2' => [
                '/* testPHP74Invalid2 */',
                [
                    'orig_content' => '1',
                    'content'      => '1',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '1',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-invalid-3' => [
                '/* testPHP74Invalid3 */',
                [
                    'orig_content' => '1',
                    'content'      => '1',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '1',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-invalid-4' => [
                '/* testPHP74Invalid4 */',
                [
                    'orig_content' => '1.',
                    'content'      => '1.',
                    'code'         => \T_DNUMBER,
                    'type'         => 'T_DNUMBER',
                    'decimal'      => '1.',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-invalid-5' => [
                '/* testPHP74Invalid5 */',
                [
                    'orig_content' => '0',
                    'content'      => '0',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '0',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-invalid-6' => [
                '/* testPHP74Invalid6 */',
                [
                    'orig_content' => '0',
                    'content'      => '0',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '0',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-invalid-7' => [
                '/* testPHP74Invalid7 */',
                [
                    'orig_content' => '1',
                    'content'      => '1',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '1',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'php-7.4-invalid-8' => [
                '/* testPHP74Invalid8 */',
                [
                    'orig_content' => '1',
                    'content'      => '1',
                    'code'         => \T_LNUMBER,
                    'type'         => 'T_LNUMBER',
                    'decimal'      => '1',
                    'last_token'   => 0, // Offset from $stackPtr.
                ],
            ],
            'live-coding' => [
                '/* testLiveCoding */',
                [
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
