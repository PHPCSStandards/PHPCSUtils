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

 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */

namespace PHPCSUtils\Tests\BackCompat\BCFile;

use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Tokens\Collections;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::findEndOfStatement method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::findEndOfStatement
 *
 * @since 1.0.0
 */
class FindEndOfStatementTest extends UtilityMethodTestCase
{

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

        $this->assertSame(($start + 23), $found);
    }

    /**
     * Test arrow function as array value.
     *
     * @return void
     */
    public function testArrowFunctionArrayValue()
    {
        $start = $this->getTargetToken('/* testArrowFunctionArrayValue */', Collections::arrowFunctionTokensBC());
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
        $fn     = $this->getTargetToken('/* testStaticArrowFunction */', Collections::arrowFunctionTokensBC());

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
        $start = $this->getTargetToken('/* testArrowFunctionReturnValue */', Collections::arrowFunctionTokensBC());
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 18), $found);
    }
}
