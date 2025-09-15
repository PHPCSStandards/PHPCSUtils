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
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
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
     * @param string            $testMarker   Comment which precedes the test case.
     * @param array<int|string> $targetTokens Type of tokens to look for.
     *
     * @dataProvider dataNotBitwiseAndToken
     *
     * @return void
     */
    public function testNotBitwiseAndToken($testMarker, $targetTokens)
    {
        $testClass      = static::TEST_CLASS;
        $targetTokens[] = T_BITWISE_AND;

        $target = $this->getTargetToken($testMarker, $targetTokens);
        $this->assertFalse($testClass::isReference(self::$phpcsFile, $target));
    }

    /**
     * Data provider.
     *
     * @see testNotBitwiseAndToken()
     *
     * @return array<string, array<string, string|array<int|string>>>
     */
    public static function dataNotBitwiseAndToken()
    {
        return [
            'Not ampersand token at all' => [
                'testMarker'   => '/* testBitwiseAndA */',
                'targetTokens' => [T_STRING],
            ],
            'ampersand in intersection type' => [
                'testMarker'   => '/* testIntersectionIsNotReference */',
                'targetTokens' => [T_TYPE_INTERSECTION],
            ],
            'ampersand in DNF type' => [
                'testMarker'   => '/* testDNFTypeIsNotReference */',
                'targetTokens' => [T_TYPE_INTERSECTION],
            ],
        ];
    }

    /**
     * Test correctly identifying whether a "bitwise and" token is a reference or not.
     *
     * @dataProvider dataIsReference
     *
     * @param string $testMarker Comment which precedes the test case.
     * @param bool   $expected   Expected function output.
     *
     * @return void
     */
    public function testIsReference($testMarker, $expected)
    {
        $testClass = static::TEST_CLASS;

        $bitwiseAnd = $this->getTargetToken($testMarker, T_BITWISE_AND);
        $result     = $testClass::isReference(self::$phpcsFile, $bitwiseAnd);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testIsReference()
     *
     * @return array<string, array<string, string|bool>>
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
                'testMarker' => '/* testBitwiseAndA */',
                'expected'   => false,
            ],
            'bitwise and: in unkeyed short array, first value' => [
                'testMarker' => '/* testBitwiseAndB */',
                'expected'   => false,
            ],
            'bitwise and: in unkeyed short array, last value' => [
                'testMarker' => '/* testBitwiseAndC */',
                'expected'   => false,
            ],
            'bitwise and: in unkeyed long array, last value' => [
                'testMarker' => '/* testBitwiseAndD */',
                'expected'   => false,
            ],
            'bitwise and: in keyed short array, last value' => [
                'testMarker' => '/* testBitwiseAndE */',
                'expected'   => false,
            ],
            'bitwise and: in keyed long array, last value' => [
                'testMarker' => '/* testBitwiseAndF */',
                'expected'   => false,
            ],
            'bitwise and: in assignment' => [
                'testMarker' => '/* testBitwiseAndG */',
                'expected'   => false,
            ],
            'bitwise and: in param default value in function declaration' => [
                'testMarker' => '/* testBitwiseAndH */',
                'expected'   => false,
            ],
            'bitwise and: in param default value in closure declaration' => [
                'testMarker' => '/* testBitwiseAndI */',
                'expected'   => false,
            ],
            'reference: function declared to return by reference' => [
                'testMarker' => '/* testFunctionReturnByReference */',
                'expected'   => true,
            ],
            'reference: only param in function declaration, pass by reference' => [
                'testMarker' => '/* testFunctionPassByReferenceA */',
                'expected'   => true,
            ],
            'reference: last param in function declaration, pass by reference' => [
                'testMarker' => '/* testFunctionPassByReferenceB */',
                'expected'   => true,
            ],
            'reference: only param in closure declaration, pass by reference' => [
                'testMarker' => '/* testFunctionPassByReferenceC */',
                'expected'   => true,
            ],
            'reference: last param in closure declaration, pass by reference' => [
                'testMarker' => '/* testFunctionPassByReferenceD */',
                'expected'   => true,
            ],
            'reference: typed param in function declaration, pass by reference' => [
                'testMarker' => '/* testFunctionPassByReferenceE */',
                'expected'   => true,
            ],
            'reference: typed param in closure declaration, pass by reference' => [
                'testMarker' => '/* testFunctionPassByReferenceF */',
                'expected'   => true,
            ],
            'reference: variadic param in function declaration, pass by reference' => [
                'testMarker' => '/* testFunctionPassByReferenceG */',
                'expected'   => true,
            ],
            'reference: foreach value' => [
                'testMarker' => '/* testForeachValueByReference */',
                'expected'   => true,
            ],
            'reference: foreach key' => [
                'testMarker' => '/* testForeachKeyByReference */',
                'expected'   => true,
            ],
            'reference: keyed short array, first value, value by reference' => [
                'testMarker' => '/* testArrayValueByReferenceA */',
                'expected'   => true,
            ],
            'reference: keyed short array, last value, value by reference' => [
                'testMarker' => '/* testArrayValueByReferenceB */',
                'expected'   => true,
            ],
            'reference: unkeyed short array, only value, value by reference' => [
                'testMarker' => '/* testArrayValueByReferenceC */',
                'expected'   => true,
            ],
            'reference: unkeyed short array, last value, value by reference' => [
                'testMarker' => '/* testArrayValueByReferenceD */',
                'expected'   => true,
            ],
            'reference: keyed long array, first value, value by reference' => [
                'testMarker' => '/* testArrayValueByReferenceE */',
                'expected'   => true,
            ],
            'reference: keyed long array, last value, value by reference' => [
                'testMarker' => '/* testArrayValueByReferenceF */',
                'expected'   => true,
            ],
            'reference: unkeyed long array, only value, value by reference' => [
                'testMarker' => '/* testArrayValueByReferenceG */',
                'expected'   => true,
            ],
            'reference: unkeyed long array, last value, value by reference' => [
                'testMarker' => '/* testArrayValueByReferenceH */',
                'expected'   => true,
            ],
            'reference: variable, assign by reference' => [
                'testMarker' => '/* testAssignByReferenceA */',
                'expected'   => true,
            ],
            'reference: variable, assign by reference, spacing variation' => [
                'testMarker' => '/* testAssignByReferenceB */',
                'expected'   => true,
            ],
            'reference: variable, assign by reference, concat assign' => [
                'testMarker' => '/* testAssignByReferenceC */',
                'expected'   => true,
            ],
            'reference: property, assign by reference' => [
                'testMarker' => '/* testAssignByReferenceD */',
                'expected'   => true,
            ],
            'reference: function return value, assign by reference' => [
                'testMarker' => '/* testAssignByReferenceE */',
                'expected'   => true,
            ],
            'reference: function return value, assign by reference, null coalesce assign' => [
                'testMarker' => '/* testAssignByReferenceF */',
                'expected'   => true,
            ],
            'reference: unkeyed short list, first var, assign by reference' => [
                'testMarker' => '/* testShortListAssignByReferenceNoKeyA */',
                'expected'   => true,
            ],
            'reference: unkeyed short list, second var, assign by reference' => [
                'testMarker' => '/* testShortListAssignByReferenceNoKeyB */',
                'expected'   => true,
            ],
            'reference: unkeyed short list, nested var, assign by reference' => [
                'testMarker' => '/* testNestedShortListAssignByReferenceNoKey */',
                'expected'   => true,
            ],
            'reference: unkeyed long list, second var, assign by reference' => [
                'testMarker' => '/* testLongListAssignByReferenceNoKeyA */',
                'expected'   => true,
            ],
            'reference: unkeyed long list, first nested var, assign by reference' => [
                'testMarker' => '/* testLongListAssignByReferenceNoKeyB */',
                'expected'   => true,
            ],
            'reference: unkeyed long list, last nested var, assign by reference' => [
                'testMarker' => '/* testLongListAssignByReferenceNoKeyC */',
                'expected'   => true,
            ],
            'reference: keyed short list, first nested var, assign by reference' => [
                'testMarker' => '/* testNestedShortListAssignByReferenceWithKeyA */',
                'expected'   => true,
            ],
            'reference: keyed short list, last nested var, assign by reference' => [
                'testMarker' => '/* testNestedShortListAssignByReferenceWithKeyB */',
                'expected'   => true,
            ],
            'reference: keyed long list, only var, assign by reference' => [
                'testMarker' => '/* testLongListAssignByReferenceWithKeyA */',
                'expected'   => true,
            ],
            'reference: first param in function call, pass by reference' => [
                'testMarker' => '/* testPassByReferenceA */',
                'expected'   => true,
            ],
            'reference: last param in function call, pass by reference' => [
                'testMarker' => '/* testPassByReferenceB */',
                'expected'   => true,
            ],
            'reference: property in function call, pass by reference' => [
                'testMarker' => '/* testPassByReferenceC */',
                'expected'   => true,
            ],
            'reference: hierarchical self property in function call, pass by reference' => [
                'testMarker' => '/* testPassByReferenceD */',
                'expected'   => true,
            ],
            'reference: hierarchical parent property in function call, pass by reference' => [
                'testMarker' => '/* testPassByReferenceE */',
                'expected'   => true,
            ],
            'reference: hierarchical static property in function call, pass by reference' => [
                'testMarker' => '/* testPassByReferenceF */',
                'expected'   => true,
            ],
            'reference: static property in function call, pass by reference' => [
                'testMarker' => '/* testPassByReferenceG */',
                'expected'   => true,
            ],
            'reference: static property in function call, first with FQN, pass by reference' => [
                'testMarker' => '/* testPassByReferenceH */',
                'expected'   => true,
            ],
            'reference: static property in function call, last with FQN, pass by reference' => [
                'testMarker' => '/* testPassByReferenceI */',
                'expected'   => true,
            ],
            'reference: static property in function call, last with namespace relative name, pass by reference' => [
                'testMarker' => '/* testPassByReferenceJ */',
                'expected'   => true,
            ],
            'reference: static property in function call, last with PQN, pass by reference' => [
                'testMarker' => '/* testPassByReferencePartiallyQualifiedName */',
                'expected'   => true,
            ],
            'reference: new by reference' => [
                'testMarker' => '/* testNewByReferenceA */',
                'expected'   => true,
            ],
            'reference: new by reference as function call param' => [
                'testMarker' => '/* testNewByReferenceB */',
                'expected'   => true,
            ],
            'reference: closure use by reference' => [
                'testMarker' => '/* testUseByReference */',
                'expected'   => true,
            ],
            'reference: closure use by reference, first param, with comment' => [
                'testMarker' => '/* testUseByReferenceWithCommentFirstParam */',
                'expected'   => true,
            ],
            'reference: closure use by reference, last param, with comment' => [
                'testMarker' => '/* testUseByReferenceWithCommentSecondParam */',
                'expected'   => true,
            ],
            'reference: arrow fn declared to return by reference' => [
                'testMarker' => '/* testArrowFunctionReturnByReference */',
                'expected'   => true,
            ],
            'bitwise and: first param default value in closure declaration' => [
                'testMarker' => '/* testBitwiseAndExactParameterA */',
                'expected'   => false,
            ],
            'reference: param in closure declaration, pass by reference' => [
                'testMarker' => '/* testPassByReferenceExactParameterB */',
                'expected'   => true,
            ],
            'reference: variadic param in closure declaration, pass by reference' => [
                'testMarker' => '/* testPassByReferenceExactParameterC */',
                'expected'   => true,
            ],
            'bitwise and: last param default value in closure declaration' => [
                'testMarker' => '/* testBitwiseAndExactParameterD */',
                'expected'   => false,
            ],
            'reference: typed param in arrow fn declaration, pass by reference' => [
                'testMarker' => '/* testArrowFunctionPassByReferenceA */',
                'expected'   => true,
            ],
            'reference: variadic param in arrow fn declaration, pass by reference' => [
                'testMarker' => '/* testArrowFunctionPassByReferenceB */',
                'expected'   => true,
            ],
            'reference: closure declared to return by reference' => [
                'testMarker' => '/* testClosureReturnByReference */',
                'expected'   => true,
            ],
            'bitwise and: param default value in arrow fn declaration' => [
                'testMarker' => '/* testBitwiseAndArrowFunctionInDefault */',
                'expected'   => false,
            ],
            'reference: param pass by ref in arrow function' => [
                'testMarker' => '/* testParamPassByReference */',
                'expected'   => true,
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
