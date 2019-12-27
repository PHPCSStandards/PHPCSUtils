<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:findImplementedInterfaceNames method.
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
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHP_CodeSniffer\Tests\Core\File;

use PHP_CodeSniffer\Tests\Core\AbstractMethodUnitTest;

class FindImplementedInterfaceNamesTest extends AbstractMethodUnitTest
{


    /**
     * Test retrieving the name(s) of the interfaces being implemented by a class.
     *
     * @param string $identifier Comment which precedes the test case.
     * @param bool   $expected   Expected function output.
     *
     * @dataProvider dataImplementedInterface
     *
     * @return void
     */
    public function testFindImplementedInterfaceNames($identifier, $expected)
    {
        $OOToken = $this->getTargetToken($identifier, [T_CLASS, T_ANON_CLASS, T_INTERFACE]);
        $result  = self::$phpcsFile->findImplementedInterfaceNames($OOToken);
        $this->assertSame($expected, $result);

    }//end testFindImplementedInterfaceNames()


    /**
     * Data provider for the FindImplementedInterfaceNames test.
     *
     * @see testFindImplementedInterfaceNames()
     *
     * @return array
     */
    public function dataImplementedInterface()
    {
        return [
            [
                '/* testImplementedClass */',
                ['testFIINInterface'],
            ],
            [
                '/* testMultiImplementedClass */',
                [
                    'testFIINInterface',
                    'testFIINInterface2',
                ],
            ],
            [
                '/* testNamespacedClass */',
                ['\PHP_CodeSniffer\Tests\Core\File\testFIINInterface'],
            ],
            [
                '/* testNonImplementedClass */',
                false,
            ],
            [
                '/* testInterface */',
                false,
            ],
            [
                '/* testClassThatExtendsAndImplements */',
                [
                    'InterfaceA',
                    '\NameSpaced\Cat\InterfaceB',
                ],
            ],
            [
                '/* testClassThatImplementsAndExtends */',
                [
                    '\InterfaceA',
                    'InterfaceB',
                ],
            ],
        ];

    }//end dataImplementedInterface()


}//end class
