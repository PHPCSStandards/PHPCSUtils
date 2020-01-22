<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\FunctionDeclarations;

use PHPCSUtils\Tests\BackCompat\BCFile\GetMethodPropertiesTest as BCFile_GetMethodPropertiesTest;
use PHPCSUtils\Utils\FunctionDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\FunctionDeclarations::getProperties method.
 *
 * @covers \PHPCSUtils\Utils\FunctionDeclarations::getProperties
 *
 * @group functiondeclarations
 *
 * @since 1.0.0
 */
class GetPropertiesTest extends BCFile_GetMethodPropertiesTest
{

    /**
     * Full path to the test case file associated with this test class.
     *
     * @var string
     */
    protected static $caseFile = '';

    /**
     * Initialize PHPCS & tokenize the test case file.
     *
     * Overloaded to re-use the `$caseFile` from the BCFile test.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = \dirname(\dirname(__DIR__)) . '/BackCompat/BCFile/GetMethodPropertiesTest.inc';
        parent::setUpTestFile();
    }

    /**
     * Test receiving an expected exception when a non function token is passed.
     *
     * @return void
     */
    public function testNotAFunctionException()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_FUNCTION or T_CLOSURE');

        $next = $this->getTargetToken('/* testNotAFunction */', \T_RETURN);
        FunctionDeclarations::getProperties(self::$phpcsFile, $next);
    }

    /**
     * Test helper.
     *
     * @param string $commentString The comment which preceeds the test.
     * @param array  $expected      The expected function output.
     *
     * @return void
     */
    protected function getMethodPropertiesTestHelper($commentString, $expected)
    {
        $function = $this->getTargetToken($commentString, [\T_FUNCTION, \T_CLOSURE]);
        $found    = FunctionDeclarations::getProperties(self::$phpcsFile, $function);

        if ($expected['return_type_token'] !== false) {
            $expected['return_type_token'] += $function;
        }

        $this->assertSame($expected, $found);
    }
}
