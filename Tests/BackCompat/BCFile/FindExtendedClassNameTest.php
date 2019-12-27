<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:findExtendedClassName method.
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
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHPCSUtils\Tests\BackCompat\BCFile;

use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

class FindExtendedClassNameTest extends UtilityMethodTestCase
{


    /**
     * Test retrieving the name of the class being extended by another class
     * (or interface).
     *
     * @param string $identifier Comment which precedes the test case.
     * @param bool   $expected   Expected function output.
     *
     * @dataProvider dataExtendedClass
     *
     * @return void
     */
    public function testFindExtendedClassName($identifier, $expected)
    {
        $OOToken = $this->getTargetToken($identifier, [T_CLASS, T_ANON_CLASS, T_INTERFACE]);
        $result  = BCFile::findExtendedClassName(self::$phpcsFile, $OOToken);
        $this->assertSame($expected, $result);

    }//end testFindExtendedClassName()


    /**
     * Data provider for the FindExtendedClassName test.
     *
     * @see testFindExtendedClassName()
     *
     * @return array
     */
    public function dataExtendedClass()
    {
        return [
            [
                '/* testExtendedClass */',
                'testFECNClass',
            ],
            [
                '/* testNamespacedClass */',
                '\PHP_CodeSniffer\Tests\Core\File\testFECNClass',
            ],
            [
                '/* testNonExtendedClass */',
                false,
            ],
            [
                '/* testInterface */',
                false,
            ],
            [
                '/* testInterfaceThatExtendsInterface */',
                'testFECNInterface',
            ],
            [
                '/* testInterfaceThatExtendsFQCNInterface */',
                '\PHP_CodeSniffer\Tests\Core\File\testFECNInterface',
            ],
            [
                '/* testNestedExtendedClass */',
                false,
            ],
            [
                '/* testNestedExtendedAnonClass */',
                'testFECNAnonClass',
            ],
            [
                '/* testClassThatExtendsAndImplements */',
                'testFECNClass',
            ],
            [
                '/* testClassThatImplementsAndExtends */',
                'testFECNClass',
            ],
        ];

    }//end dataExtendedClass()


}//end class
