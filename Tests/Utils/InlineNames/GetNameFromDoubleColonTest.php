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
 * Tests for the \PHPCSUtils\Utils\InlineNames::getNameFromDoubleColon() method.
 *
 * @covers \PHPCSUtils\Utils\InlineNames::getNameFromDoubleColon
 *
 * @group inlinenames
 *
 * @since 1.0.0
 */
class GetNameFromDoubleColonTest extends UtilityMethodTestCase
{

    /**
     * Test receiving an expected exception when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_DOUBLE_COLON');
        InlineNames::getNameFromDoubleColon(self::$phpcsFile, 10000);
    }

    /**
     * Test receiving an expected exception when a non-NEW token is passed.
     *
     * @return void
     */
    public function testUnexpectedTokenException()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_DOUBLE_COLON');

        $target = $this->getTargetToken('/* testNotDoubleColon */', \T_ECHO);
        InlineNames::getNameFromDoubleColon(self::$phpcsFile, $target);
    }

    /**
     * Test retrieving the class/trait name used before a double colon.
     *
     * @dataProvider dataGetNameFromDoubleColon
     *
     * @param string $commentString The comment which prefaces the T_DOUBLE_COLON token in the test file.
     * @param string $expected      The expected function return value.
     *
     * @return void
     */
    public function testGetNameFromDoubleColon($commentString, $expected)
    {
        $stackPtr = $this->getTargetToken($commentString, \T_DOUBLE_COLON);
        $result   = InlineNames::getNameFromDoubleColon(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetNameFromDoubleColon() For the array format.
     *
     * @return array
     */
    public function dataGetNameFromDoubleColon()
    {
        return [
            'fqn' => [
                '/* testFQName */',
                '\Fully\Qualified\Name',
            ],
            'partially-qualified' => [
                '/* testPartiallyQualified */',
                'Partially\Qualified\Name',
            ],
            'partially-qualified-comments-whitespace' => [
                '/* testPartiallyQualifiedAndCommentsWhitespace */',
                'Partially\Qualified\Name',
            ],
            'unqualified' => [
                '/* testUnqualified */',
                'Name',
            ],
            'qualified' => [
                '/* testQualified */',
                '\Name',
            ],
            'namespace-operator-with-sub' => [
                '/* testNamespaceOperatorWithSub */',
                'namespace\Sub\Name',
            ],
            'namespace-operator' => [
                '/* testNamespaceOperator */',
                'namespace\Name',
            ],
            'self-keyword' => [
                '/* testSelf */',
                'self',
            ],
            'static-keyword' => [
                '/* testStatic */',
                'static',
            ],
            'parent-keyword' => [
                '/* testParent */',
                'PARENT',
            ],
            'variable-name' => [
                '/* testVariableName */',
                false,
            ],
            'variable-variable-name' => [
                '/* testVariableVariableName */',
                false,
            ],
        ];
    }
}
