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

use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\FunctionDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\FunctionDeclarations::isArrowFunction() and the
 * \PHPCSUtils\Utils\FunctionDeclarations::getArrowFunctionOpenClose() methods.
 *
 * These tests are loosely based on the `Tokenizer/BackfillFnTokenTest` file in PHPCS itself.
 *
 * @covers \PHPCSUtils\Utils\FunctionDeclarations::isArrowFunction
 * @covers \PHPCSUtils\Utils\FunctionDeclarations::getArrowFunctionOpenClose
 *
 * @group functiondeclarations
 *
 * @since 1.0.0
 */
class IsArrowFunctionTest extends PolyfilledTestCase
{

    /**
     * Test that the function returns false when passed a non-existent token.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectDeprecation();
        $this->expectDeprecationMessage(
            'FunctionDeclarations::isArrowFunction() function is deprecated since PHPCSUtils 1.0.0-alpha4.'
            . ' Use the `T_FN` token instead.'
        );

        $result = FunctionDeclarations::isArrowFunction(self::$phpcsFile, 10000);
        $this->assertFalse($result, 'Failed isArrowFunction() test');

        $result = FunctionDeclarations::getArrowFunctionOpenClose(self::$phpcsFile, 10000);
        $this->assertFalse($result, 'Failed getArrowFunctionOpenClose() test');
    }

    /**
     * Test that the function returns false when passed a token which definitely is not an arrow function.
     *
     * @return void
     */
    public function testUnsupportedToken()
    {
        $this->expectDeprecation();
        $this->expectDeprecationMessage(
            'FunctionDeclarations::isArrowFunction() function is deprecated since PHPCSUtils 1.0.0-alpha4.'
            . ' Use the `T_FN` token instead.'
        );

        $stackPtr = $this->getTargetToken('/* testConstantDeclaration */', \T_CONST);

        $result = FunctionDeclarations::isArrowFunction(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result, 'Failed isArrowFunction() test');

        $result = FunctionDeclarations::getArrowFunctionOpenClose(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result, 'Failed getArrowFunctionOpenClose() test');
    }

    /**
     * Test that the function returns false when passed a T_STRING token without `fn` as content.
     *
     * @return void
     */
    public function testTStringNotFn()
    {
        $this->expectDeprecation();
        $this->expectDeprecationMessage(
            'FunctionDeclarations::isArrowFunction() function is deprecated since PHPCSUtils 1.0.0-alpha4.'
            . ' Use the `T_FN` token instead.'
        );

        $stackPtr = $this->getTargetToken('/* testNotTheRightContent */', \T_STRING);

        $result = FunctionDeclarations::isArrowFunction(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result, 'Failed isArrowFunction() test');

        $result = FunctionDeclarations::getArrowFunctionOpenClose(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result, 'Failed getArrowFunctionOpenClose() test');
    }

    /**
     * Test correctly detecting arrow functions.
     *
     * @dataProvider dataArrowFunction
     *
     * @param string $testMarker    The comment which prefaces the target token in the test file.
     * @param array  $expected      The expected return value for the respective functions.
     * @param array  $targetContent The content for the target token to look for in case there could
     *                              be confusion.
     * @param bool   $skipOnPHP8    Optional. Whether the test should be skipped when the PHP 8 identifier
     *                              name tokenization is used (as the target token won't exist).
     *                              Defaults to `false`.
     *
     * @return void
     */
    public function testIsArrowFunction($testMarker, $expected, $targetContent = null, $skipOnPHP8 = false)
    {
        if ($skipOnPHP8 === true && parent::usesPhp8NameTokens() === true) {
            $this->markTestSkipped("PHP 8.0 identifier name tokenization used. Target token won't exist.");
        }

        $this->expectDeprecation();
        $this->expectDeprecationMessage(
            'FunctionDeclarations::isArrowFunction() function is deprecated since PHPCSUtils 1.0.0-alpha4.'
            . ' Use the `T_FN` token instead.'
        );

        $targets  = [\T_FN, \T_STRING];
        $stackPtr = $this->getTargetToken($testMarker, $targets, $targetContent);
        $result   = FunctionDeclarations::isArrowFunction(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['is'], $result);
    }

