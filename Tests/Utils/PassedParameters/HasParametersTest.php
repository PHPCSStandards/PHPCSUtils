<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\PassedParameters;

use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\PassedParameters;

/**
 * Tests for the \PHPCSUtils\Utils\PassedParameters::hasParameters() method.
 *
 * @covers \PHPCSUtils\Utils\PassedParameters::hasParameters
 *
 * @since 1.0.0
 */
final class HasParametersTest extends PolyfilledTestCase
{

    /**
     * Test receiving an expected exception when a non-integer token pointer is passed.
     *
     * @return void
     */
    public function testNonIntegerToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type integer, array given');

        PassedParameters::hasParameters(self::$phpcsFile, []);
    }

    /**
     * Test receiving an expected exception when an invalid token pointer is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object, 100000 given'
        );

        PassedParameters::hasParameters(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an expected exception when a token which is not supported by
     * these methods is passed.
     *
     * @return void
     */
    public function testNotAnAcceptedTokenException()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be of type function call, array, isset, unset or exit;'
        );

        $interface = $this->getTargetToken('/* testNotAnAcceptedToken */', \T_INTERFACE);
        PassedParameters::hasParameters(self::$phpcsFile, $interface);
    }

    /**
     * Test receiving an expected exception when a hierarchy keyword is passed not preceeded by `new`.
     *
     * @dataProvider dataNotACallToConstructor
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType The type of token to look for.
     *
     * @return void
     */
    public function testNotACallToConstructor($testMarker, $targetType)
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be of type function call, array, isset, unset or exit;'
        );

        $self = $this->getTargetToken($testMarker, $targetType);
        PassedParameters::hasParameters(self::$phpcsFile, $self);
    }

    /**
     * Data provider.
     *
     * @see testNotACallToConstructor() For the array format.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataNotACallToConstructor()
    {
        return [
            'parent' => [
                'testMarker' => '/* testNotACallToConstructor1 */',
                'targetType' => \T_PARENT,
            ],
            'static' => [
                'testMarker' => '/* testNotACallToConstructor2 */',
                'targetType' => \T_STATIC,
            ],
            'self' => [
                'testMarker' => '/* testNotACallToConstructor3 */',
                'targetType' => \T_SELF,
            ],
        ];
    }

    /**
     * Test receiving an expected exception when T_OPEN_SHORT_ARRAY is passed but represents a short list.
     *
     * @return void
     */
    public function testNotAShortArray()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be of type function call, array, isset, unset or exit;'
        );

        $self = $this->getTargetToken(
            '/* testShortListNotShortArray */',
            Collections::shortArrayListOpenTokensBC()
        );
        PassedParameters::hasParameters(self::$phpcsFile, $self);
    }

    /**
     * Test correctly identifying whether parameters were passed to a function call or construct.
     *
     * @dataProvider dataHasParameters
     *
     * @param string                       $testMarker    The comment which prefaces the target token in the test file.
     * @param int|string|array<int|string> $targetType    The type(s) of token to look for.
     * @param bool                         $expected      Whether or not the function/array has parameters/values.
     * @param string|null                  $targetContent Optional. The content of the target token to find.
     *                                                    Defaults to null (ignore content).
     *
     * @return void
     */
    public function testHasParameters($testMarker, $targetType, $expected, $targetContent = null)
    {
        $stackPtr = $this->getTargetToken($testMarker, $targetType, $targetContent);
        $result   = PassedParameters::hasParameters(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testHasParameters() For the array format.
     *
     * @return array<string, array<string, int|string|bool|array<int|string>|null>>
     */
    public static function dataHasParameters()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            // Function calls.
            'no-params-function-call-1' => [
                'testMarker' => '/* testNoParamsFunctionCall1 */',
                'targetType' => \T_STRING,
                'expected'   => false,
            ],
            'no-params-function-call-2' => [
                'testMarker' => '/* testNoParamsFunctionCall2 */',
                'targetType' => \T_STRING,
                'expected'   => false,
            ],
            'no-params-function-call-3' => [
                'testMarker' => '/* testNoParamsFunctionCall3 */',
                'targetType' => \T_STRING,
                'expected'   => false,
            ],
            'no-params-function-call-4' => [
                'testMarker' => '/* testNoParamsFunctionCall4 */',
                'targetType' => \T_VARIABLE,
                'expected'   => false,
            ],
            'no-params-function-call-5-new-self' => [
                'testMarker' => '/* testNoParamsFunctionCall5 */',
                'targetType' => \T_SELF,
                'expected'   => false,
            ],
            'no-params-function-call-6-new-static' => [
                'testMarker' => '/* testNoParamsFunctionCall6 */',
                'targetType' => \T_STATIC,
                'expected'   => false,
            ],
            'no-params-function-call-7-new-parent' => [
                'testMarker' => '/* testNoParamsFunctionCall7 */',
                'targetType' => \T_PARENT,
                'expected'   => false,
            ],

            'has-params-function-call-1' => [
                'testMarker' => '/* testHasParamsFunctionCall1 */',
                'targetType' => \T_STRING,
                'expected'   => true,
            ],
            'has-params-function-call-2' => [
                'testMarker' => '/* testHasParamsFunctionCall2 */',
                'targetType' => \T_VARIABLE,
                'expected'   => true,
            ],
            'has-params-function-call-3-new-self' => [
                'testMarker' => '/* testHasParamsFunctionCall3 */',
                'targetType' => \T_SELF,
                'expected'   => true,
            ],
            'has-params-function-call-4-new-static' => [
                'testMarker' => '/* testHasParamsFunctionCall4 */',
                'targetType' => \T_STATIC,
                'expected'   => true,
            ],
            'has-params-function-call-5-new-parent' => [
                'testMarker' => '/* testHasParamsFunctionCall5 */',
                'targetType' => \T_PARENT,
                'expected'   => true,
            ],
            'has-params-function-call-6-self-as-method-name' => [
                'testMarker'    => '/* testHasParamsFunctionCall6 */',
                'targetType'    => \T_STRING,
                'expected'      => true,
                'targetContent' => 'self',
            ],
            'has-params-function-call-7-static-as-method-name' => [
                'testMarker'    => '/* testHasParamsFunctionCall7 */',
                'targetType'    => \T_STRING,
                'expected'      => true,
                'targetContent' => 'static',
            ],
            'has-params-function-call-8-parent-as-method-name' => [
                'testMarker'    => '/* testHasParamsFunctionCall8 */',
                'targetType'    => \T_STRING,
                'expected'      => true,
                'targetContent' => 'parent',
            ],
            'has-params-function-call-9-self-as-global-function-name' => [
                'testMarker'    => '/* testHasParamsFunctionCall9 */',
                'targetType'    => [\T_STRING, \T_SELF],
                'expected'      => true,
                'targetContent' => 'self',
            ],
            // Parse error in PHP, but not our concern.
            'has-params-function-call-10-static-as-global-function-name' => [
                'testMarker'    => '/* testHasParamsFunctionCall10 */',
                'targetType'    => [\T_STRING, \T_STATIC],
                'expected'      => true,
                'targetContent' => 'static',
            ],
            'has-params-function-call-11-parent-as-global-function-name' => [
                'testMarker'    => '/* testHasParamsFunctionCall11 */',
                'targetType'    => [\T_STRING, \T_PARENT],
                'expected'      => true,
                'targetContent' => 'parent',
            ],

            'no-params-function-call-fully-qualified' => [
                'testMarker'    => '/* testNoParamsFunctionCallFullyQualified */',
                'targetType'    => ($php8Names === true) ? \T_NAME_FULLY_QUALIFIED : \T_STRING,
                'expected'      => false,
                'targetContent' => ($php8Names === true) ? null : 'myfunction',
            ],
            'has-params-function-call-fully-qualified-with-namespace' => [
                'testMarker'    => '/* testHasParamsFunctionCallFullyQualifiedWithNamespace */',
                'targetType'    => ($php8Names === true) ? \T_NAME_FULLY_QUALIFIED : \T_STRING,
                'expected'      => true,
                'targetContent' => ($php8Names === true) ? null : 'myfunction',
            ],
            'no-params-function-call-partially-qualified' => [
                'testMarker'    => '/* testNoParamsFunctionCallPartiallyQualified */',
                'targetType'    => ($php8Names === true) ? \T_NAME_QUALIFIED : \T_STRING,
                'expected'      => false,
                'targetContent' => ($php8Names === true) ? null : 'myfunction',
            ],
            'has-params-function-call-namespace-operator-relative' => [
                'testMarker'    => '/* testHasParamsFunctionCallNamespaceOperator */',
                'targetType'    => ($php8Names === true) ? \T_NAME_RELATIVE : \T_STRING,
                'expected'      => true,
                'targetContent' => ($php8Names === true) ? null : 'myfunction',
            ],

            // Arrays.
            'no-params-long-array-1' => [
                'testMarker' => '/* testNoParamsLongArray1 */',
                'targetType' => \T_ARRAY,
                'expected'   => false,
            ],
            'no-params-long-array-2' => [
                'testMarker' => '/* testNoParamsLongArray2 */',
                'targetType' => \T_ARRAY,
                'expected'   => false,
            ],
            'no-params-long-array-3' => [
                'testMarker' => '/* testNoParamsLongArray3 */',
                'targetType' => \T_ARRAY,
                'expected'   => false,
            ],
            'no-params-long-array-4' => [
                'testMarker' => '/* testNoParamsLongArray4 */',
                'targetType' => \T_ARRAY,
                'expected'   => false,
            ],
            'no-params-short-array-1' => [
                'testMarker' => '/* testNoParamsShortArray1 */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'expected'   => false,
            ],
            'no-params-short-array-2' => [
                'testMarker' => '/* testNoParamsShortArray2 */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'expected'   => false,
            ],
            'no-params-short-array-3' => [
                'testMarker' => '/* testNoParamsShortArray3 */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'expected'   => false,
            ],
            'no-params-short-array-4' => [
                'testMarker' => '/* testNoParamsShortArray4 */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'expected'   => false,
            ],
            'has-params-long-array-1' => [
                'testMarker' => '/* testHasParamsLongArray1 */',
                'targetType' => \T_ARRAY,
                'expected'   => true,
            ],
            'has-params-long-array-2' => [
                'testMarker' => '/* testHasParamsLongArray2 */',
                'targetType' => \T_ARRAY,
                'expected'   => true,
            ],
            'has-params-long-array-3' => [
                'testMarker' => '/* testHasParamsLongArray3 */',
                'targetType' => \T_ARRAY,
                'expected'   => true,
            ],
            'has-params-short-array-1' => [
                'testMarker' => '/* testHasParamsShortArray1 */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'expected'   => true,
            ],
            'has-params-short-array-2' => [
                'testMarker' => '/* testHasParamsShortArray2 */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'expected'   => true,
            ],
            'has-params-short-array-3' => [
                'testMarker' => '/* testHasParamsShortArray3 */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
                'expected'   => true,
            ],

            // Isset.
            'no-params-isset' => [
                'testMarker' => '/* testNoParamsIsset */',
                'targetType' => \T_ISSET,
                'expected'   => false,
            ],
            'has-params-isset' => [
                'testMarker' => '/* testHasParamsIsset */',
                'targetType' => \T_ISSET,
                'expected'   => true,
            ],

            // Unset.
            'no-params-unset' => [
                'testMarker' => '/* testNoParamsUnset */',
                'targetType' => \T_UNSET,
                'expected'   => false,
            ],
            'has-params-unset' => [
                'testMarker' => '/* testHasParamsUnset */',
                'targetType' => \T_UNSET,
                'expected'   => true,
            ],

            // Exit/die.
            'exit as a constant' => [
                'testMarker' => '/* testExitAsConstant */',
                'targetType' => \T_EXIT,
                'expected'   => false,
            ],
            'die as a constant' => [
                'testMarker' => '/* testDieAsConstant */',
                'targetType' => \T_EXIT,
                'expected'   => false,
            ],
            'no-params-exit' => [
                'testMarker' => '/* testNoParamsExit */',
                'targetType' => \T_EXIT,
                'expected'   => false,
            ],
            'has-params-exit' => [
                'testMarker' => '/* testHasParamsExit */',
                'targetType' => \T_EXIT,
                'expected'   => true,
            ],
            'no-params-die' => [
                'testMarker' => '/* testNoParamsDie */',
                'targetType' => \T_EXIT,
                'expected'   => false,
            ],
            'has-params-die' => [
                'testMarker' => '/* testHasParamsDie */',
                'targetType' => \T_EXIT,
                'expected'   => true,
            ],

            // Anonymous class instantiation.
            'no-params-no-parens-anon-class' => [
                'testMarker' => '/* testNoParamsNoParensAnonClass */',
                'targetType' => \T_ANON_CLASS,
                'expected'   => false,
            ],
            'no-params-with-parens-anon-class' => [
                'testMarker' => '/* testNoParamsWithParensAnonClass */',
                'targetType' => \T_ANON_CLASS,
                'expected'   => false,
            ],
            'has-params-anon-class' => [
                'testMarker' => '/* testHasParamsAnonClass */',
                'targetType' => \T_ANON_CLASS,
                'expected'   => true,
            ],

            // Class instantiations in attribute.
            'has-params-class-instantiation-in-attribute' => [
                'testMarker' => '/* testHasParamsPHP80ClassInstantiationInAttribute */',
                'targetType' => \T_STRING,
                'expected'   => true,
            ],
            'no-params-class-instantiation-in-multi-attribute' => [
                'testMarker'    => '/* testHasParamsPHP80ClassInstantiationInMultiAttribute */',
                'targetType'    => \T_STRING,
                'expected'      => false,
                'targetContent' => 'AttributeOne',
            ],
            'has-params-class-instantiation-in-multi-attribute' => [
                'testMarker'    => '/* testHasParamsPHP80ClassInstantiationInMultiAttribute */',
                'targetType'    => ($php8Names === true) ? \T_NAME_FULLY_QUALIFIED : \T_STRING,
                'expected'      => true,
                'targetContent' => ($php8Names === true) ? '\AttributeTwo' : 'AttributeTwo',
            ],

            // PHP 8.1 first class callables are callbacks, not function calls.
            'no-params-php81-first-class-callable-global-function' => [
                'testMarker' => '/* testPHP81FirstClassCallableNotFunctionCallGlobalFunction */',
                'targetType' => \T_STRING,
                'expected'   => false,
            ],
            'no-params-php81-first-class-callable-oo-method' => [
                'testMarker' => '/* testPHP81FirstClassCallableNotFunctionCallOOMethod */',
                'targetType' => \T_STRING,
                'expected'   => false,
            ],
            'no-params-php81-first-class-callable-variable-static-oo-method' => [
                'testMarker'    => '/* testPHP81FirstClassCallableNotFunctionCallVariableStaticOOMethod */',
                'targetType'    => \T_VARIABLE,
                'expected'      => false,
                'targetContent' => '$name2',
            ],

            // Defensive coding against parse errors and live coding.
            'defense-in-depth-no-close-parens' => [
                'testMarker' => '/* testNoCloseParenthesis */',
                'targetType' => \T_ARRAY,
                'expected'   => false,
            ],
            'defense-in-depth-no-open-parens' => [
                'testMarker' => '/* testNoOpenParenthesis */',
                'targetType' => \T_STRING,
                'expected'   => false,
            ],
            'defense-in-depth-live-coding' => [
                'testMarker' => '/* testLiveCoding */',
                'targetType' => \T_ARRAY,
                'expected'   => false,
            ],
        ];
    }
}
