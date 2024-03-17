<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Operators;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Operators;

/**
 * Test case for the \PHPCSUtils\Utils\Operators::isUnaryPlusMinus() method.
 *
 * @since 1.0.0
 */
abstract class IsUnaryPlusMinusTestCase extends UtilityMethodTestCase
{

    /**
     * Test that false is returned when a non-existent token is passed.
     *
     * @covers \PHPCSUtils\Utils\Operators::isUnaryPlusMinus
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(Operators::isUnaryPlusMinus(self::$phpcsFile, 10000));
    }

    /**
     * Test that false is returned when a non-plus/minus token is passed.
     *
     * @covers \PHPCSUtils\Utils\Operators::isUnaryPlusMinus
     *
     * @return void
     */
    public function testNotPlusMinusToken()
    {
        $target = $this->getTargetToken('/* testNonUnaryPlus */', \T_LNUMBER);
        $this->assertFalse(Operators::isUnaryPlusMinus(self::$phpcsFile, $target));
    }

    /**
     * Test whether a T_PLUS or T_MINUS token is a unary operator.
     *
     * @covers \PHPCSUtils\Utils\Operators::isUnaryPlusMinus
     *
     * @dataProvider dataIsUnaryPlusMinus
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   The expected boolean return value.
     *
     * @return void
     */
    public function testIsUnaryPlusMinus($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [\T_PLUS, \T_MINUS]);
        $result   = Operators::isUnaryPlusMinus(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testIsUnaryPlusMinus() For the array format.
     *
     * @return array<string, array<string, string|bool>>
     */
    abstract public static function dataIsUnaryPlusMinus();
}
