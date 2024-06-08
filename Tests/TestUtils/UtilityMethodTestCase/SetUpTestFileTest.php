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
        parent::setUpTestFile();
        $this->assertInstanceOf('PHP_CodeSniffer\Files\File', self::$phpcsFile);
        $this->assertSame(57, self::$phpcsFile->numTokens);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertIsArray($tokens);
    }
}
