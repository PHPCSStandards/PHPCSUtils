<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\BackCompat\BCFile;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::findStartOfStatement method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::findStartOfStatement
 *
 * @since 1.0.0
 */
final class FindStartOfStatementTest extends UtilityMethodTestCase
{

    /**
     * Test that start of statement is NEVER beyond the "current" token.
     *
     * @return void
     */
    public function testStartIsNeverMoreThanCurrentToken()
    {
        $tokens = self::$phpcsFile->getTokens();
        $errors = [];

        for ($i = 0; $i < self::$phpcsFile->numTokens; $i++) {
            if (isset(Tokens::$emptyTokens[$tokens[$i]['code']]) === true) {
                continue;
            }

            $start = BCFile::findStartOfStatement(self::$phpcsFile, $i);

            // Collect all the errors.
            if ($start > $i) {
                $errors[] = sprintf(
                    'Start of statement for token %1$d (%2$s: %3$s) on line %4$d is %5$d (%6$s), which is more than %1$d',
                    $i,
                    $tokens[$i]['type'],
                    $tokens[$i]['content'],
                    $tokens[$i]['line'],
                    $start,
                    $tokens[$start]['type']
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
        $start = $this->getTargetToken('/* testSimpleAssignment */', T_SEMICOLON);
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 5), $found);
    }

    /**
     * Test a function call.
     *
     * @return void
     */
    public function testFunctionCall()
    {
        $start = $this->getTargetToken('/* testFunctionCall */', T_CLOSE_PARENTHESIS);
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 6), $found);
    }

    /**
     * Test a function call.
     *
     * @return void
     */
    public function testFunctionCallArgument()
    {
        $start = $this->getTargetToken('/* testFunctionCallArgument */', T_VARIABLE, '$b');
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame($start, $found);
    }

    /**
     * Test a direct call to a control structure.
     *
     * @return void
     */
    public function testControlStructure()
    {
        $start = $this->getTargetToken('/* testControlStructure */', T_CLOSE_CURLY_BRACKET);
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 6), $found);
    }

    /**
     * Test the assignment of a closure.
     *
     * @return void
     */
    public function testClosureAssignment()
    {
        $start = $this->getTargetToken('/* testClosureAssignment */', T_CLOSE_CURLY_BRACKET);
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 11), $found);
    }

    /**
     * Test using a heredoc in a function argument.
     *
     * @return void
     */
    public function testHeredocFunctionArg()
    {
        // Find the start of the function.
        $start = $this->getTargetToken('/* testHeredocFunctionArg */', T_SEMICOLON);
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 10), $found);

        // Find the start of the heredoc.
        $start -= 4;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 4), $found);

        // Find the start of the last arg.
        $start += 2;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame($start, $found);
    }

    /**
     * Test parts of a switch statement.
     *
     * @return void
     */
    public function testSwitch()
    {
        // Find the start of the switch.
        $start = $this->getTargetToken('/* testSwitch */', T_CLOSE_CURLY_BRACKET);
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 47), $found);

        // Find the start of default case.
        $start -= 5;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 6), $found);

        // Find the start of the second case.
        $start -= 12;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 5), $found);

        // Find the start of the first case.
        $start -= 13;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 8), $found);

        // Test inside the first case.
        --$start;
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 1), $found);
    }

    /**
     * Test statements that are array values.
     *
     * @return void
     */
    public function testStatementAsArrayValue()
    {
        // Test short array syntax.
        $start = $this->getTargetToken('/* testStatementAsArrayValue */', T_STRING, 'Datetime');
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 2), $found);

        // Test long array syntax.
        $start += 12;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 2), $found);

        // Test same statement outside of array.
        ++$start;
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 9), $found);

        // Test with an array index.
        $start += 17;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 5), $found);
    }

    /**
     * Test a use group.
     *
     * @return void
     */
    public function testUseGroup()
    {
        $start    = $this->getTargetToken('/* testUseGroup */', T_SEMICOLON);
        $expected = parent::usesPhp8NameTokens() ? ($start - 21) : ($start - 23);
        $found    = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame($expected, $found);
    }

    /**
     * Test arrow function as array value.
     *
     * @return void
     */
    public function testArrowFunctionArrayValue()
    {
        $start = $this->getTargetToken('/* testArrowFunctionArrayValue */', T_COMMA);
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 7), $found);
    }

    /**
     * Test static arrow function.
     *
     * @return void
     */
    public function testStaticArrowFunction()
    {
        $start = $this->getTargetToken('/* testStaticArrowFunction */', T_SEMICOLON);
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 11), $found);
    }

    /**
     * Test arrow function with return value.
     *
     * @return void
     */
    public function testArrowFunctionReturnValue()
    {
        $start = $this->getTargetToken('/* testArrowFunctionReturnValue */', T_SEMICOLON);
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 18), $found);
    }

    /**
     * Test arrow function used as a function argument.
     *
     * @return void
     */
    public function testArrowFunctionAsArgument()
    {
        $start  = $this->getTargetToken('/* testArrowFunctionAsArgument */', T_FN);
        $start += 8;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 8), $found);
    }

    /**
     * Test arrow function with arrays used as a function argument.
     *
     * @return void
     */
    public function testArrowFunctionWithArrayAsArgument()
    {
        $start  = $this->getTargetToken('/* testArrowFunctionWithArrayAsArgument */', T_FN);
        $start += 17;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 17), $found);
    }

    /**
     * Test simple match expression case.
     *
     * @return void
     */
    public function testMatchCase()
    {
        $start = $this->getTargetToken('/* testMatchCase */', T_COMMA);
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 1), $found);
    }

    /**
     * Test simple match expression default case.
     *
     * @return void
     */
    public function testMatchDefault()
    {
        $start = $this->getTargetToken('/* testMatchDefault */', T_CONSTANT_ENCAPSED_STRING, "'bar'");
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame($start, $found);
    }

    /**
     * Test multiple comma-separated match expression case values.
     *
     * @return void
     */
    public function testMatchMultipleCase()
    {
        $start = $this->getTargetToken('/* testMatchMultipleCase */', T_MATCH_ARROW);
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 6), $found);

        $start += 6;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 4), $found);
    }

    /**
     * Test match expression default case with trailing comma.
     *
     * @return void
     */
    public function testMatchDefaultComma()
    {
        $start = $this->getTargetToken('/* testMatchDefaultComma */', T_MATCH_ARROW);
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 3), $found);

        $start += 2;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame($start, $found);
    }

    /**
     * Test match expression with function call.
     *
     * @return void
     */
    public function testMatchFunctionCall()
    {
        $start = $this->getTargetToken('/* testMatchFunctionCall */', T_CLOSE_PARENTHESIS);
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 6), $found);
    }

    /**
     * Test match expression with function call in the arm.
     *
     * @return void
     */
    public function testMatchFunctionCallArm()
    {
        // Check the first case.
        $start = $this->getTargetToken('/* testMatchFunctionCallArm */', T_MATCH_ARROW);
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 18), $found);

        // Check the second case.
        $start += 24;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 18), $found);
    }

    /**
     * Test match expression with closure.
     *
     * @return void
     */
    public function testMatchClosure()
    {
        $start  = $this->getTargetToken('/* testMatchClosure */', T_LNUMBER);
        $start += 14;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 10), $found);

        $start += 17;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 10), $found);
    }

    /**
     * Test match expression with array declaration.
     *
     * @return void
     */
    public function testMatchArray()
    {
        // Start of first case statement.
        $start = $this->getTargetToken('/* testMatchArray */', T_LNUMBER);
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);
        $this->assertSame($start, $found);

        // Comma after first statement.
        $start += 11;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);
        $this->assertSame(($start - 7), $found);

        // Start of second case statement.
        $start += 3;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);
        $this->assertSame($start, $found);

        // Comma after first statement.
        $start += 30;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);
        $this->assertSame(($start - 26), $found);
    }

    /**
     * Test nested match expressions.
     *
     * @return void
     */
    public function testNestedMatch()
    {
        $start  = $this->getTargetToken('/* testNestedMatch */', T_LNUMBER);
        $start += 30;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 26), $found);

        $start -= 4;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 1), $found);

        $start -= 3;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 2), $found);
    }

    /**
     * Test full PHP open tag.
     *
     * @return void
     */
    public function testOpenTag()
    {
        $start  = $this->getTargetToken('/* testOpenTag */', T_OPEN_TAG);
        $start += 2;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 1), $found);
    }

    /**
     * Test PHP open tag with echo.
     *
     * @return void
     */
    public function testOpenTagWithEcho()
    {
        $start  = $this->getTargetToken('/* testOpenTagWithEcho */', T_OPEN_TAG_WITH_ECHO);
        $start += 3;
        $found  = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame(($start - 1), $found);
    }

    /**
     * Test object call on result of static function call with arrow function as parameter and wrapped within an array.
     *
     * @link https://github.com/squizlabs/PHP_CodeSniffer/issues/2849
     * @link https://github.com/squizlabs/PHP_CodeSniffer/commit/fbf67efc3fc0c2a355f5585d49f4f6fe160ff2f9
     *
     * @return void
     */
    public function testObjectCallPrecededByArrowFunctionAsFunctionCallParameterInArray()
    {
        $expected = $this->getTargetToken('/* testPrecededByArrowFunctionInArray - Expected */', \T_STRING, 'Url');

        $start = $this->getTargetToken('/* testPrecededByArrowFunctionInArray */', \T_STRING, 'onlyOnDetail');
        $found = BCFile::findStartOfStatement(self::$phpcsFile, $start);

        $this->assertSame($expected, $found);
    }

    /**
     * Test finding the start of a statement inside a switch control structure case/default statement.
     *
     * @link https://github.com/squizlabs/php_codesniffer/issues/3192
     * @link https://github.com/squizlabs/PHP_CodeSniffer/pull/3186/commits/18a0e54735bb9b3850fec266e5f4c50dacf618ea
     *
     * @dataProvider dataFindStartInsideSwitchCaseDefaultStatements
     *
     * @param string     $testMarker     The comment which prefaces the target token in the test file.
     * @param int|string $targets        The token to search for after the test marker.
     * @param string|int $expectedTarget Token code of the expected start of statement stack pointer.
     *
     * @return void
     */
    public function testFindStartInsideSwitchCaseDefaultStatements($testMarker, $targets, $expectedTarget)
    {
        $testToken = $this->getTargetToken($testMarker, $targets);
        $expected  = $this->getTargetToken($testMarker, $expectedTarget);

        $found = BCFile::findStartOfStatement(self::$phpcsFile, $testToken);

        $this->assertSame($expected, $found);
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataFindStartInsideSwitchCaseDefaultStatements()
    {
        return [
            'Case keyword should be start of case statement - case itself' => [
                'testMarker'     => '/* testCaseStatement */',
                'targets'        => \T_CASE,
                'expectedTarget' => \T_CASE,
            ],
            'Case keyword should be start of case statement - number (what\'s being compared)' => [
                'testMarker'     => '/* testCaseStatement */',
                'targets'        => \T_LNUMBER,
                'expectedTarget' => \T_CASE,
            ],
            'Variable should be start of arbitrary assignment statement - variable itself' => [
                'testMarker'     => '/* testInsideCaseStatement */',
                'targets'        => \T_VARIABLE,
                'expectedTarget' => \T_VARIABLE,
            ],
            'Variable should be start of arbitrary assignment statement - equal sign' => [
                'testMarker'     => '/* testInsideCaseStatement */',
                'targets'        => \T_EQUAL,
                'expectedTarget' => \T_VARIABLE,
            ],
            'Variable should be start of arbitrary assignment statement - function call' => [
                'testMarker'     => '/* testInsideCaseStatement */',
                'targets'        => \T_STRING,
                'expectedTarget' => \T_VARIABLE,
            ],
            'Break should be start for contents of the break statement - contents' => [
                'testMarker'     => '/* testInsideCaseBreakStatement */',
                'targets'        => \T_LNUMBER,
                'expectedTarget' => \T_BREAK,
            ],
            'Continue should be start for contents of the continue statement - contents' => [
                'testMarker'     => '/* testInsideCaseContinueStatement */',
                'targets'        => \T_LNUMBER,
                'expectedTarget' => \T_CONTINUE,
            ],
            'Return should be start for contents of the return statement - contents' => [
                'testMarker'     => '/* testInsideCaseReturnStatement */',
                'targets'        => \T_FALSE,
                'expectedTarget' => \T_RETURN,
            ],
            'Exit should be start for contents of the exit statement - close parenthesis' => [
                // Note: not sure if this is actually correct - should this be the open parenthesis ?
                'testMarker'     => '/* testInsideCaseExitStatement */',
                'targets'        => \T_CLOSE_PARENTHESIS,
                'expectedTarget' => \T_EXIT,
            ],
            'Throw should be start for contents of the throw statement - new keyword' => [
                'testMarker'     => '/* testInsideCaseThrowStatement */',
                'targets'        => \T_NEW,
                'expectedTarget' => \T_THROW,
            ],
            'Throw should be start for contents of the throw statement - exception name' => [
                'testMarker'     => '/* testInsideCaseThrowStatement */',
                'targets'        => \T_STRING,
                'expectedTarget' => \T_THROW,
            ],
            'Throw should be start for contents of the throw statement - close parenthesis' => [
                'testMarker'     => '/* testInsideCaseThrowStatement */',
                'targets'        => \T_CLOSE_PARENTHESIS,
                'expectedTarget' => \T_THROW,
            ],
            'Default keyword should be start of default statement - default itself' => [
                'testMarker'     => '/* testDefaultStatement */',
                'targets'        => \T_DEFAULT,
                'expectedTarget' => \T_DEFAULT,
            ],
            'Return should be start for contents of the return statement (inside default) - variable' => [
                'testMarker'     => '/* testInsideDefaultContinueStatement */',
                'targets'        => \T_VARIABLE,
                'expectedTarget' => \T_CONTINUE,
            ],
        ];
    }

    /**
     * Test finding the start of a statement inside a closed scope nested within a match expressions.
     *
     * @param string     $testMarker     The comment which prefaces the target token in the test file.
     * @param int|string $target         The token to search for after the test marker.
     * @param int|string $expectedTarget Token code of the expected start of statement stack pointer.
     *
     * @link https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/437
     *
     * @dataProvider dataFindStartInsideClosedScopeNestedWithinMatch
     *
     * @return void
     */
    public function testFindStartInsideClosedScopeNestedWithinMatch($testMarker, $target, $expectedTarget)
    {
        $testToken = $this->getTargetToken($testMarker, $target);
        $expected  = $this->getTargetToken($testMarker, $expectedTarget);

        $found = BCFile::findStartOfStatement(self::$phpcsFile, $testToken);

        $this->assertSame($expected, $found);
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataFindStartInsideClosedScopeNestedWithinMatch()
    {
        return [
            // These were already working correctly.
            'Closure function keyword should be start of closure - closure keyword'                   => [
                'testMarker'     => '/* test437ClosureDeclaration */',
                'target'         => T_CLOSURE,
                'expectedTarget' => T_CLOSURE,
            ],
            'Open curly is a statement/expression opener - open curly'                                => [
                'testMarker'     => '/* test437ClosureDeclaration */',
                'target'         => T_OPEN_CURLY_BRACKET,
                'expectedTarget' => T_OPEN_CURLY_BRACKET,
            ],

            'Echo should be start for expression - echo keyword'                                      => [
                'testMarker'     => '/* test437EchoNestedWithinClosureWithinMatch */',
                'target'         => T_ECHO,
                'expectedTarget' => T_ECHO,
            ],
            'Echo should be start for expression - variable'                                          => [
                'testMarker'     => '/* test437EchoNestedWithinClosureWithinMatch */',
                'target'         => T_VARIABLE,
                'expectedTarget' => T_ECHO,
            ],
            'Echo should be start for expression - comma'                                             => [
                'testMarker'     => '/* test437EchoNestedWithinClosureWithinMatch */',
                'target'         => T_COMMA,
                'expectedTarget' => T_ECHO,
            ],

            // These were not working correctly and would previously return the close curly of the match expression.
            'First token after comma in echo expression should be start for expression - text string' => [
                'testMarker'     => '/* test437EchoNestedWithinClosureWithinMatch */',
                'target'         => T_CONSTANT_ENCAPSED_STRING,
                'expectedTarget' => T_CONSTANT_ENCAPSED_STRING,
            ],
            'First token after comma in echo expression - PHP_EOL constant'                           => [
                'testMarker'     => '/* test437EchoNestedWithinClosureWithinMatch */',
                'target'         => T_STRING,
                'expectedTarget' => T_STRING,
            ],
            'First token after comma in echo expression - semicolon'                                  => [
                'testMarker'     => '/* test437EchoNestedWithinClosureWithinMatch */',
                'target'         => T_SEMICOLON,
                'expectedTarget' => T_STRING,
            ],
        ];
    }

    /**
     * Test finding the start of a statement for a token within a set of parentheses within a match expressions.
     *
     * @param string     $testMarker     The comment which prefaces the target token in the test file.
     * @param int|string $target         The token to search for after the test marker.
     * @param int|string $expectedTarget Token code of the expected start of statement stack pointer.
     *
     * @link https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/437
     *
     * @dataProvider dataFindStartInsideParenthesesNestedWithinMatch
     *
     * @return void
     */
    public function testFindStartInsideParenthesesNestedWithinMatch($testMarker, $target, $expectedTarget)
    {
        $testToken = $this->getTargetToken($testMarker, $target);
        $expected  = $this->getTargetToken($testMarker, $expectedTarget);

        $found = BCFile::findStartOfStatement(self::$phpcsFile, $testToken);

        $this->assertSame($expected, $found);
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataFindStartInsideParenthesesNestedWithinMatch()
    {
        return [
            'Array item itself should be start for first array item'                       => [
                'testMarker'     => '/* test437NestedLongArrayWithinMatch */',
                'target'         => T_LNUMBER,
                'expectedTarget' => T_LNUMBER,
            ],
            'Array item itself should be start for second array item'                      => [
                'testMarker'     => '/* test437NestedLongArrayWithinMatch */',
                'target'         => T_DNUMBER,
                'expectedTarget' => T_DNUMBER,
            ],
            'Array item itself should be start for third array item'                       => [
                'testMarker'     => '/* test437NestedLongArrayWithinMatch */',
                'target'         => T_VARIABLE,
                'expectedTarget' => T_VARIABLE,
            ],

            'Parameter itself should be start for first param passed to function call'     => [
                'testMarker'     => '/* test437NestedFunctionCallWithinMatch */',
                'target'         => T_LNUMBER,
                'expectedTarget' => T_LNUMBER,
            ],
            'Parameter itself should be start for second param passed to function call'    => [
                'testMarker'     => '/* test437NestedFunctionCallWithinMatch */',
                'target'         => T_VARIABLE,
                'expectedTarget' => T_VARIABLE,
            ],
            'Parameter itself should be start for third param passed to function call'     => [
                'testMarker'     => '/* test437NestedFunctionCallWithinMatch */',
                'target'         => T_DNUMBER,
                'expectedTarget' => T_DNUMBER,
            ],

            'Parameter itself should be start for first param declared in arrow function'  => [
                'testMarker'     => '/* test437NestedArrowFunctionWithinMatch */',
                'target'         => T_VARIABLE,
                'expectedTarget' => T_VARIABLE,
            ],
            'Parameter itself should be start for second param declared in arrow function' => [
                'testMarker'     => '/* test437FnSecondParamWithinMatch */',
                'target'         => T_VARIABLE,
                'expectedTarget' => T_VARIABLE,
            ],
        ];
    }

    /**
     * Test finding the start of a statement for a token within a set of parentheses within a match expressions,
     * which itself is nested within parentheses.
     *
     * @param string     $testMarker     The comment which prefaces the target token in the test file.
     * @param int|string $target         The token to search for after the test marker.
     * @param int|string $expectedTarget Token code of the expected start of statement stack pointer.
     *
     * @link https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/437
     *
     * @dataProvider dataFindStartInsideParenthesesNestedWithinNestedMatch
     *
     * @return void
     */
    public function testFindStartInsideParenthesesNestedWithinNestedMatch($testMarker, $target, $expectedTarget)
    {
        $testToken = $this->getTargetToken($testMarker, $target);
        $expected  = $this->getTargetToken($testMarker, $expectedTarget);

        $found = BCFile::findStartOfStatement(self::$phpcsFile, $testToken);

        $this->assertSame($expected, $found);
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataFindStartInsideParenthesesNestedWithinNestedMatch()
    {
        return [
            'Array item itself should be start for first array item'                       => [
                'testMarker'     => '/* test437NestedLongArrayWithinNestedMatch */',
                'target'         => T_LNUMBER,
                'expectedTarget' => T_LNUMBER,
            ],
            'Array item itself should be start for second array item'                      => [
                'testMarker'     => '/* test437NestedLongArrayWithinNestedMatch */',
                'target'         => T_DNUMBER,
                'expectedTarget' => T_DNUMBER,
            ],
            'Array item itself should be start for third array item'                       => [
                'testMarker'     => '/* test437NestedLongArrayWithinNestedMatch */',
                'target'         => T_VARIABLE,
                'expectedTarget' => T_VARIABLE,
            ],

            'Parameter itself should be start for first param passed to function call'     => [
                'testMarker'     => '/* test437NestedFunctionCallWithinNestedMatch */',
                'target'         => T_LNUMBER,
                'expectedTarget' => T_LNUMBER,
            ],
            'Parameter itself should be start for second param passed to function call'    => [
                'testMarker'     => '/* test437NestedFunctionCallWithinNestedMatch */',
                'target'         => T_VARIABLE,
                'expectedTarget' => T_VARIABLE,
            ],
            'Parameter itself should be start for third param passed to function call'     => [
                'testMarker'     => '/* test437NestedFunctionCallWithinNestedMatch */',
                'target'         => T_DNUMBER,
                'expectedTarget' => T_DNUMBER,
            ],

            'Parameter itself should be start for first param declared in arrow function'  => [
                'testMarker'     => '/* test437NestedArrowFunctionWithinNestedMatch */',
                'target'         => T_VARIABLE,
                'expectedTarget' => T_VARIABLE,
            ],
            'Parameter itself should be start for second param declared in arrow function' => [
                'testMarker'     => '/* test437FnSecondParamWithinNestedMatch */',
                'target'         => T_VARIABLE,
                'expectedTarget' => T_VARIABLE,
            ],
        ];
    }

    /**
     * Test finding the start of a statement for a token within a short array within a match expressions.
     *
     * @param string     $testMarker     The comment which prefaces the target token in the test file.
     * @param int|string $target         The token to search for after the test marker.
     * @param int|string $expectedTarget Token code of the expected start of statement stack pointer.
     *
     * @link https://github.com/PHPCSStandards/PHP_CodeSniffer/issues/437
     *
     * @dataProvider dataFindStartInsideShortArrayNestedWithinMatch
     *
     * @return void
     */
    public function testFindStartInsideShortArrayNestedWithinMatch($testMarker, $target, $expectedTarget)
    {
        $testToken = $this->getTargetToken($testMarker, $target);
        $expected  = $this->getTargetToken($testMarker, $expectedTarget);

        $found = BCFile::findStartOfStatement(self::$phpcsFile, $testToken);

        $this->assertSame($expected, $found);
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataFindStartInsideShortArrayNestedWithinMatch()
    {
        return [
            'Array item itself should be start for first array item'  => [
                'testMarker'     => '/* test437NestedShortArrayWithinMatch */',
                'target'         => T_LNUMBER,
                'expectedTarget' => T_LNUMBER,
            ],
            'Array item itself should be start for second array item' => [
                'testMarker'     => '/* test437NestedShortArrayWithinMatch */',
                'target'         => T_DNUMBER,
                'expectedTarget' => T_DNUMBER,
            ],
            'Array item itself should be start for third array item'  => [
                'testMarker'     => '/* test437NestedShortArrayWithinMatch */',
                'target'         => T_VARIABLE,
                'expectedTarget' => T_VARIABLE,
            ],
        ];
    }
}
