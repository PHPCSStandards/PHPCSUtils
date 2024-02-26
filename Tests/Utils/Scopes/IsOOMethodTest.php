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
 * @coversDefaultClass \PHPCSUtils\Utils\Scopes
 *
 * @since 1.0.0
 */
final class IsOOMethodTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @covers ::isOOMethod
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
     * @covers ::isOOMethod
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
     * @covers ::isOOMethod
     * @covers ::validDirectScope
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
     * @return array<string, array<string, string|bool>>
     */
    public static function dataIsOOMethod()
    {
        return [
            'global-function' => [
                'testMarker' => '/* testGlobalFunction */',
                'expected'   => false,
            ],
            'nested-function' => [
                'testMarker' => '/* testNestedFunction */',
                'expected'   => false,
            ],
            'nested-closure' => [
                'testMarker' => '/* testNestedClosure */',
                'expected'   => false,
            ],
            'class-method' => [
                'testMarker' => '/* testClassMethod */',
                'expected'   => true,
            ],
            'class-nested-function' => [
                'testMarker' => '/* testClassNestedFunction */',
                'expected'   => false,
            ],
            'class-nested-closure' => [
                'testMarker' => '/* testClassNestedClosure */',
                'expected'   => false,
            ],
            'class-abstract-method' => [
                'testMarker' => '/* testClassAbstractMethod */',
                'expected'   => true,
            ],
            'anon-class-method' => [
                'testMarker' => '/* testAnonClassMethod */',
                'expected'   => true,
            ],
            'interface-method' => [
                'testMarker' => '/* testInterfaceMethod */',
                'expected'   => true,
            ],
            'trait-method' => [
                'testMarker' => '/* testTraitMethod */',
                'expected'   => true,
            ],
            'enum-method' => [
                'testMarker' => '/* testEnumMethod */',
                'expected'   => true,
            ],
            'enum-nested-function' => [
                'testMarker' => '/* testEnumNestedFunction */',
                'expected'   => false,
            ],
            'enum-nested-closure' => [
                'testMarker' => '/* testEnumNestedClosure */',
                'expected'   => false,
            ],
        ];
    }
}