    /**
     * Test correctly detecting arrow functions.
     *
     * @dataProvider dataArrowFunction
     *
     * @param string $testMarker    The comment which prefaces the target token in the test file.
     * @param array  $expected      The expected return value for the respective functions.
     * @param string $targetContent The content for the target token to look for in case there could
     *                              be confusion.
     * @param bool   $skipOnPHP8    Optional. Whether the test should be skipped when the PHP 8 identifier
     *                              name tokenization is used (as the target token won't exist).
     *                              Defaults to `false`.
     *
     * @return void
     */
    public function testGetArrowFunctionOpenClose($testMarker, $expected, $targetContent = 'fn', $skipOnPHP8 = false)
    {
        if ($skipOnPHP8 === true && parent::usesPhp8NameTokens() === true) {
            $this->markTestSkipped("PHP 8.0 identifier name tokenization used. Target token won't exist.");
        }

        $this->expectDeprecation();
        $this->expectDeprecationMessage(
            'FunctionDeclarations::getArrowFunctionOpenClose() function is deprecated since PHPCSUtils 1.0.0-alpha4.'
            . ' Use the `T_FN` token instead.'
        );

        $targets  = [\T_FN, \T_STRING];
        $stackPtr = $this->getTargetToken($testMarker, $targets, $targetContent);

        // Change from offsets to absolute token positions.
        if ($expected['get'] !== false) {
            foreach ($expected['get'] as $key => $value) {
                $expected['get'][$key] += $stackPtr;
            }
        }

        $result = FunctionDeclarations::getArrowFunctionOpenClose(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['get'], $result);
    }

