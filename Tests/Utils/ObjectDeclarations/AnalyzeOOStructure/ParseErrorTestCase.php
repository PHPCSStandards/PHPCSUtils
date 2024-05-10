<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\ObjectDeclarations\AnalyzeOOStructure;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\ObjectDeclarations;

/**
 * Testcase for the \PHPCSUtils\Utils\ObjectDeclarations::analyzeOOStructure method
 * and its associated get*() methods.
 *
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getDeclaredEnumCases
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getDeclaredConstants
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getDeclaredProperties
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getDeclaredMethods
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::analyzeOOStructure
 *
 * @since 1.1.0
 */
abstract class ParseErrorTestCase extends UtilityMethodTestCase
{

    /**
     * Test retrieving the constants declared in an OO structure.
     *
     * @return void
     */
    public function testGetDeclaredConstants()
    {
        $stackPtr = $this->getTargetToken('/* testParseError */', Tokens::$ooScopeTokens);
        $this->assertSame([], ObjectDeclarations::getDeclaredConstants(self::$phpcsFile, $stackPtr));
    }

    /**
     * Test retrieving the cases declared in an enum.
     *
     * @return void
     */
    public function testGetDeclaredEnumCases()
    {
        $stackPtr = $this->getTargetToken('/* testParseError */', Tokens::$ooScopeTokens);
        $this->assertSame([], ObjectDeclarations::getDeclaredEnumCases(self::$phpcsFile, $stackPtr));
    }

    /**
     * Test retrieving the properties declared in an OO structure.
     *
     * @return void
     */
    public function testGetDeclaredProperties()
    {
        $stackPtr = $this->getTargetToken('/* testParseError */', Tokens::$ooScopeTokens);
        $this->assertSame([], ObjectDeclarations::getDeclaredProperties(self::$phpcsFile, $stackPtr));
    }

    /**
     * Test retrieving the methods declared in an OO structure.
     *
     * @return void
     */
    public function testGetDeclaredMethods()
    {
        $stackPtr = $this->getTargetToken('/* testParseError */', Tokens::$ooScopeTokens);
        $this->assertSame([], ObjectDeclarations::getDeclaredMethods(self::$phpcsFile, $stackPtr));
    }
}
