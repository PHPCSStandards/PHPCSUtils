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
     * Initialize PHPCS & tokenize the test case file.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = __DIR__ . '/SetUpTestFileTest.inc';
        parent::setUpTestFile();
    }

    /**
     * Test that the class is correctly reset.
     *
     * @return void
     */
    public function testTearDown()
    {
        parent::resetTestFile();
        $this->assertSame('0', self::$phpcsVersion, 'phpcsVersion was not reset');
        $this->assertSame('inc', self::$fileExtension, 'fileExtension was not reset');
        $this->assertSame('', self::$caseFile, 'caseFile was not reset');
        $this->assertSame(4, self::$tabWidth, 'tabWidth was not reset');
        $this->assertNull(self::$phpcsFile, 'phpcsFile was not reset');
        $this->assertSame(['Dummy.Dummy.Dummy'], self::$selectedSniff, 'selectedSniff was not reset');
    }
}
