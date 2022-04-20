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

use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::findStartOfStatement method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::findStartOfStatement
 *
 * @since 1.0.0
 */
class FindStartOfStatementTest extends UtilityMethodTestCase
{

    /**
     * Test object call on result of static function call with arrow function as parameter and wrapped within an array.
     *
     * @link https://github.com/squizlabs/php_codesniffer/issues/2849
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
     * @param string           $testMarker     The comment which prefaces the target token in the test file.
     * @param array|string|int $targets        The token to search for after the test marker.
     * @param string|int       $expectedTarget Token code of the expected start of statement stack pointer.
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
     * @return array
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
}
