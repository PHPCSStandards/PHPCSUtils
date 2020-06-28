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
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::getDeclarationName() method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::getDeclarationName
 *
 * @group objectdeclarations
 *
 * @since 1.0.0
 */
class GetDeclarationNameTest extends UtilityMethodTestCase
{

    /**
     * Test receiving an expected exception when a non-supported token is passed.
     *
     * @return void
     */
    public function testInvalidTokenPassed()
    {
        $this->expectPhpcsException('Token type "T_STRING" is not T_FUNCTION, T_CLASS, T_INTERFACE or T_TRAIT');

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
     * @return array
     */
    public function dataGetDeclarationNameNull()
    {
        return [
            'closure' => [
                '/* testClosure */',
                \T_CLOSURE,
            ],
            'anon-class-with-parentheses' => [
                '/* testAnonClassWithParens */',
                \T_ANON_CLASS,
            ],
            'anon-class-with-parentheses-2' => [
                '/* testAnonClassWithParens2 */',
                \T_ANON_CLASS,
            ],
            'anon-class-without-parentheses' => [
                '/* testAnonClassWithoutParens */',
                \T_ANON_CLASS,
            ],
            'anon-class-extends-without-parentheses' => [
                '/* testAnonClassExtendsWithoutParens */',
                \T_ANON_CLASS,
            ],

            /*
             * Note: this particular test *will* throw tokenizer "undefined offset" notices on PHPCS 2.6.0,
             * but the test will pass.
             */
            'live-coding' => [
                '/* testLiveCoding */',
                \T_FUNCTION,
            ],
        ];
    }

    /**
     * Test retrieving the name of a function or OO structure.
     *
     * @dataProvider dataGetDeclarationName
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param string     $expected   Expected function output.
     * @param int|string $targetType Token type of the token to get as stackPtr.
     *
     * @return void
     */
    public function testGetDeclarationName($testMarker, $expected, $targetType = null)
    {
        if (isset($targetType) === false) {
            $targetType = [\T_CLASS, \T_INTERFACE, \T_TRAIT, \T_FUNCTION];
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
     * @return array
     */
    public function dataGetDeclarationName()
    {
        return [
            'function' => [
                '/* testFunction */',
                'functionName',
            ],
            'function-return-by-reference' => [
                '/* testFunctionReturnByRef */',
                'functionNameByRef',
            ],
            'class' => [
                '/* testClass */',
                'ClassName',
            ],
            'method' => [
                '/* testMethod */',
                'methodName',
            ],
            'abstract-method' => [
                '/* testAbstractMethod */',
                'abstractMethodName',
            ],
            'method-return-by-reference' => [
                '/* testMethodReturnByRef */',
                'MethodNameByRef',
            ],
            'extended-class' => [
                '/* testExtendedClass */',
                'ExtendedClass',
            ],
            'interface' => [
                '/* testInterface */',
                'InterfaceName',
            ],
            'trait' => [
                '/* testTrait */',
                'TraitName',
            ],
            'function-name-ends-with-number' => [
                '/* testFunctionEndingWithNumber */',
                'ValidNameEndingWithNumber5',
            ],
            'class-with-numbers-in-name' => [
                '/* testClassWithNumber */',
                'ClassWith1Number',
            ],
            'interface-with-numbers-in-name' => [
                '/* testInterfaceWithNumbers */',
                'InterfaceWith12345Numbers',
            ],
            'class-with-comments-and-new-lines' => [
                '/* testClassWithCommentsAndNewLines */',
                'ClassWithCommentsAndNewLines',
            ],
            'function-named-fn' => [
                '/* testFunctionFn */',
                'fn',
            ],
        ];
    }
}
