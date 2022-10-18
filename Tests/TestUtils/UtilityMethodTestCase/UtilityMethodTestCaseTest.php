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
 * @covers \PHPCSUtils\TestUtils\UtilityMethodTestCase
 *
 * @group testutils
 *
 * @since 1.0.0
 */
final class UtilityMethodTestCaseTest extends PolyfilledTestCase
{

    /**
     * Overload the "normal" set up to avoid the file being tokenized twice which would make
     * the test slower than necessary.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        // Deliberately left empty.
    }

    /**
     * Test that the setUpTestFile() method works correctly.
     *
     * @return void
     */
    public function testSetUp()
    {
        parent::setUpTestFile();
        $this->assertInstanceOf('PHP_CodeSniffer\Files\File', self::$phpcsFile);
        $this->assertSame(57, self::$phpcsFile->numTokens);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertIsArray($tokens);
    }

    /**
     * Test that the class is correct reset.
     *
     * @depends testSetUp
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
