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

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\FunctionDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\FunctionDeclarations::getParameters method.
 *
 * @covers \PHPCSUtils\Utils\FunctionDeclarations::getParameters
 *
 * @group functiondeclarations
 *
 * @since 1.0.0
 */
class GetParametersParseError2Test extends UtilityMethodTestCase
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
        self::$caseFile = \dirname(\dirname(__DIR__)) . '/BackCompat/BCFile/GetMethodParametersParseError2Test.inc';
        parent::setUpTestFile();
    }

    /**
     * Test receiving an empty array when encountering a specific parse error.
     *
     * @return void
     */
    public function testParseError()
    {
        $target = $this->getTargetToken('/* testParseError */', Collections::functionDeclarationTokens());
        $result = FunctionDeclarations::getParameters(self::$phpcsFile, $target);

        $this->assertSame([], $result);
    }
}
