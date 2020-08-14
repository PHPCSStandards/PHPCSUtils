<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\InlineNames;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\InlineNames;

/**
 * Tests for the \PHPCSUtils\Utils\InlineNames::getNameFromInstanceOf() method.
 *
 * @covers \PHPCSUtils\Utils\InlineNames::getNameFromInstanceOf
 * @covers \PHPCSUtils\Utils\InlineNames::getNameAfterKeyword
 *
 * @group inlinenames
 *
 * @since 1.0.0
 */
class GetNameFromInstanceOfTest extends UtilityMethodTestCase
{

    /**
     * Test receiving an expected exception when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_INSTANCEOF');
        InlineNames::getNameFromInstanceOf(self::$phpcsFile, 10000);
    }

    /**
     * Test receiving an expected exception when a non-INSTANCEOF token is passed.
     *
     * @return void
     */
    public function testUnexpectedTokenException()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_INSTANCEOF');

        $target = $this->getTargetToken('/* testNotInstanceOf */', \T_ECHO);
        InlineNames::getNameFromInstanceOf(self::$phpcsFile, $target);
    }

    /**
     * Test retrieving the name used in comparison with the instanceof operator.
     *
     * @dataProvider dataGetNameFromInstanceOf
     *
     * @param string $commentString The comment which prefaces the T_INSTANCEOF token in the test file.
     * @param string $expected      The expected function return value.
     *
     * @return void
     */
    public function testGetNameFromInstanceOf($commentString, $expected)
    {
        $stackPtr = $this->getTargetToken($commentString, \T_INSTANCEOF);
        $result   = InlineNames::getNameFromInstanceOf(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetNameFromInstanceOf() For the array format.
     *
     * @return array
     */
    public function dataGetNameFromInstanceOf()
    {
        return [
            'unqualified-name' => [
                '/* testUnqualifiedName */',
                'Name',
            ],
            'qualified-name-with-comments' => [
                '/* testQualifiedNameAndComments */',
                '\Name',
            ],
            'partially-qualified-name' => [
                '/* testPartiallyQualifiedName */',
                'Partially\Qualified\Name',
            ],
            'partially-qualified-name-with-comments-and-whitespace' => [
                '/* testPartiallyQualifiedWithParenthesesAndCommentsWhitespace */',
                'Partially\Qualified\Name',
            ],
            'fully-qualified-name' => [
                '/* testFullyQualifiedName */',
                '\Fully\Qualified\Name',
            ],
            'namespace-operator' => [
                '/* testNamespaceOperator */',
                'namespace\Name',
            ],
            'namespace-operator-with-sublevel' => [
                '/* testNamespaceOperatorWithSubLevel */',
                'namespace\Sub\Name',
            ],
            'unqualified-name-with-class-constant' => [
                '/* testUnqualifiedNameWithClassModifier */',
                'Name',
            ],
            'self' => [
                '/* testSelf */',
                'self',
            ],
            'parent' => [
                '/* testParent */',
                'parent',
            ],
            'static' => [
                '/* testStatic */',
                'static',
            ],
            'string-name' => [
                '/* testStringName */',
                'Name',
            ],
            'magic-namespace-constant-with-string-name' => [
                '/* testMagicConstantWithStringName */',
                'namespace\Name',
            ],
            'magic-namespace-constant-with-variable-name' => [
                '/* testMagicConstantWithVariableName */',
                false,
            ],
            'magic-namespace-constant-with-double-quoted-variable-name' => [
                '/* testMagicConstantWithDoubleQuotedStringVariableName */',
                false,
            ],
            'anon-class' => [
                '/* testAnonClass */',
                false,
            ],
            'variable-name' => [
                '/* testVariableName */',
                false,
            ],
            'variable-variable-name' => [
                '/* testVariableVariableName */',
                false,
            ],
            'variable-property-name' => [
                '/* testVariablePropertyName */',
                false,
            ],
            'static-property-name' => [
                '/* testStaticPropertyName */',
                false,
            ],
            'class-constant-name' => [
                '/* testClassConstantName */',
                false,
            ],
            'self-property-name' => [
                '/* testSelfPropertyClassName */',
                false,
            ],
            'parent-constant-name' => [
                '/* testParentConstantClassName */',
                false,
            ],
            'static-property-class-name' => [
                '/* testStaticPropertyClassName */',
                false,
            ],
            'self-in-array-key' => [
                '/* testSelfInArrayKey */',
                false,
            ],
            'live-coding' => [
                '/* testLiveCoding */',
                false,
            ],
        ];
    }
}
