<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Scopes;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Scopes;

/**
 * Tests for the \PHPCSUtils\Utils\Scopes::isOOProperty method.
 *
 * @group scopes
 *
 * @since 1.0.0
 */
class IsOOPropertyTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @covers \PHPCSUtils\Utils\Scopes::isOOProperty
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
     * @covers \PHPCSUtils\Utils\Scopes::isOOProperty
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
     * @covers \PHPCSUtils\Utils\Scopes::isOOProperty
     * @covers \PHPCSUtils\Utils\Scopes::validDirectScope
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
     * @return array
     */
    public function dataIsOOProperty()
    {
        return [
            'global-var' => [
                '/* testGlobalVar */',
                false,
            ],
            'function-param' => [
                '/* testFunctionParameter */',
                false,
            ],
            'function-local-var' => [
                '/* testFunctionLocalVar */',
                false,
            ],
            'class-property-public' => [
                '/* testClassPropPublic */',
                true,
            ],
            'class-property-var' => [
                '/* testClassPropVar */',
                true,
            ],
            'class-property-static-protected' => [
                '/* testClassPropStaticProtected */',
                true,
            ],
            'method-param' => [
                '/* testMethodParameter */',
                false,
            ],
            'method-local-var' => [
                '/* testMethodLocalVar */',
                false,
            ],
            'anon-class-property-private' => [
                '/* testAnonClassPropPrivate */',
                true,
            ],
            'anon-class-method-param' => [
                '/* testAnonMethodParameter */',
                false,
            ],
            'anon-class-method-local-var' => [
                '/* testAnonMethodLocalVar */',
                false,
            ],
            'interface-property' => [
                '/* testInterfaceProp */',
                false,
            ],
            'interface-method-param' => [
                '/* testInterfaceMethodParameter */',
                false,
            ],
            'trait-property' => [
                '/* testTraitProp */',
                true,
            ],
            'trait-method-param' => [
                '/* testTraitMethodParameter */',
                false,
            ],
            'class-multi-property-1' => [
                '/* testClassMultiProp1 */',
                true,
            ],
            'class-multi-property-2' => [
                '/* testClassMultiProp2 */',
                true,
            ],
            'class-multi-property-3' => [
                '/* testClassMultiProp3 */',
                true,
            ],
            'global-var-obj-access' => [
                '/* testGlobalVarObj */',
                false,
            ],
            'nested-anon-class-property' => [
                '/* testNestedAnonClassProp */',
                true,
            ],
            'double-nested-anon-class-property' => [
                '/* testDoubleNestedAnonClassProp */',
                true,
            ],
            'double-nested-anon-class-method-param' => [
                '/* testDoubleNestedAnonClassMethodParameter */',
                false,
            ],
            'double-nested-anon-class-method-local-var' => [
                '/* testDoubleNestedAnonClassMethodLocalVar */',
                false,
            ],
            'function-call-param' => [
                '/* testFunctionCallParameter */',
                false,
            ],
        ];
    }
}
