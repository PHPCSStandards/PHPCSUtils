<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Operators;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\PassedParameters;

/**
 * Tests for the \PHPCSUtils\Utils\PassedParameters::hasParameters() method.
 *
 * @covers \PHPCSUtils\Utils\PassedParameters::hasParameters
 *
 * @group passedparameters
 *
 * @since 1.0.0
 */
class HasParametersTest extends UtilityMethodTestCase
{

    /**
     * Test receiving an expected exception when an invalid token pointer is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException(
            'The hasParameters() method expects a function call, array, isset or unset token to be passed'
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
        $this->expectPhpcsException(
            'The hasParameters() method expects a function call, array, isset or unset token to be passed.'
        );

        $interface = $this->getTargetToken('/* testNotAnAcceptedToken */', \T_INTERFACE);
        PassedParameters::hasParameters(self::$phpcsFile, $interface);
    }

    /**
     * Test receiving an expected exception when T_SELF is passed not preceeded by `new`.
     *
     * @return void
     */
    public function testNotACallToConstructor()
    {
        $this->expectPhpcsException(
            'The hasParameters() method expects a function call, array, isset or unset token to be passed.'
        );

        $self = $this->getTargetToken('/* testNotACallToConstructor */', \T_SELF);
        PassedParameters::hasParameters(self::$phpcsFile, $self);
    }

    /**
     * Test receiving an expected exception when T_OPEN_SHORT_ARRAY is passed but represents a short list.
     *
     * @return void
     */
    public function testNotAShortArray()
    {
        $this->expectPhpcsException(
            'The hasParameters() method expects a function call, array, isset or unset token to be passed.'
        );

        $self = $this->getTargetToken(
            '/* testShortListNotShortArray */',
            [\T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]
        );
        PassedParameters::hasParameters(self::$phpcsFile, $self);
    }

    /**
     * Test correctly identifying whether parameters were passed to a function call or construct.
     *
     * @dataProvider dataHasParameters
     *
     * @param string     $testMarker    The comment which prefaces the target token in the test file.
     * @param int|string $targetType    The type of token to look for.
     * @param bool       $expected      Whether or not the function/array has parameters/values.
     * @param string     $targetContent Optional. The content of the target token to find.
     *                                  Defaults to null (ignore content).
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
     * @return array
     */
    public function dataHasParameters()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            // Function calls.
            'no-params-function-call-1' => [
                '/* testNoParamsFunctionCall1 */',
                \T_STRING,
                false,
            ],
            'no-params-function-call-2' => [
                '/* testNoParamsFunctionCall2 */',
                \T_STRING,
                false,
            ],
            'no-params-function-call-3' => [
                '/* testNoParamsFunctionCall3 */',
                \T_STRING,
                false,
            ],
            'no-params-function-call-4' => [
                '/* testNoParamsFunctionCall4 */',
                \T_VARIABLE,
                false,
            ],
            'has-params-function-call-1' => [
                '/* testHasParamsFunctionCall1 */',
                \T_STRING,
                true,
            ],
            'has-params-function-call-2' => [
                '/* testHasParamsFunctionCall2 */',
                \T_VARIABLE,
                true,
            ],
            'has-params-function-call-3' => [
                '/* testHasParamsFunctionCall3 */',
                // In PHPCS < 2.8.0, self in "new self" is tokenized as T_STRING.
                [\T_SELF, \T_STRING],
                true,
            ],
            'no-params-function-call-fully-qualified' => [
                '/* testNoParamsFunctionCallFullyQualified */',
                ($php8Names === true) ? \T_NAME_FULLY_QUALIFIED : \T_STRING,
                false,
                ($php8Names === true) ? null : 'myfunction',
            ],
            'has-params-function-call-fully-qualified-with-namespace' => [
                '/* testHasParamsFunctionCallFullyQualifiedWithNamespace */',
                ($php8Names === true) ? \T_NAME_FULLY_QUALIFIED : \T_STRING,
                true,
                ($php8Names === true) ? null : 'myfunction',
            ],
            'no-params-function-call-partially-qualified' => [
                '/* testNoParamsFunctionCallPartiallyQualified */',
                ($php8Names === true) ? \T_NAME_QUALIFIED : \T_STRING,
                false,
                ($php8Names === true) ? null : 'myfunction',
            ],
            'has-params-function-call-namespace-operator-relative' => [
                '/* testHasParamsFunctionCallNamespaceOperator */',
                ($php8Names === true) ? \T_NAME_RELATIVE : \T_STRING,
                true,
                ($php8Names === true) ? null : 'myfunction',
            ],

            // Arrays.
            'no-params-long-array-1' => [
                '/* testNoParamsLongArray1 */',
                \T_ARRAY,
                false,
            ],
            'no-params-long-array-2' => [
                '/* testNoParamsLongArray2 */',
                \T_ARRAY,
                false,
            ],
            'no-params-long-array-3' => [
                '/* testNoParamsLongArray3 */',
                \T_ARRAY,
                false,
            ],
            'no-params-long-array-4' => [
                '/* testNoParamsLongArray4 */',
                \T_ARRAY,
                false,
            ],
            'no-params-short-array-1' => [
                '/* testNoParamsShortArray1 */',
                \T_OPEN_SHORT_ARRAY,
                false,
            ],
            'no-params-short-array-2' => [
                '/* testNoParamsShortArray2 */',
                \T_OPEN_SHORT_ARRAY,
                false,
            ],
            'no-params-short-array-3' => [
                '/* testNoParamsShortArray3 */',
                \T_OPEN_SHORT_ARRAY,
                false,
            ],
            'no-params-short-array-4' => [
                '/* testNoParamsShortArray4 */',
                \T_OPEN_SHORT_ARRAY,
                false,
            ],
            'has-params-long-array-1' => [
                '/* testHasParamsLongArray1 */',
                \T_ARRAY,
                true,
            ],
            'has-params-long-array-2' => [
                '/* testHasParamsLongArray2 */',
                \T_ARRAY,
                true,
            ],
            'has-params-long-array-3' => [
                '/* testHasParamsLongArray3 */',
                \T_ARRAY,
                true,
            ],
            'has-params-short-array-1' => [
                '/* testHasParamsShortArray1 */',
                \T_OPEN_SHORT_ARRAY,
                true,
            ],
            'has-params-short-array-2' => [
                '/* testHasParamsShortArray2 */',
                \T_OPEN_SHORT_ARRAY,
                true,
            ],
            'has-params-short-array-3' => [
                '/* testHasParamsShortArray3 */',
                \T_OPEN_SHORT_ARRAY,
                true,
            ],

            // Isset.
            'no-params-isset' => [
                '/* testNoParamsIsset */',
                \T_ISSET,
                false,
            ],
            'has-params-isset' => [
                '/* testHasParamsIsset */',
                \T_ISSET,
                true,
            ],

            // Unset.
            'no-params-unset' => [
                '/* testNoParamsUnset */',
                \T_UNSET,
                false,
            ],
            'has-params-unset' => [
                '/* testHasParamsUnset */',
                \T_UNSET,
                true,
            ],

            // Defensive coding against parse errors and live coding.
            'defense-in-depth-no-close-parens' => [
                '/* testNoCloseParenthesis */',
                \T_ARRAY,
                false,
            ],
            'defense-in-depth-no-open-parens' => [
                '/* testNoOpenParenthesis */',
                \T_STRING,
                false,
            ],
            'defense-in-depth-live-coding' => [
                '/* testLiveCoding */',
                \T_ARRAY,
                false,
            ],
        ];
    }
}
