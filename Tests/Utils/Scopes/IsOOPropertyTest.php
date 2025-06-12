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
 * Tests for the \PHPCSUtils\Utils\Scopes::isOOProperty method.
 *
 * @coversDefaultClass \PHPCSUtils\Utils\Scopes
 *
 * @since 1.0.0
 */
final class IsOOPropertyTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @covers ::isOOProperty
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $result = Scopes::isOOProperty(self::$phpcsFile, 10000);
        $this->assertFalse($result);
    }

    /**
     * Test passing a non variable token.
     *
     * @covers ::isOOProperty
     *
     * @return void
     */
    public function testNonVariableToken()
    {
        $result = Scopes::isOOProperty(self::$phpcsFile, 0);
        $this->assertFalse($result);
    }

    /**
     * Test correctly identifying whether a T_VARIABLE token is a class property declaration.
     *
     * @dataProvider dataIsOOProperty
     *
     * @covers ::isOOProperty
     * @covers ::validDirectScope
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   The expected function return value.
     *
     * @return void
     */
    public function testIsOOProperty($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_VARIABLE);
        $result   = Scopes::isOOProperty(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testIsOOProperty() For the array format.
     *
     * @return array<string, array<string, string|bool>>
     */
    public static function dataIsOOProperty()
    {
        return [
            'global-var' => [
                'testMarker' => '/* testGlobalVar */',
                'expected'   => false,
            ],
            'function-param' => [
                'testMarker' => '/* testFunctionParameter */',
                'expected'   => false,
            ],
            'function-local-var' => [
                'testMarker' => '/* testFunctionLocalVar */',
                'expected'   => false,
            ],
            'class-property-public' => [
                'testMarker' => '/* testClassPropPublic */',
                'expected'   => true,
            ],
            'class-property-var' => [
                'testMarker' => '/* testClassPropVar */',
                'expected'   => true,
            ],
            'class-property-static-protected' => [
                'testMarker' => '/* testClassPropStaticProtected */',
                'expected'   => true,
            ],
            'method-param' => [
                'testMarker' => '/* testMethodParameter */',
                'expected'   => false,
            ],
            'method-local-var' => [
                'testMarker' => '/* testMethodLocalVar */',
                'expected'   => false,
            ],
            'anon-class-property-private' => [
                'testMarker' => '/* testAnonClassPropPrivate */',
                'expected'   => true,
            ],
            'anon-class-method-param' => [
                'testMarker' => '/* testAnonMethodParameter */',
                'expected'   => false,
            ],
            'anon-class-method-local-var' => [
                'testMarker' => '/* testAnonMethodLocalVar */',
                'expected'   => false,
            ],
            'interface-property' => [
                'testMarker' => '/* testInterfaceProp */',
                'expected'   => true,
            ],
            'interface-method-param' => [
                'testMarker' => '/* testInterfaceMethodParameter */',
                'expected'   => false,
            ],
            'trait-property' => [
                'testMarker' => '/* testTraitProp */',
                'expected'   => true,
            ],
            'trait-method-param' => [
                'testMarker' => '/* testTraitMethodParameter */',
                'expected'   => false,
            ],
            'class-multi-property-1' => [
                'testMarker' => '/* testClassMultiProp1 */',
                'expected'   => true,
            ],
            'class-multi-property-2' => [
                'testMarker' => '/* testClassMultiProp2 */',
                'expected'   => true,
            ],
            'class-multi-property-3' => [
                'testMarker' => '/* testClassMultiProp3 */',
                'expected'   => true,
            ],
            'global-var-obj-access' => [
                'testMarker' => '/* testGlobalVarObj */',
                'expected'   => false,
            ],
            'nested-anon-class-property' => [
                'testMarker' => '/* testNestedAnonClassProp */',
                'expected'   => true,
            ],
            'double-nested-anon-class-property' => [
                'testMarker' => '/* testDoubleNestedAnonClassProp */',
                'expected'   => true,
            ],
            'double-nested-anon-class-method-param' => [
                'testMarker' => '/* testDoubleNestedAnonClassMethodParameter */',
                'expected'   => false,
            ],
            'double-nested-anon-class-method-local-var' => [
                'testMarker' => '/* testDoubleNestedAnonClassMethodLocalVar */',
                'expected'   => false,
            ],
            'function-call-param' => [
                'testMarker' => '/* testFunctionCallParameter */',
                'expected'   => false,
            ],
            'enum-property' => [
                'testMarker' => '/* testEnumProp */',
                'expected'   => false,
            ],
            'enum-method-param' => [
                'testMarker' => '/* testEnumMethodParameter */',
                'expected'   => false,
            ],
        ];
    }
}
