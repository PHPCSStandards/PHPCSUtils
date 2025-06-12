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
use PHPCSUtils\Tests\Utils\ObjectDeclarations\AnalyzeOOStructure\ParseErrorTestCase;
use PHPCSUtils\Utils\ObjectDeclarations;

/**
 * Test for the \PHPCSUtils\Utils\ObjectDeclarations::analyzeOOStructure method.
 *
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getDeclaredEnumCases
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getDeclaredConstants
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getDeclaredProperties
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getDeclaredMethods
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::analyzeOOStructure
 *
 * @since 1.1.0
 */
final class ParseError7Test extends ParseErrorTestCase
{

    /**
     * Test retrieving the cases declared in an enum.
     *
     * @return void
     */
    public function testGetDeclaredEnumCases()
    {
        $this->markTestSkipped('Parse error which doesn\'t involve an enum');
    }

    /**
     * Test retrieving the methods declared in an OO structure.
     *
     * @return void
     */
    public function testGetDeclaredMethods()
    {
        $expected = [
            'name'    => '/* markerFunction1 */',
            'another' => '/* markerFunction2 */',
        ];

        // Translate the method markers to token pointers.
        foreach ($expected as $name => $marker) {
            $expected[$name] = $this->getTargetToken($marker, [\T_FUNCTION]);
        }

        $stackPtr = $this->getTargetToken('/* testParseError */', Tokens::$ooScopeTokens);
        $result   = ObjectDeclarations::getDeclaredMethods(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }
}
