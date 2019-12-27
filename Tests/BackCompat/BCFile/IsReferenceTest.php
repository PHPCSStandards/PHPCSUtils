<?php
/**
 * Tests for the \PHP_CodeSniffer\Files\File:isReference method.
 *
 * This class is imported from the PHP_CodeSniffer project.
 *
 * Copyright of the original code in this class as per the import:
 * @author    Juliette Reinders Folmer <jrf@phpcodesniffer.info>
 * @author    Greg Sherwood <gsherwood@squiz.net>
 *
 * With documentation contributions from:
 * @author    Phil Davis <phil@jankaritech.com>
 *
 * @copyright 2017-2019 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHPCSUtils\Tests\BackCompat\BCFile;

use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

class IsReferenceTest extends UtilityMethodTestCase
{


    /**
     * Test a class that extends another.
     *
     * @param string $identifier Comment which precedes the test case.
     * @param bool   $expected   Expected function output.
     *
     * @dataProvider dataIsReference
     *
     * @return void
     */
    public function testIsReference($identifier, $expected)
    {
        $bitwiseAnd = $this->getTargetToken($identifier, T_BITWISE_AND);
        $result     = BCFile::isReference(self::$phpcsFile, $bitwiseAnd);
        $this->assertSame($expected, $result);

    }//end testIsReference()


    /**
     * Data provider for the IsReference test.
     *
     * @see testIsReference()
     *
     * @return array
     */
    public function dataIsReference()
    {
        return [
            [
                '/* testBitwiseAndA */',
                false,
            ],
            [
                '/* testBitwiseAndB */',
                false,
            ],
            [
                '/* testBitwiseAndC */',
                false,
            ],
            [
                '/* testBitwiseAndD */',
                false,
            ],
            [
                '/* testBitwiseAndE */',
                false,
            ],
            [
                '/* testBitwiseAndF */',
                false,
            ],
            [
                '/* testBitwiseAndG */',
                false,
            ],
            [
                '/* testBitwiseAndH */',
                false,
            ],
            [
                '/* testBitwiseAndI */',
                false,
            ],
            [
                '/* testFunctionReturnByReference */',
                true,
            ],
            [
                '/* testFunctionPassByReferenceA */',
                true,
            ],
            [
                '/* testFunctionPassByReferenceB */',
                true,
            ],
            [
                '/* testFunctionPassByReferenceC */',
                true,
            ],
            [
                '/* testFunctionPassByReferenceD */',
                true,
            ],
            [
                '/* testFunctionPassByReferenceE */',
                true,
            ],
            [
                '/* testFunctionPassByReferenceF */',
                true,
            ],
            [
                '/* testFunctionPassByReferenceG */',
                true,
            ],
            [
                '/* testForeachValueByReference */',
                true,
            ],
            [
                '/* testForeachKeyByReference */',
                true,
            ],
            [
                '/* testArrayValueByReferenceA */',
                true,
            ],
            [
                '/* testArrayValueByReferenceB */',
                true,
            ],
            [
                '/* testArrayValueByReferenceC */',
                true,
            ],
            [
                '/* testArrayValueByReferenceD */',
                true,
            ],
            [
                '/* testArrayValueByReferenceE */',
                true,
            ],
            [
                '/* testArrayValueByReferenceF */',
                true,
            ],
            [
                '/* testArrayValueByReferenceG */',
                true,
            ],
            [
                '/* testArrayValueByReferenceH */',
                true,
            ],
            [
                '/* testAssignByReferenceA */',
                true,
            ],
            [
                '/* testAssignByReferenceB */',
                true,
            ],
            [
                '/* testAssignByReferenceC */',
                true,
            ],
            [
                '/* testAssignByReferenceD */',
                true,
            ],
            [
                '/* testAssignByReferenceE */',
                true,
            ],
            [
                '/* testPassByReferenceA */',
                true,
            ],
            [
                '/* testPassByReferenceB */',
                true,
            ],
            [
                '/* testPassByReferenceC */',
                true,
            ],
            [
                '/* testPassByReferenceD */',
                true,
            ],
            [
                '/* testPassByReferenceE */',
                true,
            ],
            [
                '/* testPassByReferenceF */',
                true,
            ],
            [
                '/* testPassByReferenceG */',
                true,
            ],
            [
                '/* testPassByReferenceH */',
                true,
            ],
            [
                '/* testPassByReferenceI */',
                true,
            ],
            [
                '/* testPassByReferenceJ */',
                true,
            ],
            [
                '/* testNewByReferenceA */',
                true,
            ],
            [
                '/* testNewByReferenceB */',
                true,
            ],
            [
                '/* testUseByReference */',
                true,
            ],
        ];

    }//end dataIsReference()


}//end class