    /**
     * Data provider.
     *
     * @see testIsArrowFunction()           For the array format.
     * @see testgetArrowFunctionOpenClose() For the array format.
     *
     * @return array
     */
    public function dataArrowFunction()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'arrow-function-standard' => [
                'testMarker' => '/* testStandard */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 3,
                        'scope_opener'       => 5,
                        'scope_closer'       => 12,
                    ],
                ],
            ],
            'arrow-function-mixed-case' => [
                'testMarker'    => '/* testMixedCase */',
                'expected'      => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 3,
                        'scope_opener'       => 5,
                        'scope_closer'       => 12,
                    ],
                ],
                'targetContent' => 'Fn',
            ],
            'arrow-function-with-whitespace' => [
                'testMarker' => '/* testWhitespace */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 2,
                        'parenthesis_closer' => 4,
                        'scope_opener'       => 6,
                        'scope_closer'       => 13,
                    ],
                ],
            ],
            'arrow-function-with-comment' => [
                'testMarker' => '/* testComment */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 4,
                        'parenthesis_closer' => 6,
                        'scope_opener'       => 8,
                        'scope_closer'       => 15,
                    ],
                ],
            ],
            'non-arrow-function-global-function-declaration' => [
                'testMarker' => '/* testFunctionName */',
                'expected'   => [
                    'is'  => false,
                    'get' => false,
                ],
            ],
            'arrow-function-nested-outer' => [
                'testMarker' => '/* testNestedOuter */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 3,
                        'scope_opener'       => 5,
                        'scope_closer'       => 25,
                    ],
                ],
            ],
            'arrow-function-nested-inner' => [
                'testMarker' => '/* testNestedInner */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 3,
                        'scope_opener'       => 5,
                        'scope_closer'       => 16,
                    ],
                ],
            ],
            'arrow-function-function-call' => [
                'testMarker' => '/* testFunctionCall */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 3,
                        'scope_opener'       => 5,
                        'scope_closer'       => 17,
                    ],
                ],
            ],
            'arrow-function-chained-function-call' => [
                'testMarker'    => '/* testChainedFunctionCall */',
                'expected'      => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 3,
                        'scope_opener'       => 5,
                        'scope_closer'       => 12,
                    ],
                ],
                'targetContent' => 'fn',
            ],
            'arrow-function-as-function-argument' => [
                'testMarker' => '/* testFunctionArgument */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 6,
                        'scope_opener'       => 8,
                        'scope_closer'       => 15,
                    ],
                ],
            ],
            'arrow-function-nested-closure' => [
                'testMarker' => '/* testClosure */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 3,
                        'scope_opener'       => 5,
                        'scope_closer'       => 60,
                    ],
                ],
            ],
            'arrow-function-with-return-type-nullable-int' => [
                'testMarker' => '/* testReturnTypeNullableInt */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 5,
                        'scope_opener'       => 12,
                        'scope_closer'       => 19,
                    ],
                ],
            ],
            'arrow-function-with-reference' => [
                'testMarker' => '/* testReference */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 2,
                        'parenthesis_closer' => 4,
                        'scope_opener'       => 6,
                        'scope_closer'       => 9,
                    ],
                ],
            ],
            'arrow-function-grouped-within-parenthesis' => [
                'testMarker' => '/* testGrouped */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 3,
                        'scope_opener'       => 5,
                        'scope_closer'       => 8,
                    ],
                ],
            ],
            'arrow-function-as-array-value' => [
                'testMarker' => '/* testArrayValue */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 2,
                        'scope_opener'       => 4,
                        'scope_closer'       => 9,
                    ],
                ],
            ],
            'arrow-function-with-yield-in-value' => [
                'testMarker' => '/* testYield */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 3,
                        'scope_opener'       => 5,
                        'scope_closer'       => 14,
                    ],
                ],
            ],
            'arrow-function-with-return-type-nullable-namespaced-class' => [
                'testMarker' => '/* testReturnTypeNamespacedClass */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 3,
                        'scope_opener'       => ($php8Names === true) ? 10 : 15,
                        'scope_closer'       => ($php8Names === true) ? 13 : 18,
                    ],
                ],
            ],
            'arrow-function-with-return-type-nullable-partially-qualified-class' => [
                'testMarker' => '/* testReturnTypePartiallyQualifiedClass */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 3,
                        'scope_opener'       => ($php8Names === true) ? 10 : 12,
                        'scope_closer'       => ($php8Names === true) ? 13 : 15,
                    ],
                ],
            ],
            'arrow-function-with-fqn-class' => [
                'testMarker' => '/* testReturnTypeNullableFQNClass */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => ($php8Names === true) ? 6 : 7,
                        'scope_opener'       => ($php8Names === true) ? 13 : 15,
                        'scope_closer'       => ($php8Names === true) ? 16 : 18,
                    ],
                ],
            ],
            'arrow-function-with-namespace-operator-in-types' => [
                'testMarker' => '/* testNamespaceOperatorInTypes */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => ($php8Names === true) ? 5 : 7,
                        'scope_opener'       => ($php8Names === true) ? 12 : 16,
                        'scope_closer'       => ($php8Names === true) ? 15 : 19,
                    ],
                ],
            ],
            'arrow-function-with-return-type-nullable-self' => [
                'testMarker' => '/* testReturnTypeSelf */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 5,
                        'scope_opener'       => 12,
                        'scope_closer'       => 15,
                    ],
                ],
            ],
            'arrow-function-with-return-type-parent' => [
                'testMarker' => '/* testReturnTypeParent */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 5,
                        'scope_opener'       => 11,
                        'scope_closer'       => 14,
                    ],
                ],
            ],
            'arrow-function-with-return-type-callable' => [
                'testMarker' => '/* testReturnTypeCallable */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 5,
                        'scope_opener'       => 11,
                        'scope_closer'       => 14,
                    ],
                ],
            ],
            'arrow-function-with-return-type-array' => [
                'testMarker' => '/* testReturnTypeArray */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 5,
                        'scope_opener'       => 11,
                        'scope_closer'       => 14,
                    ],
                ],
            ],
            'arrow-function-with-return-type-static' => [
                'testMarker' => '/* testReturnTypeStatic */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 5,
                        'scope_opener'       => 11,
                        'scope_closer'       => 14,
                    ],
                ],
            ],

            'arrow-function-with-union-param-type' => [
                'testMarker' => '/* testUnionParamType */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 7,
                        'scope_opener'       => 13,
                        'scope_closer'       => 21,
                    ],
                ],
            ],
            'arrow-function-with-union-return-type' => [
                'testMarker' => '/* testUnionReturnType */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 3,
                        'scope_opener'       => 11,
                        'scope_closer'       => 18,
                    ],
                ],
            ],
            'arrow-function-with-return-type-array-bug-2773' => [
                'testMarker' => '/* testReturnTypeArrayBug2773 */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 2,
                        'scope_opener'       => 7,
                        'scope_closer'       => 18,
                    ],
                ],
            ],
            'arrow-function-with-array-param-and-return-type' => [
                'testMarker' => '/* testMoreArrayTypeDeclarations */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 2,
                        'parenthesis_closer' => 6,
                        'scope_opener'       => 11,
                        'scope_closer'       => 17,
                    ],
                ],
            ],
            'arrow-function-with-ternary-content' => [
                'testMarker' => '/* testTernary */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 3,
                        'scope_opener'       => 5,
                        'scope_closer'       => 40,
                    ],
                ],
            ],
            'arrow-function-with-ternary-content-after-then' => [
                'testMarker' => '/* testTernaryThen */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 2,
                        'scope_opener'       => 8,
                        'scope_closer'       => 12,
                    ],
                ],
            ],
            'arrow-function-with-ternary-content-after-else' => [
                'testMarker' => '/* testTernaryElse */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 2,
                        'scope_opener'       => 8,
                        'scope_closer'       => 11,
                    ],
                ],
            ],
            'arrow-function-as-function-call-argument' => [
                'testMarker' => '/* testArrowFunctionAsArgument */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 2,
                        'scope_opener'       => 4,
                        'scope_closer'       => 8,
                    ],
                ],
            ],
            'arrow-function-as-function-call-argument-with-array-return' => [
                'testMarker' => '/* testArrowFunctionWithArrayAsArgument */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 2,
                        'scope_opener'       => 4,
                        'scope_closer'       => 17,
                    ],
                ],
            ],
            'arrow-function-nested-in-method' => [
                'testMarker' => '/* testNestedInMethod */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 3,
                        'scope_opener'       => 5,
                        'scope_closer'       => 17,
                    ],
                ],
            ],

            /*
             * Use of the "fn" keyword when not an arrow function.
             */
            'non-arrow-function-const-declaration' => [
                'testMarker'    => '/* testConstantDeclaration */',
                'expected'      => [
                    'is'  => false,
                    'get' => false,
                ],
                'targetContent' => 'FN',
            ],
            'non-arrow-function-const-declaration-lowercase' => [
                'testMarker' => '/* testConstantDeclarationLower */',
                'expected'   => [
                    'is'  => false,
                    'get' => false,
                ],
            ],
            'non-arrow-function-static-method-declaration' => [
                'testMarker' => '/* testStaticMethodName */',
                'expected'   => [
                    'is'  => false,
                    'get' => false,
                ],
            ],
            'non-arrow-function-assignment-to-property' => [
                'testMarker' => '/* testPropertyAssignment */',
                'expected'   => [
                    'is'  => false,
                    'get' => false,
                ],
            ],
            'non-arrow-function-anon-class-method-declaration' => [
                'testMarker'    => '/* testAnonClassMethodName */',
                'expected'      => [
                    'is'  => false,
                    'get' => false,
                ],
                'targetContent' => 'fN',
            ],
            'non-arrow-function-call-to-static-method' => [
                'testMarker' => '/* testNonArrowStaticMethodCall */',
                'expected'   => [
                    'is'  => false,
                    'get' => false,
                ],
            ],
            'non-arrow-function-class-constant-access' => [
                'testMarker'    => '/* testNonArrowConstantAccess */',
                'expected'      => [
                    'is'  => false,
                    'get' => false,
                ],
                'targetContent' => 'FN',
            ],
            'non-arrow-function-class-constant-access-with-deref' => [
                'testMarker'    => '/* testNonArrowConstantAccessDeref */',
                'expected'      => [
                    'is'  => false,
                    'get' => false,
                ],
                'targetContent' => 'Fn',
            ],
            'non-arrow-function-call-to-object-method' => [
                'testMarker' => '/* testNonArrowObjectMethodCall */',
                'expected'   => [
                    'is'  => false,
                    'get' => false,
                ],
            ],
            'non-arrow-function-call-to-object-method-uppercase' => [
                'testMarker'    => '/* testNonArrowObjectMethodCallUpper */',
                'expected'      => [
                    'is'  => false,
                    'get' => false,
                ],
                'targetContent' => 'FN',
            ],
            'non-arrow-function-call-to-namespaced-function' => [
                'testMarker'    => '/* testNonArrowNamespacedFunctionCall */',
                'expected'      => [
                    'is'  => false,
                    'get' => false,
                ],
                'targetContent' => 'Fn',
                'skipOnPHP8'    => true,
            ],
            'non-arrow-function-call-to-namespaced-function-using-namespace-operator' => [
                'testMarker'    => '/* testNonArrowNamespaceOperatorFunctionCall */',
                'expected'      => [
                    'is'  => false,
                    'get' => false,
                ],
                'targetContent' => 'fn',
                'skipOnPHP8'    => true,
            ],
            'non-arrow-function-declaration-with-union-types' => [
                'testMarker' => '/* testNonArrowFunctionNameWithUnionTypes */',
                'expected'   => [
                    'is'  => false,
                    'get' => false,
                ],
            ],

            'live-coding' => [
                'testMarker' => '/* testLiveCoding */',
                'expected'   => [
                    'is'  => false,
                    'get' => false,
                ],
            ],
        ];
    }
}
