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
use PHPCSUtils\Tokens\Collections;
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
class IsArrowFunctionTest extends UtilityMethodTestCase
{

    /**
     * Test that the function returns false when passed a non-existent token.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
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
     *
     * @return void
     */
    public function testIsArrowFunction($testMarker, $expected, $targetContent = null)
    {
        $targets  = Collections::arrowFunctionTokensBC();
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
     *
     * @return void
     */
    public function testGetArrowFunctionOpenClose($testMarker, $expected, $targetContent = 'fn')
    {
        $targets  = Collections::arrowFunctionTokensBC();
        $stackPtr = $this->getTargetToken($testMarker, $targets, $targetContent);

        // Change from offsets to absolute token positions.
        if ($expected['get'] != false) {
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
        return [
            'arrow-function-standard' => [
                '/* testStandard */',
                [
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
                '/* testMixedCase */',
                [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 3,
                        'scope_opener'       => 5,
                        'scope_closer'       => 12,
                    ],
                ],
                'Fn',
            ],
            'arrow-function-with-whitespace' => [
                '/* testWhitespace */',
                [
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
                '/* testComment */',
                [
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
                '/* testFunctionName */',
                [
                    'is'  => false,
                    'get' => false,
                ],
            ],
            'arrow-function-nested-outer' => [
                '/* testNestedOuter */',
                [
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
                '/* testNestedInner */',
                [
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
                '/* testFunctionCall */',
                [
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
                '/* testChainedFunctionCall */',
                [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 3,
                        'scope_opener'       => 5,
                        'scope_closer'       => 12,
                    ],
                ],
                'fn',
            ],
            'arrow-function-as-function-argument' => [
                '/* testFunctionArgument */',
                [
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
                '/* testClosure */',
                [
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
                '/* testReturnTypeNullableInt */',
                [
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
                '/* testReference */',
                [
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
                '/* testGrouped */',
                [
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
                '/* testArrayValue */',
                [
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
                '/* testYield */',
                [
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
                '/* testReturnTypeNamespacedClass */',
                [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 3,
                        'scope_opener'       => 15,
                        'scope_closer'       => 18,
                    ],
                ],
            ],
            'arrow-function-with-fqn-class' => [
                '/* testReturnTypeNullableFQNClass */',
                [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 7,
                        'scope_opener'       => 15,
                        'scope_closer'       => 18,
                    ],
                ],
            ],
            'arrow-function-with-return-type-nullable-self' => [
                '/* testReturnTypeSelf */',
                [
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
                '/* testReturnTypeParent */',
                [
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
                '/* testReturnTypeCallable */',
                [
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
                '/* testReturnTypeArray */',
                [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 5,
                        'scope_opener'       => 11,
                        'scope_closer'       => 14,
                    ],
                ],
            ],
            'arrow-function-with-return-type-array-bug-2773' => [
                '/* testReturnTypeArrayBug2773 */',
                [
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
                '/* testMoreArrayTypeDeclarations */',
                [
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
                '/* testTernary */',
                [
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
                '/* testTernaryThen */',
                [
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
                '/* testTernaryElse */',
                [
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
                '/* testArrowFunctionAsArgument */',
                [
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
                '/* testArrowFunctionWithArrayAsArgument */',
                [
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
                '/* testNestedInMethod */',
                [
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
                '/* testConstantDeclaration */',
                [
                    'is'  => false,
                    'get' => false,
                ],
                'FN',
            ],
            'non-arrow-function-const-declaration-lowercase' => [
                '/* testConstantDeclarationLower */',
                [
                    'is'  => false,
                    'get' => false,
                ],
            ],
            'non-arrow-function-static-method-declaration' => [
                '/* testStaticMethodName */',
                [
                    'is'  => false,
                    'get' => false,
                ],
            ],
            'non-arrow-function-assignment-to-property' => [
                '/* testPropertyAssignment */',
                [
                    'is'  => false,
                    'get' => false,
                ],
            ],
            'non-arrow-function-anon-class-method-declaration' => [
                '/* testAnonClassMethodName */',
                [
                    'is'  => false,
                    'get' => false,
                ],
                'fN',
            ],
            'non-arrow-function-call-to-static-method' => [
                '/* testNonArrowStaticMethodCall */',
                [
                    'is'  => false,
                    'get' => false,
                ],
            ],
            'non-arrow-function-class-constant-access' => [
                '/* testNonArrowConstantAccess */',
                [
                    'is'  => false,
                    'get' => false,
                ],
                'FN',
            ],
            'non-arrow-function-class-constant-access-with-deref' => [
                '/* testNonArrowConstantAccessDeref */',
                [
                    'is'  => false,
                    'get' => false,
                ],
                'Fn',
            ],
            'non-arrow-function-call-to-object-method' => [
                '/* testNonArrowObjectMethodCall */',
                [
                    'is'  => false,
                    'get' => false,
                ],
            ],
            'non-arrow-function-call-to-object-method-uppercase' => [
                '/* testNonArrowObjectMethodCallUpper */',
                [
                    'is'  => false,
                    'get' => false,
                ],
                'FN',
            ],
            'non-arrow-function-call-to-namespaced-function' => [
                '/* testNonArrowNamespacedFunctionCall */',
                [
                    'is'  => false,
                    'get' => false,
                ],
                'Fn',
            ],
            'non-arrow-function-call-to-namespaced-function-using-namespace-operator' => [
                '/* testNonArrowNamespaceOperatorFunctionCall */',
                [
                    'is'  => false,
                    'get' => false,
                ],
            ],

            'live-coding' => [
                '/* testLiveCoding */',
                [
                    'is'  => false,
                    'get' => false,
                ],
            ],
        ];
    }
}
