<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Namespaces;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Namespaces;

/**
 * Tests for the \PHPCSUtils\Utils\Namespaces::isDeclaration(),
 * \PHPCSUtils\Utils\Namespaces::isOperator() and the.
 * \PHPCSUtils\Utils\Namespaces::getType() methods.
 *
 * @covers \PHPCSUtils\Utils\Namespaces::isDeclaration
 * @covers \PHPCSUtils\Utils\Namespaces::isOperator
 * @covers \PHPCSUtils\Utils\Namespaces::getType
 *
 * @group namespaces
 *
 * @since 1.0.0
 */
class NamespaceTypeTest extends UtilityMethodTestCase
{

    /**
     * Test receiving an expected exception when passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_NAMESPACE');

        Namespaces::getType(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an expected exception when passing a non T_NAMESPACE token.
     *
     * @return void
     */
    public function testNonNamespaceToken()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_NAMESPACE');

        Namespaces::getType(self::$phpcsFile, 0);
    }

    /**
     * Test whether a T_NAMESPACE token is used as the keyword for a namespace declaration.
     *
     * @dataProvider dataNamespaceType
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected output for the functions.
     * @param bool   $skipOnPHP8 Optional. Whether the test should be skipped when the PHP 8 identifier
     *                           name tokenization is used (as the target token won't exist).
     *                           Defaults to `false`.
     *
     * @return void
     */
    public function testIsDeclaration($testMarker, $expected, $skipOnPHP8 = false)
    {
        if ($skipOnPHP8 === true && parent::usesPhp8NameTokens() === true) {
            $this->markTestSkipped("PHP 8.0 identifier name tokenization used. Target token won't exist.");
        }

        $stackPtr = $this->getTargetToken($testMarker, \T_NAMESPACE);
        $result   = Namespaces::isDeclaration(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected['declaration'], $result);
    }

    /**
     * Test whether a T_NAMESPACE token is used as an operator.
     *
     * @dataProvider dataNamespaceType
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected output for the functions.
     * @param bool   $skipOnPHP8 Optional. Whether the test should be skipped when the PHP 8 identifier
     *                           name tokenization is used (as the target token won't exist).
     *                           Defaults to `false`.
     *
     * @return void
     */
    public function testIsOperator($testMarker, $expected, $skipOnPHP8 = false)
    {
        if ($skipOnPHP8 === true && parent::usesPhp8NameTokens() === true) {
            $this->markTestSkipped("PHP 8.0 identifier name tokenization used. Target token won't exist.");
        }

        $stackPtr = $this->getTargetToken($testMarker, \T_NAMESPACE);
        $result   = Namespaces::isOperator(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected['operator'], $result);
    }

    /**
     * Data provider.
     *
     * @see testIsDeclaration() For the array format.
     * @see testIsOperator()    For the array format.
     *
     * @return array
     */
    public function dataNamespaceType()
    {
        return [
            'namespace-declaration' => [
                '/* testNamespaceDeclaration */',
                [
                    'declaration' => true,
                    'operator'    => false,
                ],
            ],
            'namespace-declaration-with-comment' => [
                '/* testNamespaceDeclarationWithComment */',
                [
                    'declaration' => true,
                    'operator'    => false,
                ],
            ],
            'namespace-declaration-scoped' => [
                '/* testNamespaceDeclarationScoped */',
                [
                    'declaration' => true,
                    'operator'    => false,
                ],
            ],
            'namespace-operator' => [
                '/* testNamespaceOperator */',
                [
                    'declaration' => false,
                    'operator'    => true,
                ],
                true,
            ],
            'namespace-operator-with-annotation' => [
                '/* testNamespaceOperatorWithAnnotation */',
                [
                    'declaration' => false,
                    'operator'    => true,
                ],
            ],
            'namespace-operator-in-conditional' => [
                '/* testNamespaceOperatorInConditional */',
                [
                    'declaration' => false,
                    'operator'    => true,
                ],
                true,
            ],
            'namespace-operator-in-closed-scope' => [
                '/* testNamespaceOperatorInClosedScope */',
                [
                    'declaration' => false,
                    'operator'    => true,
                ],
                true,
            ],
            'namespace-operator-in-parentheses' => [
                '/* testNamespaceOperatorInParentheses */',
                [
                    'declaration' => false,
                    'operator'    => true,
                ],
                true,
            ],
            'namespace-operator-global-namespace-start-of-statement-function-call' => [
                '/* testNamespaceOperatorGlobalNamespaceStartOfStatementFunctionCall */',
                [
                    'declaration' => false,
                    'operator'    => true,
                ],
                true,
            ],
            'namespace-operator-global-namespace-start-of-statement-with-non-confusing-token-1' => [
                '/* testNamespaceOperatorGlobalNamespaceStartOfStatementCombiWithNonConfusingToken1 */',
                [
                    'declaration' => false,
                    'operator'    => true,
                ],
                true,
            ],
            'namespace-operator-global-namespace-start-of-statement-with-non-confusing-token-2' => [
                '/* testNamespaceOperatorGlobalNamespaceStartOfStatementCombiWithNonConfusingToken2 */',
                [
                    'declaration' => false,
                    'operator'    => true,
                ],
                true,
            ],
            'namespace-operator-global-namespace-start-of-statement-with-non-confusing-token-3' => [
                '/* testNamespaceOperatorGlobalNamespaceStartOfStatementCombiWithNonConfusingToken3 */',
                [
                    'declaration' => false,
                    'operator'    => true,
                ],
                true,
            ],
            'namespace-operator-global-namespace-start-of-statement-with-non-confusing-token-4' => [
                '/* testNamespaceOperatorGlobalNamespaceStartOfStatementCombiWithNonConfusingToken4 */',
                [
                    'declaration' => false,
                    'operator'    => true,
                ],
                true,
            ],
            'parse-error-scoped-namespace-declaration' => [
                '/* testParseErrorScopedNamespaceDeclaration */',
                [
                    'declaration' => false,
                    'operator'    => false,
                ],
            ],
            'parse-error-conditional-namespace' => [
                '/* testParseErrorConditionalNamespace */',
                [
                    'declaration' => false,
                    'operator'    => false,
                ],
            ],

            'fatal-error-declaration-leading-slash' => [
                '/* testFatalErrorDeclarationLeadingSlash */',
                [
                    'declaration' => false,
                    'operator'    => false,
                ],
            ],
            'parse-error-double-colon' => [
                '/* testParseErrorDoubleColon */',
                [
                    'declaration' => false,
                    'operator'    => false,
                ],
            ],
            'parse-error-semicolon' => [
                '/* testParseErrorSemiColon */',
                [
                    'declaration' => false,
                    'operator'    => false,
                ],
            ],
            'live-coding' => [
                '/* testLiveCoding */',
                [
                    'declaration' => false,
                    'operator'    => false,
                ],
            ],
        ];
    }
}
