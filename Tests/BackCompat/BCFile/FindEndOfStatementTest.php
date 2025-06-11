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
 * @author    Phil Davis <phil@jankaritech.com>
 *
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/PHPCSStandards/PHP_CodeSniffer/blob/HEAD/licence.txt BSD Licence
 */

namespace PHPCSUtils\Tests\BackCompat\BCFile;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::findEndOfStatement method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::findEndOfStatement
 *
 * @since 1.0.0
 */
final class FindEndOfStatementTest extends UtilityMethodTestCase
{

    /**
     * Test that end of statement is NEVER before the "current" token.
     *
     * @return void
     */
    public function testEndIsNeverLessThanCurrentToken()
    {
        $tokens = self::$phpcsFile->getTokens();
        $errors = [];

        for ($i = 0; $i < self::$phpcsFile->numTokens; $i++) {
            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                continue;
            }

            $end = BCFile::findEndOfStatement(self::$phpcsFile, $i);

            // Collect all the errors.
            if ($end < $i) {
                $errors[] = sprintf(
                    'End of statement for token %1$d (%2$s: %3$s) on line %4$d is %5$d (%6$s), which is less than %1$d',
                    $i,
                    $tokens[$i]['type'],
                    $tokens[$i]['content'],
                    $tokens[$i]['line'],
                    $end,
                    $tokens[$end]['type']
                );
            }
        }

