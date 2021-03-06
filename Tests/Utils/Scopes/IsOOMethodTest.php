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
 * Tests for the \PHPCSUtils\Utils\Scopes::isOOMethod() method.
 *
 * @group scopes
 *
 * @since 1.0.0
 */
class IsOOMethodTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @covers \PHPCSUtils\Utils\Scopes::isOOMethod
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $result = Scopes::isOOMethod(self::$phpcsFile, 10000);
        $this->assertFalse($result);
    }

    /**
     * Test passing a non function token.
     *
     * @covers \PHPCSUtils\Utils\Scopes::isOOMethod
     *
     * @return void
     */
    public function testNonFunctionToken()
    {
        $result = Scopes::isOOMethod(self::$phpcsFile, 0);
        $this->assertFalse($result);
    }

    /**
     * Test correctly identifying whether a T_FUNCTION token is a class method declaration.
     *
     * @dataProvider dataIsOOMethod
     *
     * @covers \PHPCSUtils\Utils\Scopes::isOOMethod
     * @covers \PHPCSUtils\Utils\Scopes::validDirectScope
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   The expected function return value.
     *
     * @return void
     */
    public function testIsOOMethod($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [\T_FUNCTION, \T_CLOSURE]);
        $result   = Scopes::isOOMethod(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testIsOOMethod() For the array format.
     *
     * @return array
     */
    public function dataIsOOMethod()
    {
        return [
            'global-function' => [
                '/* testGlobalFunction */',
                false,
            ],
            'nested-function' => [
                '/* testNestedFunction */',
                false,
            ],
            'nested-closure' => [
                '/* testNestedClosure */',
                false,
            ],
            'class-method' => [
                '/* testClassMethod */',
                true,
            ],
            'class-nested-function' => [
                '/* testClassNestedFunction */',
                false,
            ],
            'class-nested-closure' => [
                '/* testClassNestedClosure */',
                false,
            ],
            'class-abstract-method' => [
                '/* testClassAbstractMethod */',
                true,
            ],
            'anon-class-method' => [
                '/* testAnonClassMethod */',
                true,
            ],
            'interface-method' => [
                '/* testInterfaceMethod */',
                true,
            ],
            'trait-method' => [
                '/* testTraitMethod */',
                true,
            ],
        ];
    }
}
