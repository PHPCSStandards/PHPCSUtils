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
 * @covers \PHPCSUtils\TestUtils\UtilityMethodTestCase::setUpTestFile
 * @covers \PHPCSUtils\TestUtils\UtilityMethodTestCase::parseFile
 * @covers \PHPCSUtils\TestUtils\UtilityMethodTestCase::skipJSCSSTestsOnPHPCS4
 *
 * @since 1.0.0
 */
final class SetUpTestFileTest extends PolyfilledTestCase
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
        // Deliberately not running the actual setUpTestFile() method.
    }

    /**
     * Test that the setUpTestFile() method works correctly.
     *
     * @return void
     */
    public function testSetUp()
    {
        // Verify that the class was virgin to begin with.
        $this->assertSame('0', self::$phpcsVersion, 'phpcsVersion was not correct to begin with');
        $this->assertSame('inc', self::$fileExtension, 'fileExtension was not correct to begin with');
        $this->assertSame('', self::$caseFile, 'caseFile was not correct to begin with');
        $this->assertSame(4, self::$tabWidth, 'tabWidth was not correct to begin with');
        $this->assertNull(self::$phpcsFile, 'phpcsFile was not correct to begin with');
        $this->assertSame(['Dummy.Dummy.Dummy'], self::$selectedSniff, 'selectedSniff was not correct to begin with');

        // Run the set up.
        parent::setUpTestFile();

        // Verify select properties have been set correctly.
        $this->assertNotSame('0', self::$phpcsVersion, 'phpcsVersion was not set');

        $this->assertInstanceOf('PHP_CodeSniffer\Files\File', self::$phpcsFile);
        $this->assertSame(57, self::$phpcsFile->numTokens);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertIsArray($tokens);
    }
}
