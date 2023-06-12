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
    public static function dataIsReference()
    {
        return [
            'issue-1971-list-first-in-file' => [
                'testMarker' => '/* testTokenizerIssue1971PHPCSlt330gt271A */',
                'expected'   => true,
            ],
            'issue-1971-list-first-in-file-nested' => [
                'testMarker' => '/* testTokenizerIssue1971PHPCSlt330gt271B */',
                'expected'   => true,
            ],
            'bitwise and: param in function call' => [
                '/* testBitwiseAndA */',
                false,
            ],
            'bitwise and: in unkeyed short array, first value' => [
                '/* testBitwiseAndB */',
                false,
            ],
            'bitwise and: in unkeyed short array, last value' => [
                '/* testBitwiseAndC */',
                false,
            ],
            'bitwise and: in unkeyed long array, last value' => [
                '/* testBitwiseAndD */',
                false,
            ],
            'bitwise and: in keyed short array, last value' => [
                '/* testBitwiseAndE */',
                false,
            ],
            'bitwise and: in keyed long array, last value' => [
                '/* testBitwiseAndF */',
                false,
            ],
            'bitwise and: in assignment' => [
                '/* testBitwiseAndG */',
                false,
            ],
            'bitwise and: in param default value in function declaration' => [
                '/* testBitwiseAndH */',
                false,
            ],
            'bitwise and: in param default value in closure declaration' => [
                '/* testBitwiseAndI */',
                false,
            ],
            'reference: function declared to return by reference' => [
                '/* testFunctionReturnByReference */',
                true,
            ],
            'reference: only param in function declaration, pass by reference' => [
                '/* testFunctionPassByReferenceA */',
                true,
            ],
            'reference: last param in function declaration, pass by reference' => [
                '/* testFunctionPassByReferenceB */',
                true,
            ],
            'reference: only param in closure declaration, pass by reference' => [
                '/* testFunctionPassByReferenceC */',
                true,
            ],
            'reference: last param in closure declaration, pass by reference' => [
                '/* testFunctionPassByReferenceD */',
                true,
            ],
            'reference: typed param in function declaration, pass by reference' => [
                '/* testFunctionPassByReferenceE */',
                true,
            ],
            'reference: typed param in closure declaration, pass by reference' => [
                '/* testFunctionPassByReferenceF */',
                true,
            ],
            'reference: variadic param in function declaration, pass by reference' => [
                '/* testFunctionPassByReferenceG */',
                true,
            ],
            'reference: foreach value' => [
                '/* testForeachValueByReference */',
                true,
            ],
            'reference: foreach key' => [
                '/* testForeachKeyByReference */',
                true,
            ],
            'reference: keyed short array, first value, value by reference' => [
                '/* testArrayValueByReferenceA */',
                true,
            ],
            'reference: keyed short array, last value, value by reference' => [
                '/* testArrayValueByReferenceB */',
                true,
            ],
            'reference: unkeyed short array, only value, value by reference' => [
                '/* testArrayValueByReferenceC */',
                true,
            ],
            'reference: unkeyed short array, last value, value by reference' => [
                '/* testArrayValueByReferenceD */',
                true,
            ],
            'reference: keyed long array, first value, value by reference' => [
                '/* testArrayValueByReferenceE */',
                true,
            ],
            'reference: keyed long array, last value, value by reference' => [
                '/* testArrayValueByReferenceF */',
                true,
            ],
            'reference: unkeyed long array, only value, value by reference' => [
                '/* testArrayValueByReferenceG */',
                true,
            ],
            'reference: unkeyed long array, last value, value by reference' => [
                '/* testArrayValueByReferenceH */',
                true,
            ],
            'reference: variable, assign by reference' => [
                '/* testAssignByReferenceA */',
                true,
            ],
            'reference: variable, assign by reference, spacing variation' => [
                '/* testAssignByReferenceB */',
                true,
            ],
            'reference: variable, assign by reference, concat assign' => [
                '/* testAssignByReferenceC */',
                true,
            ],
            'reference: property, assign by reference' => [
                '/* testAssignByReferenceD */',
                true,
            ],
            'reference: function return value, assign by reference' => [
                '/* testAssignByReferenceE */',
                true,
            ],
            'reference: function return value, assign by reference, null coalesce assign' => [
                '/* testAssignByReferenceF */',
                true,
            ],
            'reference: unkeyed short list, first var, assign by reference' => [
                '/* testShortListAssignByReferenceNoKeyA */',
                true,
            ],
            'reference: unkeyed short list, second var, assign by reference' => [
                '/* testShortListAssignByReferenceNoKeyB */',
                true,
            ],
            'reference: unkeyed short list, nested var, assign by reference' => [
                '/* testNestedShortListAssignByReferenceNoKey */',
                true,
            ],
            'reference: unkeyed long list, second var, assign by reference' => [
                '/* testLongListAssignByReferenceNoKeyA */',
                true,
            ],
            'reference: unkeyed long list, first nested var, assign by reference' => [
                '/* testLongListAssignByReferenceNoKeyB */',
                true,
            ],
            'reference: unkeyed long list, last nested var, assign by reference' => [
                '/* testLongListAssignByReferenceNoKeyC */',
                true,
            ],
            'reference: keyed short list, first nested var, assign by reference' => [
                '/* testNestedShortListAssignByReferenceWithKeyA */',
                true,
            ],
            'reference: keyed short list, last nested var, assign by reference' => [
                '/* testNestedShortListAssignByReferenceWithKeyB */',
                true,
            ],
            'reference: keyed long list, only var, assign by reference' => [
                '/* testLongListAssignByReferenceWithKeyA */',
                true,
            ],
            'reference: first param in function call, pass by reference' => [
                '/* testPassByReferenceA */',
                true,
            ],
            'reference: last param in function call, pass by reference' => [
                '/* testPassByReferenceB */',
                true,
            ],
            'reference: property in function call, pass by reference' => [
                '/* testPassByReferenceC */',
                true,
            ],
            'reference: hierarchical self property in function call, pass by reference' => [
                '/* testPassByReferenceD */',
                true,
            ],
            'reference: hierarchical parent property in function call, pass by reference' => [
                '/* testPassByReferenceE */',
                true,
            ],
            'reference: hierarchical static property in function call, pass by reference' => [
                '/* testPassByReferenceF */',
                true,
            ],
            'reference: static property in function call, pass by reference' => [
                '/* testPassByReferenceG */',
                true,
            ],
            'reference: static property in function call, first with FQN, pass by reference' => [
                '/* testPassByReferenceH */',
                true,
            ],
            'reference: static property in function call, last with FQN, pass by reference' => [
                '/* testPassByReferenceI */',
                true,
            ],
            'reference: static property in function call, last with namespace relative name, pass by reference' => [
                '/* testPassByReferenceJ */',
                true,
            ],
            'reference: static property in function call, last with PQN, pass by reference' => [
                '/* testPassByReferencePartiallyQualifiedName */',
                true,
            ],
            'reference: new by reference' => [
                '/* testNewByReferenceA */',
                true,
            ],
            'reference: new by reference as function call param' => [
                '/* testNewByReferenceB */',
                true,
            ],
            'reference: closure use by reference' => [
                '/* testUseByReference */',
                true,
            ],
            'reference: closure use by reference, first param, with comment' => [
                '/* testUseByReferenceWithCommentFirstParam */',
                true,
            ],
            'reference: closure use by reference, last param, with comment' => [
                '/* testUseByReferenceWithCommentSecondParam */',
                true,
            ],
            'reference: arrow fn declared to return by reference' => [
                '/* testArrowFunctionReturnByReference */',
                true,
            ],
            'bitwise and: first param default value in closure declaration' => [
                '/* testBitwiseAndExactParameterA */',
                false,
            ],
            'reference: param in closure declaration, pass by reference' => [
                '/* testPassByReferenceExactParameterB */',
                true,
            ],
            'reference: variadic param in closure declaration, pass by reference' => [
                '/* testPassByReferenceExactParameterC */',
                true,
            ],
            'bitwise and: last param default value in closure declaration' => [
                '/* testBitwiseAndExactParameterD */',
                false,
            ],
            'reference: typed param in arrow fn declaration, pass by reference' => [
                '/* testArrowFunctionPassByReferenceA */',
                true,
            ],
            'reference: variadic param in arrow fn declaration, pass by reference' => [
                '/* testArrowFunctionPassByReferenceB */',
                true,
            ],
            'reference: closure declared to return by reference' => [
                '/* testClosureReturnByReference */',
                true,
            ],
            'bitwise and: param default value in arrow fn declaration' => [
                '/* testBitwiseAndArrowFunctionInDefault */',
                false,
            ],
            'issue-1284-short-list-directly-after-close-curly-control-structure' => [
                'testMarker' => '/* testTokenizerIssue1284PHPCSlt280A */',
                'expected'   => true,
            ],
            'issue-1284-short-list-directly-after-close-curly-control-structure-second-item' => [
                'testMarker' => '/* testTokenizerIssue1284PHPCSlt280B */',
                'expected'   => true,
            ],
            'issue-1284-short-array-directly-after-close-curly-control-structure' => [
                'testMarker' => '/* testTokenizerIssue1284PHPCSlt280C */',
                'expected'   => true,
            ],
        ];
    }
}
