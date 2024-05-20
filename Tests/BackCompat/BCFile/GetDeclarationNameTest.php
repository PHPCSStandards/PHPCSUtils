<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\BackCompat\BCFile;

use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\Tests\PolyfilledTestCase;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::getDeclarationName() method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::getDeclarationName
 *
 * @group objectdeclarations
 *
 * @since 1.0.0
 */
class GetDeclarationNameTest extends PolyfilledTestCase
{

    /**
     * Test receiving an expected exception when a non-supported token is passed.
     *
     * @return void
     */
    public function testInvalidTokenPassed()
    {
        $this->expectPhpcsException('Token type "T_STRING" is not T_FUNCTION, T_CLASS, T_INTERFACE, T_TRAIT or T_ENUM');

        $target = $this->getTargetToken('/* testInvalidTokenPassed */', \T_STRING);
        BCFile::getDeclarationName(self::$phpcsFile, $target);
    }

    /**
     * Test receiving "null" when passed an anonymous construct or in case of a parse error.
     *
     * @dataProvider dataGetDeclarationNameNull
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType Token type of the token to get as stackPtr.
     *
     * @return void
     */
    public function testGetDeclarationNameNull($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $result = BCFile::getDeclarationName(self::$phpcsFile, $target);
        $this->assertNull($result);
    }

    /**
     * Data provider.
     *
     * @see testGetDeclarationNameNull() For the array format.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataGetDeclarationNameNull()
    {
        return [
            'closure' => [
                'testMarker' => '/* testClosure */',
                'targetType' => \T_CLOSURE,
            ],
            'anon-class-with-parentheses' => [
                'testMarker' => '/* testAnonClassWithParens */',
                'targetType' => \T_ANON_CLASS,
            ],
            'anon-class-with-parentheses-2' => [
                'testMarker' => '/* testAnonClassWithParens2 */',
                'targetType' => \T_ANON_CLASS,
            ],
            'anon-class-without-parentheses' => [
                'testMarker' => '/* testAnonClassWithoutParens */',
                'targetType' => \T_ANON_CLASS,
            ],
            'anon-class-extends-without-parentheses' => [
                'testMarker' => '/* testAnonClassExtendsWithoutParens */',
                'targetType' => \T_ANON_CLASS,
            ],
            'live-coding' => [
                'testMarker' => '/* testLiveCoding */',
                'targetType' => \T_FUNCTION,
            ],
        ];
    }

    /**
     * Test retrieving the name of a function or OO structure.
     *
     * @dataProvider dataGetDeclarationName
     *
     * @param string          $testMarker The comment which prefaces the target token in the test file.
     * @param string          $expected   Expected function output.
     * @param int|string|null $targetType Token type of the token to get as stackPtr.
     *
     * @return void
     */
    public function testGetDeclarationName($testMarker, $expected, $targetType = null)
    {
        if (isset($targetType) === false) {
            $targetType = [\T_CLASS, \T_INTERFACE, \T_TRAIT, \T_ENUM, \T_FUNCTION];
        }

        $target = $this->getTargetToken($testMarker, $targetType);
        $result = BCFile::getDeclarationName(self::$phpcsFile, $target);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetDeclarationName() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataGetDeclarationName()
    {
        return [
            'function' => [
                'testMarker' => '/* testFunction */',
                'expected'   => 'functionName',
            ],
            'function-return-by-reference' => [
                'testMarker' => '/* testFunctionReturnByRef */',
                'expected'   => 'functionNameByRef',
            ],
            'class' => [
                'testMarker' => '/* testClass */',
                'expected'   => 'ClassName',
            ],
            'method' => [
                'testMarker' => '/* testMethod */',
                'expected'   => 'methodName',
            ],
            'abstract-method' => [
                'testMarker' => '/* testAbstractMethod */',
                'expected'   => 'abstractMethodName',
            ],
            'method-return-by-reference' => [
                'testMarker' => '/* testMethodReturnByRef */',
                'expected'   => 'MethodNameByRef',
            ],
            'extended-class' => [
                'testMarker' => '/* testExtendedClass */',
                'expected'   => 'ExtendedClass',
            ],
            'interface' => [
                'testMarker' => '/* testInterface */',
                'expected'   => 'InterfaceName',
            ],
            'trait' => [
                'testMarker' => '/* testTrait */',
                'expected'   => 'TraitName',
            ],
            'function-name-ends-with-number' => [
                'testMarker' => '/* testFunctionEndingWithNumber */',
                'expected'   => 'ValidNameEndingWithNumber5',
            ],
            'class-with-numbers-in-name' => [
                'testMarker' => '/* testClassWithNumber */',
                'expected'   => 'ClassWith1Number',
            ],
            'interface-with-numbers-in-name' => [
                'testMarker' => '/* testInterfaceWithNumbers */',
                'expected'   => 'InterfaceWith12345Numbers',
            ],
            'class-with-comments-and-new-lines' => [
                'testMarker' => '/* testClassWithCommentsAndNewLines */',
                'expected'   => 'ClassWithCommentsAndNewLines',
            ],
            'function-named-fn' => [
                'testMarker' => '/* testFunctionFn */',
                'expected'   => 'fn',
            ],
            'enum-pure' => [
                'testMarker' => '/* testPureEnum */',
                'expected'   => 'Foo',
            ],
            'enum-backed-space-between-name-and-colon' => [
                'testMarker' => '/* testBackedEnumSpaceBetweenNameAndColon */',
                'expected'   => 'Hoo',
            ],
            'enum-backed-no-space-between-name-and-colon' => [
                'testMarker' => '/* testBackedEnumNoSpaceBetweenNameAndColon */',
                'expected'   => 'Suit',
            ],
            'function-return-by-reference-with-reserved-keyword-each' => [
                'testMarker' => '/* testFunctionReturnByRefWithReservedKeywordEach */',
                'expected'   => 'each',
            ],
            'function-return-by-reference-with-reserved-keyword-parent' => [
                'testMarker' => '/* testFunctionReturnByRefWithReservedKeywordParent */',
                'expected'   => 'parent',
            ],
            'function-return-by-reference-with-reserved-keyword-self' => [
                'testMarker' => '/* testFunctionReturnByRefWithReservedKeywordSelf */',
                'expected'   => 'self',
            ],
            'function-return-by-reference-with-reserved-keyword-static' => [
                'testMarker' => '/* testFunctionReturnByRefWithReservedKeywordStatic */',
                'expected'   => 'static',
            ],
        ];
    }
}
