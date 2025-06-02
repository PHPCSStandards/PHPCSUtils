<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\FileInfo;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\FileInfo;

/**
 * Tests for the \PHPCSUtils\Utils\FileInfo::hasSheBang() and \PHPCSUtils\Utils\FileInfo::hasByteOrderMark() methods.
 *
 * @covers \PHPCSUtils\Utils\FileInfo::hasByteOrderMark
 * @covers \PHPCSUtils\Utils\FileInfo::hasSheBang
 *
 * @since 1.1.0
 */
final class HasSheBangPHPAndUTF8BomTest extends UtilityMethodTestCase
{

    /**
     * Test whether a byte order mark at the start of the file is correctly recognized.
     *
     * @return void
     */
    public function testHasSheBang()
    {
        $this->assertNotFalse(FileInfo::hasByteOrderMark(self::$phpcsFile), 'File does not have a byte order mark');
        $this->assertTrue(FileInfo::hasSheBang(self::$phpcsFile), 'No shebang found at the start of the file');
    }
}
