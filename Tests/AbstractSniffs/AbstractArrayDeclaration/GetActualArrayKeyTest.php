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
 * @group abstracts
 *
 * @since 1.0.0
 */
class GetActualArrayKeyTest extends UtilityMethodTestCase
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
                    'Failed: actual key ' . $result . ' is not the same as the expected key ' . $expected
                        . ' for item number ' . $itemNr
                );
            }
        }
    }

    /**
     * Data provider.
     *
     * @see testGetActualArrayKey() For the array format.
     *
     * @return array
     */
    public function dataGetActualArrayKey()
    {
        return [
            'unsupported-key-types' => [
                '/* testAllVoid */',
                null,
                0,
            ],
            'keys-all-empty-string' => [
                '/* testAllEmptyString */',
                '',
                0,
            ],
            'keys-all-integer-zero' => [
                '/* testAllZero */',
                0,
                0,
            ],
            'keys-all-integer-one' => [
                '/* testAllOne */',
                1,
                1,
            ],
            'keys-all-integer-eleven' => [
                '/* testAllEleven */',
                11,
                0,
            ],
            'keys-all-string-abc' => [
                '/* testAllStringAbc */',
                'abc',
                0,
            ],
        ];
    }
}
