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

use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::isReference method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::isReference
 *
 * @group operators
 *
 * @since 1.0.0
 */
class IsReferenceTest extends UtilityMethodTestCase
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
     * Test that false is returned when a non-"bitwise and" token is passed.
     *
     * @return void
     */
    public function testNotBitwiseAndToken()
    {
        $testClass = static::TEST_CLASS;

        $target = $this->getTargetToken('/* testBitwiseAndA */', T_STRING);
        $this->assertFalse($testClass::isReference(self::$phpcsFile, $target));
    }

    /**
     * Test correctly identifying that whether a "bitwise and" token is a reference or not.
     *
     * @dataProvider dataIsReference
     *
     * @param string $identifier Comment which precedes the test case.
     * @param bool   $expected   Expected function output.
     *
     * @return void
     */
    public function testIsReference($identifier, $expected)
    {
        $testClass = static::TEST_CLASS;

        $bitwiseAnd = $this->getTargetToken($identifier, T_BITWISE_AND);
        $result     = $testClass::isReference(self::$phpcsFile, $bitwiseAnd);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
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
                '/* testAssignByReferenceF */',
                true,
            ],
            [
                '/* testShortListAssignByReferenceNoKeyA */',
                true,
            ],
            [
                '/* testShortListAssignByReferenceNoKeyB */',
                true,
            ],
            [
                '/* testNestedShortListAssignByReferenceNoKey */',
                true,
            ],
            [
                '/* testLongListAssignByReferenceNoKeyA */',
                true,
            ],
            [
                '/* testLongListAssignByReferenceNoKeyB */',
                true,
            ],
            [
                '/* testLongListAssignByReferenceNoKeyC */',
                true,
            ],
            [
                '/* testNestedShortListAssignByReferenceWithKeyA */',
                true,
            ],
            [
                '/* testNestedShortListAssignByReferenceWithKeyB */',
                true,
            ],
            [
                '/* testLongListAssignByReferenceWithKeyA */',
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
            [
                '/* testArrowFunctionReturnByReference */',
                true,
            ],
            [
                '/* testClosureReturnByReference */',
                true,
            ],
        ];
    }
}
