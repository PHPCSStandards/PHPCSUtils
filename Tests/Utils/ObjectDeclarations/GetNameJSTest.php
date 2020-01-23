<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\ObjectDeclarations;

use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\Tests\BackCompat\BCFile\GetDeclarationNameJSTest as BCFile_GetDeclarationNameJSTest;
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
class GetNameJSTest extends BCFile_GetDeclarationNameJSTest
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
        self::$caseFile = \dirname(\dirname(__DIR__)) . '/BackCompat/BCFile/GetDeclarationNameJSTest.js';
        parent::setUpTestFile();
    }

    /**
     * Test receiving an expected exception when a non-supported token is passed.
     *
     * @return void
     */
    public function testInvalidTokenPassed()
    {
        $this->expectPhpcsException('Token type "T_STRING" is not T_FUNCTION, T_CLASS, T_INTERFACE or T_TRAIT');

        $target = $this->getTargetToken('/* testInvalidTokenPassed */', \T_STRING);
        ObjectDeclarations::getName(self::$phpcsFile, $target);
    }

    /**
     * Test receiving "null" when passed an anonymous construct or in case of a parse error.
     *
     * {@internal Method name not adjusted as otherwise it wouldn't overload the parent method.}
     *
     * @dataProvider dataGetDeclarationNameNull
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType Token type of the token to get as stackPtr.
     *
     * @return void
     */
    public function testGetDeclarationNameNull($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $result = ObjectDeclarations::getName(self::$phpcsFile, $target);
        $this->assertNull($result);
    }

    /**
     * Test retrieving the name of a function or OO structure.
     *
     * {@internal Method name not adjusted as otherwise it wouldn't overload the parent method.}
     *
     * @dataProvider dataGetDeclarationName
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param string     $expected   Expected function output.
     * @param int|string $targetType Token type of the token to get as stackPtr.
     *
     * @return void
     */
    public function testGetDeclarationName($testMarker, $expected, $targetType = null)
    {
        if (isset($targetType) === false) {
            $targetType = [\T_CLASS, \T_INTERFACE, \T_TRAIT, \T_FUNCTION];
        }

        $target = $this->getTargetToken($testMarker, $targetType);
        $result = ObjectDeclarations::getName(self::$phpcsFile, $target);
        $this->assertSame($expected, $result);
    }

    /**
     * Test retrieving the name of JS ES6 class method.
     *
     * {@internal Method name not adjusted as otherwise it wouldn't overload the parent method.}
     *
     * @return void
     */
    public function testGetDeclarationNameES6Method()
    {
        if (\version_compare(Helper::getVersion(), '3.0.0', '<') === true) {
            $this->markTestSkipped('Support for JS ES6 method has not been backfilled for PHPCS 2.x (yet)');
        }

        $target = $this->getTargetToken('/* testMethod */', [\T_CLASS, \T_INTERFACE, \T_TRAIT, \T_FUNCTION]);
        $result = ObjectDeclarations::getName(self::$phpcsFile, $target);
        $this->assertSame('methodName', $result);
    }
}
