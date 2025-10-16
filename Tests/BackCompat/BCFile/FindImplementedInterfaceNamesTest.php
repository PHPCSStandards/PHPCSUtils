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
 * Tests for the \PHPCSUtils\BackCompat\BCFile::findImplementedInterfaceNames method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::findImplementedInterfaceNames
 *
 * @group objectdeclarations
 *
 * @since 1.0.0
 */
class FindImplementedInterfaceNamesTest extends UtilityMethodTestCase
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

        $result = $testClass::findImplementedInterfaceNames(self::$phpcsFile, 100000);
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
        $result = $testClass::findImplementedInterfaceNames(self::$phpcsFile, $token);
        $this->assertFalse($result);
    }

    /**
     * Test retrieving the name(s) of the interfaces being implemented by a class.
     *
     * @dataProvider dataImplementedInterface
     *
     * @param string              $identifier Comment which precedes the test case.
     * @param array<string>|false $expected   Expected function output.
     *
     * @return void
     */
    public function testFindImplementedInterfaceNames($identifier, $expected)
    {
        $testClass = static::TEST_CLASS;

        $OOToken = $this->getTargetToken($identifier, [T_CLASS, T_ANON_CLASS, T_INTERFACE, T_ENUM]);
        $result  = $testClass::findImplementedInterfaceNames(self::$phpcsFile, $OOToken);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testFindImplementedInterfaceNames()
     *
     * @return array<string, array<string, string|array<string>|false>>
     */
    public static function dataImplementedInterface()
    {
        return [
            'interface declaration, no implements' => [
                'identifier' => '/* testPlainInterface */',
                'expected'   => false,
            ],
            'class does not implement' => [
                'identifier' => '/* testNonImplementedClass */',
                'expected'   => false,
            ],
            'class implements single interface, unqualified' => [
                'identifier' => '/* testClassImplementsSingle */',
                'expected'   => ['testFIINInterface'],
            ],
            'class implements multiple interfaces' => [
                'identifier' => '/* testClassImplementsMultiple */',
                'expected'   => [
                    'testFIINInterface',
                    'testFIINInterface2',
                ],
            ],
            'class implements single interface, fully qualified' => [
                'identifier' => '/* testImplementsFullyQualified */',
                'expected'   => ['\PHP_CodeSniffer\Tests\Core\File\testFIINInterface'],
            ],
            'class implements single interface, partially qualified' => [
                'identifier' => '/* testImplementsPartiallyQualified */',
                'expected'   => ['Core\File\RelativeInterface'],
            ],
            'class implements multiple interfaces, namespace relative' => [
                'identifier' => '/* testImplementsMultipleNamespaceRelativeInterfaces */',
                'expected'   => [
                    'namespace\testInterfaceA',
                    'namespace\testInterfaceB',
                ],
            ],
            'class extends and implements' => [
                'identifier' => '/* testClassThatExtendsAndImplements */',
                'expected'   => [
                    'InterfaceA',
                    '\NameSpaced\Cat\InterfaceB',
                ],
            ],
            'class implements and extends' => [
                'identifier' => '/* testClassThatImplementsAndExtends */',
                'expected'   => [
                    '\InterfaceA',
                    'InterfaceB',
                ],
            ],
            'enum does not implement' => [
                'identifier' => '/* testBackedEnumWithoutImplements */',
                'expected'   => false,
            ],
            'enum implements single interface, unqualified' => [
                'identifier' => '/* testEnumImplementsSingle */',
                'expected'   => ['Colorful'],
            ],
            'enum implements multiple interfaces, unqualified + fully qualified' => [
                'identifier' => '/* testBackedEnumImplementsMulti */',
                'expected'   => [
                    'Colorful',
                    '\Deck',
                ],
            ],
            'anon class implements single interface, unqualified' => [
                'identifier' => '/* testAnonClassImplementsSingle */',
                'expected'   => ['testFIINInterface'],
            ],
            'parse error - implements keyword, but no interface name' => [
                'identifier' => '/* testMissingImplementsName */',
                'expected'   => false,
            ],
            'parse error - live coding - no curly braces' => [
                'identifier' => '/* testParseError */',
                'expected'   => false,
            ],
        ];
    }
}
