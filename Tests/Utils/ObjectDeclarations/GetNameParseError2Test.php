<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\ObjectDeclarations;

use PHPCSUtils\Tests\BackCompat\BCFile\GetDeclarationNameParseError2Test as BCFile_GetDeclarationNameParseError2Test;
use PHPCSUtils\Utils\ObjectDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\ObjectDeclarations::getName() method.
 *
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getName
 *
 * @group objectdeclarations
 *
 * @since 1.0.0
 */
final class GetNameParseError2Test extends BCFile_GetDeclarationNameParseError2Test
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
        self::$caseFile = \dirname(\dirname(__DIR__)) . '/BackCompat/BCFile/GetDeclarationNameParseError2Test.inc';
        parent::setUpTestFile();
    }

    /**
     * Test receiving "null" in case of a parse error.
     *
     * Note: the upstream and the BCFile method no longer returns `null`, but an empty string.
     * For PHPCSUtils, this change needs to wait for the next major.
     *
     * @dataProvider dataGetDeclarationName
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType Token type of the token to get as stackPtr.
     *
     * @return void
     */
    public function testGetDeclarationName($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $result = ObjectDeclarations::getName(self::$phpcsFile, $target);
        $this->assertNull($result);
    }
}