        $this->assertSame([], $errors);
    }

    /**
     * Test a simple assignment.
     *
     * @return void
     */
    public function testSimpleAssignment()
    {
        $start = $this->getTargetToken('/* testSimpleAssignment */', T_VARIABLE);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 5), $found);
    }

    /**
     * Test a direct call to a control structure.
     *
     * @return void
     */
    public function testControlStructure()
    {
        $start = $this->getTargetToken('/* testControlStructure */', T_WHILE);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 6), $found);
    }

    /**
     * Test the assignment of a closure.
     *
     * @return void
     */
    public function testClosureAssignment()
    {
        $start = $this->getTargetToken('/* testClosureAssignment */', T_VARIABLE, '$a');
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 13), $found);
    }

    /**
     * Test using a heredoc in a function argument.
     *
     * @return void
     */
    public function testHeredocFunctionArg()
    {
        // Find the end of the function.
        $start = $this->getTargetToken('/* testHeredocFunctionArg */', T_STRING, 'myFunction');
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 10), $found);

        // Find the end of the heredoc.
        $start += 2;
        $found  = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 4), $found);

        // Find the end of the last arg.
        $start = ($found + 2);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame($start, $found);
    }

    /**
     * Test parts of a switch statement.
     *
     * @return void
     */
    public function testSwitch()
    {
        // Find the end of the switch.
        $start = $this->getTargetToken('/* testSwitch */', T_SWITCH);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 28), $found);

        // Find the end of the case.
        $start += 9;
        $found  = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 8), $found);

        // Find the end of default case.
        $start += 11;
        $found  = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 6), $found);
    }

    /**
     * Test statements that are array values.
     *
     * @return void
     */
    public function testStatementAsArrayValue()
    {
        // Test short array syntax.
        $start = $this->getTargetToken('/* testStatementAsArrayValue */', T_NEW);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 2), $found);

        // Test long array syntax.
        $start += 12;
        $found  = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 2), $found);

        // Test same statement outside of array.
        $start += 10;
        $found  = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 3), $found);
    }

    /**
     * Test a use group.
     *
     * @return void
     */
    public function testUseGroup()
    {
        $start = $this->getTargetToken('/* testUseGroup */', T_USE);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $expected = parent::usesPhp8NameTokens() ? ($start + 21) : ($start + 23);

        $this->assertSame($expected, $found);
    }

    /**
     * Test arrow function as array value.
     *
     * @return void
     */
    public function testArrowFunctionArrayValue()
    {
        $start = $this->getTargetToken('/* testArrowFunctionArrayValue */', T_FN);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 9), $found);
    }

    /**
     * Test static arrow function.
     *
     * @return void
     */
    public function testStaticArrowFunction()
    {
        $static = $this->getTargetToken('/* testStaticArrowFunction */', T_STATIC);
        $fn     = $this->getTargetToken('/* testStaticArrowFunction */', T_FN);

        $endOfStatementStatic = BCFile::findEndOfStatement(self::$phpcsFile, $static);
        $endOfStatementFn     = BCFile::findEndOfStatement(self::$phpcsFile, $fn);

        $this->assertSame($endOfStatementFn, $endOfStatementStatic);
    }

    /**
     * Test arrow function with return value.
     *
     * @return void
     */
    public function testArrowFunctionReturnValue()
    {
        $start = $this->getTargetToken('/* testArrowFunctionReturnValue */', T_FN);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 18), $found);
    }

    /**
     * Test arrow function used as a function argument.
     *
     * @return void
     */
    public function testArrowFunctionAsArgument()
    {
        $start = $this->getTargetToken('/* testArrowFunctionAsArgument */', T_FN);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 8), $found);
    }

    /**
     * Test arrow function with arrays used as a function argument.
     *
     * @return void
     */
    public function testArrowFunctionWithArrayAsArgument()
    {
        $start = $this->getTargetToken('/* testArrowFunctionWithArrayAsArgument */', T_FN);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 17), $found);
    }

    /**
     * Test simple match expression case.
     *
     * @return void
     */
    public function testMatchCase()
    {
        $start = $this->getTargetToken('/* testMatchCase */', T_LNUMBER);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 5), $found);

        $start = $this->getTargetToken('/* testMatchCase */', T_CONSTANT_ENCAPSED_STRING);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 1), $found);
    }

    /**
     * Test simple match expression default case.
     *
     * @return void
     */
    public function testMatchDefault()
    {
        $start = $this->getTargetToken('/* testMatchDefault */', T_MATCH_DEFAULT);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 4), $found);

        $start = $this->getTargetToken('/* testMatchDefault */', T_CONSTANT_ENCAPSED_STRING);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame($start, $found);
    }

    /**
     * Test multiple comma-separated match expression case values.
     *
     * @return void
     */
    public function testMatchMultipleCase()
    {
        $start = $this->getTargetToken('/* testMatchMultipleCase */', T_LNUMBER);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);
        $this->assertSame(($start + 13), $found);

        $start += 6;
        $found  = BCFile::findEndOfStatement(self::$phpcsFile, $start);
        $this->assertSame(($start + 7), $found);
    }

    /**
     * Test match expression default case with trailing comma.
     *
     * @return void
     */
    public function testMatchDefaultComma()
    {
        $start = $this->getTargetToken('/* testMatchDefaultComma */', T_MATCH_DEFAULT);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 5), $found);
    }

    /**
     * Test match expression with function call.
     *
     * @return void
     */
    public function testMatchFunctionCall()
    {
        $start = $this->getTargetToken('/* testMatchFunctionCall */', T_STRING);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 12), $found);

        $start += 8;
        $found  = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 1), $found);
    }

    /**
     * Test match expression with function call in the arm.
     *
     * @return void
     */
    public function testMatchFunctionCallArm()
    {
        // Check the first case.
        $start = $this->getTargetToken('/* testMatchFunctionCallArm */', T_STRING);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 21), $found);

        // Check the second case.
        $start += 24;
        $found  = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 21), $found);
    }

    /**
     * Test match expression with closure.
     *
     * @return void
     */
    public function testMatchClosure()
    {
        $start = $this->getTargetToken('/* testMatchClosure */', T_LNUMBER);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 14), $found);

        $start += 17;
        $found  = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 14), $found);
    }

    /**
     * Test match expression with array declaration.
     *
     * @return void
     */
    public function testMatchArray()
    {
        $start = $this->getTargetToken('/* testMatchArray */', T_LNUMBER);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 11), $found);

        $start += 14;
        $found  = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 22), $found);
    }

    /**
     * Test nested match expressions.
     *
     * @return void
     */
    public function testNestedMatch()
    {
        $start = $this->getTargetToken('/* testNestedMatch */', T_LNUMBER);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 30), $found);

        $start += 21;
        $found  = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 5), $found);
    }
}
