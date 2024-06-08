<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\TestUtils\UtilityMethodTestCase;

use PHPCSUtils\Tests\PolyfilledTestCase;

/**
 * Tests for the \PHPCSUtils\TestUtils\UtilityMethodTestCase class.
 *
 * @covers \PHPCSUtils\TestUtils\UtilityMethodTestCase::resetTestFile
 *
 * @since 1.0.0
 */
final class ResetTestFileTest extends PolyfilledTestCase
{

    /**
     * Overload the "normal" set up as it needs to be run from within the actual test(s) to ensure we have a valid test.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = __DIR__ . '/SetUpTestFileTest.inc';
        // Deliberately not running the actual setUpTestFile() method.
    }

    /**
     * Test that the static class properties in the class are correctly reset.
     *
     * @return void
     */
    public function testTearDown()
    {
        // Initialize a test, which should change the values of most static properties.
        self::$tabWidth      = 2;
        self::$selectedSniff = ['Test.Test.Test'];
        parent::setUpTestFile();

        // Verify that (most) properties no longer have their original value.
        $this->assertNotSame('0', self::$phpcsVersion, 'phpcsVersion was not updated');
        $this->assertSame('inc', self::$fileExtension, 'fileExtension was (not) updated');
        $this->assertNotSame('', self::$caseFile, 'caseFile was not updated');
        $this->assertNotSame(4, self::$tabWidth, 'tabWidth was not updated');
        $this->assertNotNull(self::$phpcsFile, 'phpcsFile was not updated');
        $this->assertNotSame(['Dummy.Dummy.Dummy'], self::$selectedSniff, 'selectedSniff was not updated');

        // Reset the file as per the "afterClass"/tear down method.
        parent::resetTestFile();

        // Verify the properties in the class have been cleaned up.
        $this->assertSame('0', self::$phpcsVersion, 'phpcsVersion was not reset');
        $this->assertSame('inc', self::$fileExtension, 'fileExtension was not reset');
        $this->assertSame('', self::$caseFile, 'caseFile was not reset');
        $this->assertSame(4, self::$tabWidth, 'tabWidth was not reset');
        $this->assertNull(self::$phpcsFile, 'phpcsFile was not reset');
        $this->assertSame(['Dummy.Dummy.Dummy'], self::$selectedSniff, 'selectedSniff was not reset');
    }
}
