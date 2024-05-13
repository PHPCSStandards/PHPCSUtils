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

use PHPCSUtils\Tests\BackCompat\BCFile\GetClassPropertiesTest as BCFile_GetClassPropertiesTest;
use PHPCSUtils\Utils\ObjectDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\ObjectDeclarations::getClassProperties() method.
 *
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getClassProperties
 *
 * @group objectdeclarations
 *
 * @since 1.0.0
 */
final class GetClassPropertiesTest extends BCFile_GetClassPropertiesTest
{

    /**
     * The fully qualified name of the class being tested.
     *
     * This allows for the same unit tests to be run for both the BCFile functions
     * as well as for the related PHPCSUtils functions.
     *
     * @var string
     */
    const TEST_CLASS = '\PHPCSUtils\Utils\ObjectDeclarations';

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
        self::$caseFile = \dirname(\dirname(__DIR__)) . '/BackCompat/BCFile/GetClassPropertiesTest.inc';
        parent::setUpTestFile();
    }

    /**
     * Test receiving an expected exception when a non class token is passed.
     *
     * @dataProvider dataNotAClassException
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $tokenType  The type of token to look for after the marker.
     *
     * @return void
     */
    public function testNotAClassException($testMarker, $tokenType)
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type T_CLASS');

        $target = $this->getTargetToken($testMarker, $tokenType);
        ObjectDeclarations::getClassProperties(self::$phpcsFile, $target);
    }

    /**
     * Test retrieving the properties for a class declaration.
     *
     * @dataProvider dataGetClassProperties
     *
     * @param string                  $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, bool|int> $expected   Expected function output.
     *
     * @return void
     */
    public function testGetClassProperties($testMarker, $expected)
    {
        $class = $this->getTargetToken($testMarker, \T_CLASS);

        // Translate offsets to absolute token positions.
        if (\is_int($expected['abstract_token']) === true) {
            $expected['abstract_token'] += $class;
        }
        if (\is_int($expected['final_token']) === true) {
            $expected['final_token'] += $class;
        }
        if (\is_int($expected['readonly_token']) === true) {
            $expected['readonly_token'] += $class;
        }

        $result = ObjectDeclarations::getClassProperties(self::$phpcsFile, $class);
        $this->assertSame($expected, $result);
    }
}
