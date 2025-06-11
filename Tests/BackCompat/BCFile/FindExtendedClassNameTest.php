<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 *
 * This class is imported from the PHP_CodeSniffer project.
 *
 * Copyright of the original code in this class as per the import:
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @author    Juliette Reinders Folmer <jrf@phpcodesniffer.info>
 * @author    Martin Hujer <mhujer@gmail.com>
 *
 * With documentation contributions from:
 * @author    George Mponos <gmponos@gmail.com>
 * @author    Phil Davis <phil@jankaritech.com>
 *
 * @copyright 2016-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */

namespace PHPCSUtils\Tests\BackCompat\BCFile;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::findExtendedClassName method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::findExtendedClassName
 *
 * @group objectdeclarations
 *
 * @since 1.0.0
 */
class FindExtendedClassNameTest extends UtilityMethodTestCase
{

    /**
     * The fully qualified name of the class being tested.
     *
     * This allows for the same unit tests to be run for both the BCFile functions
     * as well as for the related PHPCSUtils functions.
     *
     * @var string
     */
    const TEST_CLASS = '\PHPCSUtils\BackCompat\BCFile';

    /**
     * Test getting a `false` result when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $testClass = static::TEST_CLASS;

        $result = $testClass::findExtendedClassName(self::$phpcsFile, 100000);
        $this->assertFalse($result);
    }

    /**
     * Test getting a `false` result when a token other than one of the supported tokens is passed.
     *
     * @return void
     */
    public function testNotAClass()
    {
        $testClass = static::TEST_CLASS;

        $token  = $this->getTargetToken('/* testNotAClass */', [T_FUNCTION]);
        $result = $testClass::findExtendedClassName(self::$phpcsFile, $token);
        $this->assertFalse($result);
    }

    /**
     * Test retrieving the name of the class being extended by another class
     * (or interface).
     *
     * @dataProvider dataExtendedClass
     *
     * @param string       $identifier Comment which precedes the test case.
     * @param string|false $expected   Expected function output.
     *
     * @return void
     */
    public function testFindExtendedClassName($identifier, $expected)
    {
        $testClass = static::TEST_CLASS;

        $OOToken = $this->getTargetToken($identifier, [T_CLASS, T_ANON_CLASS, T_INTERFACE]);
        $result  = $testClass::findExtendedClassName(self::$phpcsFile, $OOToken);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testFindExtendedClassName()
     *
     * @return array<string, array<string, string|false>>
     */
    public static function dataExtendedClass()
    {
        return [
            'class does not extend' => [
                'identifier' => '/* testNonExtendedClass */',
                'expected'   => false,
            ],
            'class extends unqualified class' => [
                'identifier' => '/* testExtendsUnqualifiedClass */',
                'expected'   => 'testFECNClass',
            ],
            'class extends fully qualified class' => [
                'identifier' => '/* testExtendsFullyQualifiedClass */',
                'expected'   => '\PHP_CodeSniffer\Tests\Core\File\testFECNClass',
            ],
            'class extends partially qualified class' => [
                'identifier' => '/* testExtendsPartiallyQualifiedClass */',
                'expected'   => 'Core\File\RelativeClass',
            ],
            'class extends namespace relative class' => [
                'identifier' => '/* testExtendsNamespaceRelativeClass */',
                'expected'   => 'namespace\Bar',
            ],
            'interface does not extend' => [
                'identifier' => '/* testNonExtendedInterface */',
                'expected'   => false,
            ],
            'interface extends unqualified interface' => [
                'identifier' => '/* testInterfaceExtendsUnqualifiedInterface */',
                'expected'   => 'testFECNInterface',
            ],
            'interface extends fully qualified interface' => [
                'identifier' => '/* testInterfaceExtendsFullyQualifiedInterface */',
                'expected'   => '\PHP_CodeSniffer\Tests\Core\File\testFECNInterface',
            ],
            'anon class extends unqualified class' => [
                'identifier' => '/* testExtendedAnonClass */',
                'expected'   => 'testFECNExtendedAnonClass',
            ],
            'class does not extend but contains anon class which extends' => [
                'identifier' => '/* testNestedExtendedClass */',
                'expected'   => false,
            ],
            'anon class extends, nested in non-extended class' => [
                'identifier' => '/* testNestedExtendedAnonClass */',
                'expected'   => 'testFECNAnonClass',
            ],
            'class extends and implements' => [
                'identifier' => '/* testClassThatExtendsAndImplements */',
                'expected'   => 'testFECNClass',
            ],
            'class implements and extends' => [
                'identifier' => '/* testClassThatImplementsAndExtends */',
                'expected'   => 'testFECNClass',
            ],
            'interface extends multiple interfaces (not supported)' => [
                'identifier' => '/* testInterfaceMultiExtends */',
                'expected'   => '\Package\FooInterface',
            ],
            'readonly anon class extends fully qualified class'           => [
                'identifier' => '/* testExtendedReadonlyAnonClass */',
                'expected'   => '\Fully\Qualified\MyClass',
            ],
            'anon class with attribute extends partially qualified class' => [
                'identifier' => '/* testExtendedAnonClassWithAttributes */',
                'expected'   => 'Partially\Qualified\MyClass',
            ],
            'parse error - extends keyword, but no class name' => [
                'identifier' => '/* testMissingExtendsName */',
                'expected'   => false,
            ],
            'parse error - live coding - no curly braces' => [
                'identifier' => '/* testParseError */',
                'expected'   => false,
            ],
        ];
    }
}
