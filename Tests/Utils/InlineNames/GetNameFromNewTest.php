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
 * Tests for the \PHPCSUtils\Utils\InlineNames::getNameFromNew() method.
 *
 * @covers \PHPCSUtils\Utils\InlineNames::getNameFromNew
 * @covers \PHPCSUtils\Utils\InlineNames::getNameAfterKeyword
 *
 * @group inlinenames
 *
 * @since 1.0.0
 */
class GetNameFromNewTest extends UtilityMethodTestCase
{

    /**
     * Test receiving an expected exception when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_NEW');
        InlineNames::getNameFromNew(self::$phpcsFile, 10000);
    }

    /**
     * Test receiving an expected exception when a non-NEW token is passed.
     *
     * @return void
     */
    public function testUnexpectedTokenException()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_NEW');

        $target = $this->getTargetToken('/* testNotNew */', \T_ECHO);
        InlineNames::getNameFromNew(self::$phpcsFile, $target);
    }

    /**
     * Test retrieving the name used in an object instantiation.
     *
     * @dataProvider dataGetNameFromNew
     *
     * @param string $commentString The comment which prefaces the T_NEW token in the test file.
     * @param string $expected      The expected function return value.
     *
     * @return void
     */
    public function testGetNameFromNew($commentString, $expected)
    {
        $stackPtr = $this->getTargetToken($commentString, \T_NEW);
        $result   = InlineNames::getNameFromNew(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetNameFromNew() For the array format.
     *
     * @return array
     */
    public static function dataGetNameFromNew()
    {
        return [
            'anon-class-no-parens' => [
                '/* testAnonClassWithoutParentheses */',
                '',
            ],
            'anon-class-with-parens' => [
                '/* testAnonClassWithParentheses */',
                '',
            ],
            'fqn-without-parens' => [
                '/* testFQNWithoutParentheses */',
                '\Fully\Qualified\Name',
            ],
            'fqn-with-parens' => [
                '/* testFQNWithParentheses */',
                '\Fully\Qualified\Name',
            ],
            'partially-qualified-without-parens' => [
                '/* testPartiallyQualifiedWithoutParentheses */',
                'Partially\Qualified\Name',
            ],
            'partially-qualified-with-parens' => [
                '/* testPartiallyQualifiedWithParenthesesAndCommentsWhitespace */',
                'Partially\Qualified\Name',
            ],
            'unqualified-without-parens' => [
                '/* testUnqualifiedWithoutParentheses */',
                'Name',
            ],
            'qualified-with-parens' => [
                '/* testQualifiedWithParentheses */',
                '\Name',
            ],
            'namespace-operator-without-parens' => [
                '/* testNamespaceOperatorWithoutParentheses */',
                'namespace\Sub\Name',
            ],
            'namespace-operator-with-parens' => [
                '/* testNamespaceOperatorWithParentheses */',
                'namespace\Name',
            ],
            'self-keyword-with-parens' => [
                '/* testSelfWithParentheses */',
                'SELF',
            ],
            'self-keyword-phpcs-bug-1245' => [
                '/* testSelfReturnPHPCS1245 */',
                'self',
            ],
            'static-keyword-without-parens' => [
                '/* testStaticWithoutParentheses */',
                'static',
            ],
            'parent-keyword-with-parens' => [
                '/* testParentWithParentheses */',
                'parent',
            ],
            'variable-name-with-parens' => [
                '/* testVariableClassNameWithParentheses */',
                false,
            ],
            'variable-variable-name-with-parens' => [
                '/* testVariableVariableClassNameWithParentheses */',
                false,
            ],
            'static-property-name-with-parens' => [
                '/* testStaticPropertyClassNameWithParentheses */',
                false,
            ],
            'self-constant-name-with-parens' => [
                '/* testSelfConstantClassNameWithParentheses */',
                false,
            ],
            'live-coding' => [
                '/* testLiveCoding */',
                false,
            ],
        ];
    }
}
