<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\FunctionDeclarations;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\FunctionDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\FunctionDeclarations::isMagicFunction(),
 * \PHPCSUtils\Utils\FunctionDeclarations::isMagicMethod(),
 * \PHPCSUtils\Utils\FunctionDeclarations::isPHPDoubleUnderscoreMethod() and the
 * \PHPCSUtils\Utils\FunctionDeclarations::isSpecialMethod() methods.
 *
 * @coversDefaultClass \PHPCSUtils\Utils\FunctionDeclarations
 *
 * @group functiondeclarations
 *
 * @since 1.0.0
 */
final class SpecialFunctionsTest extends UtilityMethodTestCase
{

    /**
     * Test that the special function methods return false when passed a non-existent token.
     *
     * @covers ::isMagicFunction
     * @covers ::isMagicMethod
     * @covers ::isPHPDoubleUnderscoreMethod
     * @covers ::isSpecialMethod
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $result = FunctionDeclarations::isMagicFunction(self::$phpcsFile, 10000);
        $this->assertFalse($result, 'isMagicFunction() did not return false');

        $result = FunctionDeclarations::isMagicMethod(self::$phpcsFile, 10000);
        $this->assertFalse($result, 'isMagicMethod() did not return false');

        $result = FunctionDeclarations::isPHPDoubleUnderscoreMethod(self::$phpcsFile, 10000);
        $this->assertFalse($result, 'isPHPDoubleUnderscoreMethod() did not return false');

        $result = FunctionDeclarations::isSpecialMethod(self::$phpcsFile, 10000);
        $this->assertFalse($result, 'isSpecialMethod() did not return false');
    }

    /**
     * Test that the special function methods return false when passed a non-function token.
     *
     * @covers ::isMagicFunction
     * @covers ::isMagicMethod
     * @covers ::isPHPDoubleUnderscoreMethod
     * @covers ::isSpecialMethod
     *
     * @return void
     */
    public function testNotAFunctionToken()
    {
        $stackPtr = $this->getTargetToken('/* testNotAFunction */', \T_ECHO);

        $result = FunctionDeclarations::isMagicFunction(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result, 'isMagicFunction() did not return false');

        $result = FunctionDeclarations::isMagicMethod(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result, 'isMagicMethod() did not return false');

        $result = FunctionDeclarations::isPHPDoubleUnderscoreMethod(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result, 'isPHPDoubleUnderscoreMethod() did not return false');

        $result = FunctionDeclarations::isSpecialMethod(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result, 'isSpecialMethod() did not return false');
    }

