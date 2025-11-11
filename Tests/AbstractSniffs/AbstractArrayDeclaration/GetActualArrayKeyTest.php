<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\AbstractSniffs\AbstractArrayDeclaration;

use PHPCSUtils\Tests\AbstractSniffs\AbstractArrayDeclaration\ArrayDeclarationSniffTestDouble;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Arrays;
use PHPCSUtils\Utils\PassedParameters;

/**
 * Tests for the \PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff::getActualArrayKey() method.
 *
 * @covers \PHPCSUtils\AbstractSniffs\AbstractArrayDeclarationSniff::getActualArrayKey
 *
 * @since 1.0.0
 */
final class GetActualArrayKeyTest extends UtilityMethodTestCase
{

    /**
     * Test retrieving the actual array key.
     *
     * @dataProvider dataGetActualArrayKey
     *
     * @param string     $testMarker   The comment which prefaces the target token in the test file.
     * @param int|string $expected     The expected key value for (nearly) all keys in this array.
     * @param int        $expectedFrom The 1-based array index from which all keys are expected to be the same.
     *
     * @return void
     */
    public function testGetActualArrayKey($testMarker, $expected, $expectedFrom)
    {
        $testObj         = new ArrayDeclarationSniffTestDouble();
        $testObj->tokens = self::$phpcsFile->getTokens();

        $stackPtr   = $this->getTargetToken($testMarker, [\T_ARRAY, \T_OPEN_SHORT_ARRAY]);
        $arrayItems = PassedParameters::getParameters(self::$phpcsFile, $stackPtr);

        foreach ($arrayItems as $itemNr => $arrayItem) {
            if ($itemNr < $expectedFrom) {
                continue;
            }

            $arrowPtr = Arrays::getDoubleArrowPtr(self::$phpcsFile, $arrayItem['start'], $arrayItem['end']);
            if ($arrowPtr !== false) {
                $result = $testObj->getActualArrayKey(self::$phpcsFile, $arrayItem['start'], ($arrowPtr - 1));
                $this->assertSame(
                    $expected,
                    $result,
                    'Failed: actual key ' . \var_export($result, true) . ' is not the same as the expected key '
                        . \var_export($expected, true) . ' for item number ' . $itemNr
                );
            }
        }
    }

    /**
     * Data provider.
     *
     * @see testGetActualArrayKey() For the array format.
     *
     * @return array<string, array<string, int|string|null>>
     */
    public static function dataGetActualArrayKey()
    {
        return [
            'unsupported-key-types' => [
                'testMarker'   => '/* testAllVoid */',
                'expected'     => null,
                'expectedFrom' => 0,
            ],
            'keys-all-empty-string' => [
                'testMarker'   => '/* testAllEmptyString */',
                'expected'     => '',
                'expectedFrom' => 0,
            ],
            'keys-all-integer-zero' => [
                'testMarker'   => '/* testAllZero */',
                'expected'     => 0,
                'expectedFrom' => 0,
            ],
            'keys-all-integer-one' => [
                'testMarker'   => '/* testAllOne */',
                'expected'     => 1,
                'expectedFrom' => 1,
            ],
            'keys-all-integer-eleven' => [
                'testMarker'   => '/* testAllEleven */',
                'expected'     => 11,
                'expectedFrom' => 0,
            ],
            'keys-all-string-abc' => [
                'testMarker'   => '/* testAllStringAbc */',
                'expected'     => 'abc',
                'expectedFrom' => 0,
            ],
        ];
    }

    /**
     * Test retrieving the actual array key from a heredoc when the key could contain interpolation, but doesn't,
     * as the interpolation is escaped.
     *
     * @return void
     */
    public function testGetActualArrayKeyFromHeredocWithEscapedVarInKey()
    {
        $testObj         = new ArrayDeclarationSniffTestDouble();
        $testObj->tokens = self::$phpcsFile->getTokens();

        $stackPtr   = $this->getTargetToken('/* testHeredocWithEscapedVarInKey */', [\T_ARRAY, \T_OPEN_SHORT_ARRAY]);
        $arrayItems = PassedParameters::getParameters(self::$phpcsFile, $stackPtr);

        $expected = [
            1 => 'a{$b}c',
            2 => 'a$bc',
            3 => '$\{abc}',
        ];

        $this->assertCount(\count($expected), $arrayItems);

        foreach ($arrayItems as $itemNr => $arrayItem) {
            $arrowPtr = Arrays::getDoubleArrowPtr(self::$phpcsFile, $arrayItem['start'], $arrayItem['end']);
            $result   = $testObj->getActualArrayKey(self::$phpcsFile, $arrayItem['start'], ($arrowPtr - 1));
            $this->assertSame(
                $expected[$itemNr],
                $result,
                'Failed: actual key ' . \var_export($result, true) . ' is not the same as the expected key '
                    . \var_export($expected[$itemNr], true) . ' for item number ' . $itemNr
            );
        }
    }

