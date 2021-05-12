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
use PHPCSUtils\Utils\Operators;

/**
 * Tests for the \PHPCSUtils\Utils\Operators::isTypeUnion() method.
 *
 * @covers \PHPCSUtils\Utils\Operators::isTypeUnion
 *
 * @group operators
 *
 * @since 1.0.0
 */
class IsTypeUnionTest extends UtilityMethodTestCase
{

    /**
     * Test that false is returned when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(Operators::isTypeUnion(self::$phpcsFile, 10000));
    }

    /**
     * Test that false is returned when a non-bitwise or token is passed.
     *
     * @return void
     */
    public function testNotBitwiseOrToken()
    {
        $target = $this->getTargetToken('/* testNotBitwiseOrToken */', \T_ECHO);
        $this->assertFalse(Operators::isTypeUnion(self::$phpcsFile, $target));
    }

    /**
     * Test whether a type union separator is correctly identified as such.
     *
     * @dataProvider dataIsTypeUnion
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @return void
     */
    public function testIsTypeUnion($testMarker)
    {
        $targets = [\T_BITWISE_OR];
        if (\defined('T_TYPE_UNION') === true) {
            $targets[] = \T_TYPE_UNION;
        }

        $stackPtr = $this->getTargetToken($testMarker, $targets);

        $this->assertTrue(Operators::isTypeUnion(self::$phpcsFile, $stackPtr));
    }

    /**
     * Data provider.
     *
     * @see testIsTypeUnion()
     *
     * @return array
     */
    public function dataIsTypeUnion()
    {
        return [
            'property'                        => ['/* testTypeUnionPropertySimple */'],
            'property-reverse-modifier-order' => ['/* testTypeUnionPropertyReverseModifierOrder */'],
            'property-multi-type-1'           => ['/* testTypeUnionPropertyMulti1 */'],
            'property-multi-type-2'           => ['/* testTypeUnionPropertyMulti2 */'],
            'property-multi-type-3'           => ['/* testTypeUnionPropertyMulti3 */'],
            'property-namespace-operator'     => ['/* testTypeUnionPropertyNamespaceRelative */'],
            'property-partially-qualified'    => ['/* testTypeUnionPropertyPartiallyQualified */'],
            'property-fully-qualified'        => ['/* testTypeUnionPropertyFullyQualified */'],
            'parameter-multi-type-1'          => ['/* testTypeUnionParam1 */'],
            'parameter-multi-type-2'          => ['/* testTypeUnionParam2 */'],
            'parameter-multi-type-3'          => ['/* testTypeUnionParam3 */'],
            'parameter-namespace-operator'    => ['/* testTypeUnionParamNamespaceRelative */'],
            'parameter-partially-qualified'   => ['/* testTypeUnionParamPartiallyQualified */'],
            'parameter-fully-qualified'       => ['/* testTypeUnionParamFullyQualified */'],
            'return'                          => ['/* testTypeUnionReturnType */'],
            'constructor-property-promotion'  => ['/* testTypeUnionConstructorPropertyPromotion */'],
            'return-abstract-method-1'        => ['/* testTypeUnionAbstractMethodReturnType1 */'],
            'return-abstract-method-2'        => ['/* testTypeUnionAbstractMethodReturnType2 */'],
            'return-namespace-operator'       => ['/* testTypeUnionReturnTypeNamespaceRelative */'],
            'return-partially-qualified'      => ['/* testTypeUnionReturnPartiallyQualified */'],
            'return-fully-qualified'          => ['/* testTypeUnionReturnFullyQualified */'],
            'parameter-closure-with-nullable' => ['/* testTypeUnionClosureParamIllegalNullable */'],
            'parameter-with-reference'        => ['/* testTypeUnionWithReference */'],
            'parameter-with-spread-operator'  => ['/* testTypeUnionWithSpreadOperator */'],
            'return-closure'                  => ['/* testTypeUnionClosureReturn */'],
            'parameter-arrow'                 => ['/* testTypeUnionArrowParam */'],
            'return-arrow'                    => ['/* testTypeUnionArrowReturnType */'],
            'parameter-non-arrow-fn-decl'     => ['/* testTypeUnionNonArrowFunctionDeclaration */'],
            'return-ternary-nested-arrow-1'   => ['/* testTypeUnionInTernaryNestedArrowFunction1 */'],
            'return-ternary-nested-arrow-2'   => ['/* testTypeUnionInTernaryNestedArrowFunction2 */'],
            'return-ternary-nested-arrow-3'   => ['/* testTypeUnionInTernaryNestedArrowFunction3 */'],
        ];
    }

    /**
     * Test whether a real "bitwise or" is correctly identified as such.
     *
     * @dataProvider dataBitwiseOr
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @return void
     */
    public function testBitwiseOr($testMarker)
    {
        $targets = [\T_BITWISE_OR];
        if (\defined('T_TYPE_UNION') === true) {
            $targets[] = \T_TYPE_UNION;
        }

        $stackPtr = $this->getTargetToken($testMarker, $targets);

        $this->assertFalse(Operators::isTypeUnion(self::$phpcsFile, $stackPtr));
    }

    /**
     * Data provider.
     *
     * @see testBitwiseOr()
     *
     * @return array
     */
    public function dataBitwiseOr()
    {
        return [
            'bitwiseor-1'                     => ['/* testBitwiseOr1 */'],
            'bitwiseor-2'                     => ['/* testBitwiseOr2 */'],
            'bitwiseor-property-default'      => ['/* testBitwiseOrPropertyDefaultValue */'],
            'bitwiseor-param-default'         => ['/* testBitwiseOrParamDefaultValue */'],
            'bitwiseor-3'                     => ['/* testBitwiseOr3 */'],
            'bitwiseor-closure-param-default' => ['/* testBitwiseOrClosureParamDefault */'],
            'bitwiseor-arrow-param-default'   => ['/* testBitwiseOrArrowParamDefault */'],
            'bitwiseor-arrow-expression'      => ['/* testBitwiseOrArrowExpression */'],
            'bitwiseor-in-array-key'          => ['/* testBitwiseOrInArrayKey */'],
            'bitwiseor-in-array-value'        => ['/* testBitwiseOrInArrayValue */'],
            'bitwiseor-in-short-array-key'    => ['/* testBitwiseOrInShortArrayKey */'],
            'bitwiseor-in-short-array-value'  => ['/* testBitwiseOrInShortArrayValue */'],
            'bitwiseor-in-try-catch'          => ['/* testBitwiseOrTryCatch */'],
            'bitwiseor-in-non-arrow-fn-call'  => ['/* testBitwiseOrNonArrowFnFunctionCall */'],
            'bitwiseor-in-nested-ternary'     => ['/* testBitwiseOrInNestedTernaryPhpcsLt291 */'],
            'bitwiseor-in-ternary-arrow-fn'   => ['/* testBitwiseOrInTernaryNestedArrowFunction */'],
            'live-coding'                     => ['/* testLiveCoding */'],
        ];
    }
}
