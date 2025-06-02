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

use PHPCSUtils\Exceptions\OutOfBoundsStackPtr;
use PHPCSUtils\Exceptions\TypeError;
use PHPCSUtils\Exceptions\UnexpectedTokenType;
use PHPCSUtils\Tests\PolyfilledTestCase;
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
 * @since 1.0.0
 */
final class NamespaceTypeTest extends PolyfilledTestCase
{

    /**
     * Test receiving an expected exception when passing a non-integer token pointer.
     *
     * @return void
     */
    public function testNonIntegerToken()
    {
        $this->expectException(TypeError::class);
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type integer, boolean given');

        Namespaces::getType(self::$phpcsFile, false);
    }

    /**
     * Test receiving an expected exception when passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectException(OutOfBoundsStackPtr::class);
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object, 100000 given'
        );

        Namespaces::getType(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an expected exception when passing a non T_NAMESPACE token.
     *
     * @return void
     */
    public function testNonNamespaceToken()
    {
        $this->expectException(UnexpectedTokenType::class);
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be of type T_NAMESPACE;'
        );

        Namespaces::getType(self::$phpcsFile, 0);
    }

    /**
     * Test whether a T_NAMESPACE token is used as the keyword for a namespace declaration.
     *
     * @dataProvider dataNamespaceType
     *
     * @param string              $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, bool> $expected   The expected output for the functions.
     *
     * @return void
     */
    public function testIsDeclaration($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_NAMESPACE);
        $result   = Namespaces::isDeclaration(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected['declaration'], $result);
    }

    /**
     * Test whether a T_NAMESPACE token is used as an operator.
     *
     * @dataProvider dataNamespaceType
     *
     * @param string              $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, bool> $expected   The expected output for the functions.
     *
     * @return void
     */
    public function testIsOperator($testMarker, $expected)
    {
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
     * @return array<string, array<string, string|array<string, bool>|true>>
     */
    public static function dataNamespaceType()
    {
        return [
            'namespace-declaration' => [
                'testMarker' => '/* testNamespaceDeclaration */',
                'expected'   => [
                    'declaration' => true,
                    'operator'    => false,
                ],
            ],
            'namespace-declaration-with-comment' => [
                'testMarker' => '/* testNamespaceDeclarationWithComment */',
                'expected'   => [
                    'declaration' => true,
                    'operator'    => false,
                ],
            ],
            'namespace-declaration-scoped' => [
                'testMarker' => '/* testNamespaceDeclarationScoped */',
                'expected'   => [
                    'declaration' => true,
                    'operator'    => false,
                ],
            ],
            'namespace-operator-with-annotation' => [
                'testMarker' => '/* testNamespaceOperatorWithAnnotation */',
                'expected'   => [
                    'declaration' => false,
                    'operator'    => true,
                ],
            ],
            'parse-error-scoped-namespace-declaration' => [
                'testMarker' => '/* testParseErrorScopedNamespaceDeclaration */',
                'expected'   => [
                    'declaration' => false,
                    'operator'    => false,
                ],
            ],
            'parse-error-conditional-namespace' => [
                'testMarker' => '/* testParseErrorConditionalNamespace */',
                'expected'   => [
                    'declaration' => false,
                    'operator'    => false,
                ],
            ],

            'fatal-error-declaration-leading-slash' => [
                'testMarker' => '/* testFatalErrorDeclarationLeadingSlash */',
                'expected'   => [
                    'declaration' => false,
                    'operator'    => false,
                ],
            ],
            'parse-error-double-colon' => [
                'testMarker' => '/* testParseErrorDoubleColon */',
                'expected'   => [
                    'declaration' => false,
                    'operator'    => false,
                ],
            ],
            'parse-error-semicolon' => [
                'testMarker' => '/* testParseErrorSemiColon */',
                'expected'   => [
                    'declaration' => false,
                    'operator'    => false,
                ],
            ],
            'live-coding' => [
                'testMarker' => '/* testLiveCoding */',
                'expected'   => [
                    'declaration' => false,
                    'operator'    => false,
                ],
            ],
        ];
    }
}
