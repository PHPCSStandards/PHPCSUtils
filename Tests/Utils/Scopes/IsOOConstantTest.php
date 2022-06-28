<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Scopes;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Scopes;

/**
 * Tests for the \PHPCSUtils\Utils\Scopes::isOOConstant() method.
 *
 * @coversDefaultClass \PHPCSUtils\Utils\Scopes
 *
 * @group scopes
 *
 * @since 1.0.0
 */
class IsOOConstantTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @covers ::isOOConstant
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $result = Scopes::isOOConstant(self::$phpcsFile, 10000);
        $this->assertFalse($result);
    }

    /**
     * Test passing a non const token.
     *
     * @covers ::isOOConstant
     *
     * @return void
     */
    public function testNonConstToken()
    {
        $result = Scopes::isOOConstant(self::$phpcsFile, 0);
        $this->assertFalse($result);
    }

    /**
     * Test correctly identifying whether a T_CONST token is a class constant.
     *
     * @dataProvider dataIsOOConstant
     *
     * @covers ::isOOConstant
     * @covers ::validDirectScope
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   The expected function return value.
     *
     * @return void
     */
    public function testIsOOConstant($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_CONST);
        $result   = Scopes::isOOConstant(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testIsOOConstant() For the array format.
     *
     * @return array
     */
    public function dataIsOOConstant()
    {
        return [
            'global-const' => [
                '/* testGlobalConst */',
                false,
            ],
            'function-const' => [
                '/* testFunctionConst */',
                false,
            ],
            'class-const' => [
                '/* testClassConst */',
                true,
            ],
            'method-const' => [
                '/* testClassMethodConst */',
                false,
            ],
            'anon-class-const' => [
                '/* testAnonClassConst */',
                true,
            ],
            'interface-const' => [
                '/* testInterfaceConst */',
                true,
            ],
            'trait-const' => [
                '/* testTraitConst */',
                false,
            ],
        ];
    }
}