    /**
     * Test retrieving the actual array key when string keys look like numeric literals with underscores.
     *
     * @return void
     */
    public function testGetActualArrayKeyForStringLiteralsWithNumbers()
    {
        $testObj         = new ArrayDeclarationSniffTestDouble();
        $testObj->tokens = self::$phpcsFile->getTokens();

        $stackPtr   = $this->getTargetToken('/* testStringLiteralsWithNumbers */', [\T_ARRAY, \T_OPEN_SHORT_ARRAY]);
        $arrayItems = PassedParameters::getParameters(self::$phpcsFile, $stackPtr);

        $expected = [
            1 => '_1',
            2 => '_2',
            3 => '3_',
            4 => '4_',
            5 => '_5_',
            6 => '_6_',
        ];

        $this->assertCount(\count($expected), $arrayItems);

        foreach ($arrayItems as $itemNr => $arrayItem) {
            $arrowPtr = Arrays::getDoubleArrowPtr(self::$phpcsFile, $arrayItem['start'], $arrayItem['end']);
            $result   = $testObj->getActualArrayKey(self::$phpcsFile, $arrayItem['start'], ($arrowPtr - 1));
            $this->assertSame(
                $expected[$itemNr],
                $result,
                'Failed: actual key ' . \var_export($result, true) . ' is not the same as the expected key '
                    . \var_export($expected[$itemNr], true) . ' for item number ' . $itemNr
            );
        }

        // Verify against handling by PHP itself.
        $expectedKeys = \array_values($expected);
        $actualKeys   = \array_keys(\array_combine($expected, $expected));
        $this->assertSame($expectedKeys, $actualKeys, 'getActualArrayKey() results do not match PHP native handling');
    }

    /**
     * Test retrieving the actual array key when string keys look like numeric keys but start with 0.
     *
     * @return void
     */
    public function testGetActualArrayKeyForZeroPrefixedNumericStringKeys()
    {
        $testObj         = new ArrayDeclarationSniffTestDouble();
        $testObj->tokens = self::$phpcsFile->getTokens();

        $stackPtr   = $this->getTargetToken('/* testZeroPrefixedNumericStringKeys */', [\T_ARRAY, \T_OPEN_SHORT_ARRAY]);
        $arrayItems = PassedParameters::getParameters(self::$phpcsFile, $stackPtr);

        $expected = [
            1 => '01',
            2 => '002',
            3 => '0o3',
            4 => '0b1',
            5 => '0x7', // phpcs:ignore PHPCompatibility.Numbers.RemovedHexadecimalNumericStrings.Found
            6 => '0.0',
        ];

        $this->assertCount(\count($expected), $arrayItems);

        foreach ($arrayItems as $itemNr => $arrayItem) {
            $arrowPtr = Arrays::getDoubleArrowPtr(self::$phpcsFile, $arrayItem['start'], $arrayItem['end']);
            $result   = $testObj->getActualArrayKey(self::$phpcsFile, $arrayItem['start'], ($arrowPtr - 1));
            $this->assertSame(
                $expected[$itemNr],
                $result,
                'Failed: actual key ' . \var_export($result, true) . ' is not the same as the expected key '
                    . \var_export($expected[$itemNr], true) . ' for item number ' . $itemNr
            );
        }

        // Verify against handling by PHP itself.
        $expectedKeys = \array_values($expected);
        $actualKeys   = \array_keys(\array_combine($expected, $expected));
        $this->assertSame($expectedKeys, $actualKeys, 'getActualArrayKey() results do not match PHP native handling');
    }
}
