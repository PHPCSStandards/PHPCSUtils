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
        $start = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testSimpleAssignment */') + 2);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 5)], $tokens[$found]);
    }

    /**
     * Test a direct call to a control structure.
     *
     * @return void
     */
    public function testControlStructure()
    {
        $start = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testControlStructure */') + 2);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 6)], $tokens[$found]);
    }

    /**
     * Test the assignment of a closure.
     *
     * @return void
     */
    public function testClosureAssignment()
    {
        $start = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testClosureAssignment */') + 2);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 13)], $tokens[$found]);
    }

    /**
     * Test using a heredoc in a function argument.
     *
     * @return void
     */
    public function testHeredocFunctionArg()
    {
        // Find the end of the function.
        $start = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testHeredocFunctionArg */') + 2);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 10)], $tokens[$found]);

        // Find the end of the heredoc.
        $start += 2;
        $found  = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 4)], $tokens[$found]);

        // Find the end of the last arg.
        $start = ($found + 2);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[$start], $tokens[$found]);
    }

    /**
     * Test parts of a switch statement.
     *
     * @return void
     */
    public function testSwitch()
    {
        // Find the end of the switch.
        $start = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testSwitch */') + 2);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 28)], $tokens[$found]);

        // Find the end of the case.
        $start += 9;
        $found  = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 8)], $tokens[$found]);

        // Find the end of default case.
        $start += 11;
        $found  = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 6)], $tokens[$found]);
    }

    /**
     * Test statements that are array values.
     *
     * @return void
     */
    public function testStatementAsArrayValue()
    {
        // Test short array syntax.
        $start = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testStatementAsArrayValue */') + 7);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 2)], $tokens[$found]);

        // Test long array syntax.
        $start += 12;
        $found  = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 2)], $tokens[$found]);

        // Test same statement outside of array.
        $start += 10;
        $found  = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 3)], $tokens[$found]);
    }

    /**
     * Test a use group.
     *
     * @return void
     */
    public function testUseGroup()
    {
        $start = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testUseGroup */') + 2);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 23)], $tokens[$found]);
    }

    /**
     * Test arrow function as array value.
     *
     * @return void
     */
    public function testArrowFunctionArrayValue()
    {
        $start = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testArrowFunctionArrayValue */') + 7);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertSame($tokens[($start + 9)], $tokens[$found]);
    }

    /**
     * Test static arrow function.
     *
     * @return void
     */
    public function testStaticArrowFunction()
    {
        $fnTargets = Collections::arrowFunctionTokensBC();

        $static = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testStaticArrowFunction */') + 2);
        $fn     = self::$phpcsFile->findNext($fnTargets, ($static + 1));

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
        $start = (self::$phpcsFile->findNext(T_COMMENT, 0, null, false, '/* testArrowFunctionReturnValue */') + 2);
        $found = BCFile::findEndOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start + 18), $found);
    }
}
