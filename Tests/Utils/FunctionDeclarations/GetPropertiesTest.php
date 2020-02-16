<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
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
     * @dataProvider dataNotAFunctionException
     *
     * @param string $commentString   The comment which preceeds the test.
     * @param array  $targetTokenType The token type to search for after $commentString.
     *
     * @return void
     */
    public function testNotAFunctionException($commentString, $targetTokenType)
    {
        $this->expectPhpcsException('$stackPtr must be of type T_FUNCTION or T_CLOSURE or an arrow function');

        $next = $this->getTargetToken($commentString, $targetTokenType);
        FunctionDeclarations::getProperties(self::$phpcsFile, $next);
    }

    /**
     * Test helper.
     *
     * @param string $commentString The comment which preceeds the test.
     * @param array  $expected      The expected function output.
     * @param array  $targetType    Optional. The token type to search for after $commentString.
     *                              Defaults to the function/closure tokens.
     *
     * @return void
     */
    protected function getMethodPropertiesTestHelper($commentString, $expected, $targetType = [\T_FUNCTION, \T_CLOSURE])
    {
        $function = $this->getTargetToken($commentString, $targetType);
        $found    = FunctionDeclarations::getProperties(self::$phpcsFile, $function);

        if ($expected['return_type_token'] !== false) {
            $expected['return_type_token'] += $function;
        }

        /*
         * Test the new `return_type_end_token` key which is not in the original datasets.
         */
        $this->assertArrayHasKey('return_type_end_token', $found);
        $this->assertTrue($found['return_type_end_token'] === false || \is_int($found['return_type_end_token']));

        // Remove the array key before doing the compare against the original dataset.
        unset($found['return_type_end_token']);

        $this->assertSame($expected, $found);
    }
}