    /**
     * Test correctly detecting magic functions.
     *
     * @dataProvider dataItsAKindOfMagic
     * @covers       ::isMagicFunction
     *
     * @param string              $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, bool> $expected   The expected return values for the various functions.
     *
     * @return void
     */
    public function testIsMagicFunction($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_FUNCTION);
        $result   = FunctionDeclarations::isMagicFunction(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['function'], $result);
    }

    /**
     * Test correctly detecting magic methods.
     *
     * @dataProvider dataItsAKindOfMagic
     * @covers       ::isMagicMethod
     *
     * @param string              $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, bool> $expected   The expected return values for the various functions.
     *
     * @return void
     */
    public function testIsMagicMethod($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_FUNCTION);
        $result   = FunctionDeclarations::isMagicMethod(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['method'], $result);
    }

    /**
     * Test correctly detecting PHP native double underscore methods.
     *
     * @dataProvider dataItsAKindOfMagic
     * @covers       ::isPHPDoubleUnderscoreMethod
     *
     * @param string              $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, bool> $expected   The expected return values for the various functions.
     *
     * @return void
     */
    public function testIsPHPDoubleUnderscoreMethod($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_FUNCTION);
        $result   = FunctionDeclarations::isPHPDoubleUnderscoreMethod(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['double'], $result);
    }

    /**
     * Test correctly detecting magic methods and double underscore methods.
     *
     * @dataProvider dataItsAKindOfMagic
     * @covers       ::isSpecialMethod
     *
     * @param string              $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, bool> $expected   The expected return values for the various functions.
     *
     * @return void
     */
    public function testIsSpecialMethod($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_FUNCTION);
        $result   = FunctionDeclarations::isSpecialMethod(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['special'], $result);
    }

    /**
     * Data provider.
     *
     * @see testIsMagicFunction()             For the array format.
     * @see testIsMagicMethod()               For the array format.
     * @see testIsPHPDoubleUnderscoreMethod() For the array format.
     * @see testIsSpecialMethod()             For the array format.
     *
     * @return array<string, array<string, string|array<string, bool>>>
     */
    public static function dataItsAKindOfMagic()
    {
        return [
            'MagicMethodInClass' => [
                'testMarker' => '/* testMagicMethodInClass */',
                'expected'   => [
                    'function' => false,
                    'method'   => true,
                    'double'   => false,
                    'special'  => true,
                ],
            ],
            'MagicMethodInClassUppercase' => [
                'testMarker' => '/* testMagicMethodInClassUppercase */',
                'expected'   => [
                    'function' => false,
                    'method'   => true,
                    'double'   => false,
                    'special'  => true,
                ],
            ],
            'MagicMethodInClassMixedCase' => [
                'testMarker' => '/* testMagicMethodInClassMixedCase */',
                'expected'   => [
                    'function' => false,
                    'method'   => true,
                    'double'   => false,
                    'special'  => true,
                ],
            ],
            'MagicFunctionInClassNotGlobal' => [
                'testMarker' => '/* testMagicFunctionInClassNotGlobal */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MethodInClassNotMagicName' => [
                'testMarker' => '/* testMethodInClassNotMagicName */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MagicMethodNotInClass' => [
                'testMarker' => '/* testMagicMethodNotInClass */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MagicFunction' => [
                'testMarker' => '/* testMagicFunction */',
                'expected'   => [
                    'function' => true,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MagicFunctionInConditionMixedCase' => [
                'testMarker' => '/* testMagicFunctionInConditionMixedCase */',
                'expected'   => [
                    'function' => true,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'FunctionNotMagicName' => [
                'testMarker' => '/* testFunctionNotMagicName */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MagicMethodInAnonClass' => [
                'testMarker' => '/* testMagicMethodInAnonClass */',
                'expected'   => [
                    'function' => false,
                    'method'   => true,
                    'double'   => false,
                    'special'  => true,
                ],
            ],
            'MagicMethodInAnonClassUppercase' => [
                'testMarker' => '/* testMagicMethodInAnonClassUppercase */',
                'expected'   => [
                    'function' => false,
                    'method'   => true,
                    'double'   => false,
                    'special'  => true,
                ],
            ],
            'MagicFunctionInAnonClassNotGlobal' => [
                'testMarker' => '/* testMagicFunctionInAnonClassNotGlobal */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MethodInAnonClassNotMagicName' => [
                'testMarker' => '/* testMethodInAnonClassNotMagicName */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'DoubleUnderscoreMethodInClass' => [
                'testMarker' => '/* testDoubleUnderscoreMethodInClass */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => true,
                    'special'  => true,
                ],
            ],
            'DoubleUnderscoreMethodInClassMixedcase' => [
                'testMarker' => '/* testDoubleUnderscoreMethodInClassMixedcase */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => true,
                    'special'  => true,
                ],
            ],

            'DoubleUnderscoreMethodInClassNotExtended' => [
                'testMarker' => '/* testDoubleUnderscoreMethodInClassNotExtended */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'DoubleUnderscoreMethodNotInClass' => [
                'testMarker' => '/* testDoubleUnderscoreMethodNotInClass */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MagicMethodInTrait' => [
                'testMarker' => '/* testMagicMethodInTrait */',
                'expected'   => [
                    'function' => false,
                    'method'   => true,
                    'double'   => false,
                    'special'  => true,
                ],
            ],

            'DoubleUnderscoreMethodInTrait' => [
                'testMarker' => '/* testDoubleUnderscoreMethodInTrait */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => true,
                    'special'  => true,
                ],
            ],
            'MagicFunctionInTraitNotGloba' => [
                'testMarker' => '/* testMagicFunctionInTraitNotGlobal */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MethodInTraitNotMagicName' => [
                'testMarker' => '/* testMethodInTraitNotMagicName */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MagicMethodInInterface' => [
                'testMarker' => '/* testMagicMethodInInterface */',
                'expected'   => [
                    'function' => false,
                    'method'   => true,
                    'double'   => false,
                    'special'  => true,
                ],
            ],

            'DoubleUnderscoreMethodInInterface' => [
                'testMarker' => '/* testDoubleUnderscoreMethodInInterface */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => true,
                    'special'  => true,
                ],
            ],
            'MagicFunctionInInterfaceNotGlobal' => [
                'testMarker' => '/* testMagicFunctionInInterfaceNotGlobal */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MethodInInterfaceNotMagicName' => [
                'testMarker' => '/* testMethodInInterfaceNotMagicName */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],

            'NonMagicMethod' => [
                'testMarker' => '/* testNonMagicMethod */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'NestedFunctionDeclarationMagicFunction' => [
                'testMarker' => '/* testNestedFunctionDeclarationMagicFunction */',
                'expected'   => [
                    'function' => true,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'NestedFunctionDeclarationNonMagicFunction' => [
                'testMarker' => '/* testNestedFunctionDeclarationNonMagicFunction */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'NestedFunctionDeclarationNonSpecialFunction' => [
                'testMarker' => '/* testNestedFunctionDeclarationNonSpecialFunction */',
                'expected'   => [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
        ];
    }
}
