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
 * @group functiondeclarations
 *
 * @since 1.0.0
 */
class SpecialFunctionsTest extends UtilityMethodTestCase
{

    /**
     * Test that the special function methods return false when passed a non-existent token.
     *
     * @covers \PHPCSUtils\Utils\FunctionDeclarations::isMagicFunction
     * @covers \PHPCSUtils\Utils\FunctionDeclarations::isMagicMethod
     * @covers \PHPCSUtils\Utils\FunctionDeclarations::isPHPDoubleUnderscoreMethod
     * @covers \PHPCSUtils\Utils\FunctionDeclarations::isSpecialMethod
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
     * @covers \PHPCSUtils\Utils\FunctionDeclarations::isMagicFunction
     * @covers \PHPCSUtils\Utils\FunctionDeclarations::isMagicMethod
     * @covers \PHPCSUtils\Utils\FunctionDeclarations::isPHPDoubleUnderscoreMethod
     * @covers \PHPCSUtils\Utils\FunctionDeclarations::isSpecialMethod
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
     * @covers       \PHPCSUtils\Utils\FunctionDeclarations::isMagicFunction
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected return values for the various functions.
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
     * @covers       \PHPCSUtils\Utils\FunctionDeclarations::isMagicMethod
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected return values for the various functions.
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
     * @covers       \PHPCSUtils\Utils\FunctionDeclarations::isPHPDoubleUnderscoreMethod
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected return values for the various functions.
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
     * @covers       \PHPCSUtils\Utils\FunctionDeclarations::isSpecialMethod
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected return values for the various functions.
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
     * @return array
     */
    public function dataItsAKindOfMagic()
    {
        return [
            'MagicMethodInClass' => [
                '/* testMagicMethodInClass */',
                [
                    'function' => false,
                    'method'   => true,
                    'double'   => false,
                    'special'  => true,
                ],
            ],
            'MagicMethodInClassUppercase' => [
                '/* testMagicMethodInClassUppercase */',
                [
                    'function' => false,
                    'method'   => true,
                    'double'   => false,
                    'special'  => true,
                ],
            ],
            'MagicMethodInClassMixedCase' => [
                '/* testMagicMethodInClassMixedCase */',
                [
                    'function' => false,
                    'method'   => true,
                    'double'   => false,
                    'special'  => true,
                ],
            ],
            'MagicFunctionInClassNotGlobal' => [
                '/* testMagicFunctionInClassNotGlobal */',
                [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MethodInClassNotMagicName' => [
                '/* testMethodInClassNotMagicName */',
                [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MagicMethodNotInClass' => [
                '/* testMagicMethodNotInClass */',
                [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MagicFunction' => [
                '/* testMagicFunction */',
                [
                    'function' => true,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MagicFunctionInConditionMixedCase' => [
                '/* testMagicFunctionInConditionMixedCase */',
                [
                    'function' => true,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'FunctionNotMagicName' => [
                '/* testFunctionNotMagicName */',
                [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MagicMethodInAnonClass' => [
                '/* testMagicMethodInAnonClass */',
                [
                    'function' => false,
                    'method'   => true,
                    'double'   => false,
                    'special'  => true,
                ],
            ],
            'MagicMethodInAnonClassUppercase' => [
                '/* testMagicMethodInAnonClassUppercase */',
                [
                    'function' => false,
                    'method'   => true,
                    'double'   => false,
                    'special'  => true,
                ],
            ],
            'MagicFunctionInAnonClassNotGlobal' => [
                '/* testMagicFunctionInAnonClassNotGlobal */',
                [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MethodInAnonClassNotMagicName' => [
                '/* testMethodInAnonClassNotMagicName */',
                [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'DoubleUnderscoreMethodInClass' => [
                '/* testDoubleUnderscoreMethodInClass */',
                [
                    'function' => false,
                    'method'   => false,
                    'double'   => true,
                    'special'  => true,
                ],
            ],
            'DoubleUnderscoreMethodInClassMixedcase' => [
                '/* testDoubleUnderscoreMethodInClassMixedcase */',
                [
                    'function' => false,
                    'method'   => false,
                    'double'   => true,
                    'special'  => true,
                ],
            ],

            'DoubleUnderscoreMethodInClassNotExtended' => [
                '/* testDoubleUnderscoreMethodInClassNotExtended */',
                [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'DoubleUnderscoreMethodNotInClass' => [
                '/* testDoubleUnderscoreMethodNotInClass */',
                [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MagicMethodInTrait' => [
                '/* testMagicMethodInTrait */',
                [
                    'function' => false,
                    'method'   => true,
                    'double'   => false,
                    'special'  => true,
                ],
            ],

            'DoubleUnderscoreMethodInTrait' => [
                '/* testDoubleUnderscoreMethodInTrait */',
                [
                    'function' => false,
                    'method'   => false,
                    'double'   => true,
                    'special'  => true,
                ],
            ],
            'MagicFunctionInTraitNotGloba' => [
                '/* testMagicFunctionInTraitNotGlobal */',
                [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MethodInTraitNotMagicName' => [
                '/* testMethodInTraitNotMagicName */',
                [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MagicMethodInInterface' => [
                '/* testMagicMethodInInterface */',
                [
                    'function' => false,
                    'method'   => true,
                    'double'   => false,
                    'special'  => true,
                ],
            ],

            'DoubleUnderscoreMethodInInterface' => [
                '/* testDoubleUnderscoreMethodInInterface */',
                [
                    'function' => false,
                    'method'   => false,
                    'double'   => true,
                    'special'  => true,
                ],
            ],
            'MagicFunctionInInterfaceNotGlobal' => [
                '/* testMagicFunctionInInterfaceNotGlobal */',
                [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
            'MethodInInterfaceNotMagicName' => [
                '/* testMethodInInterfaceNotMagicName */',
                [
                    'function' => false,
                    'method'   => false,
                    'double'   => false,
                    'special'  => false,
                ],
            ],
        ];
    }
}
