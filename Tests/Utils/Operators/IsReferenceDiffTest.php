<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Operators;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Operators;

/**
 * Tests for the \PHPCSUtils\Utils\Operators::isReference() method.
 *
 * The tests in this class cover the differences between the PHPCS native method and the PHPCSUtils
 * version. These tests would fail when using the BCFile `isReference()` method.
 *
 * @covers \PHPCSUtils\Utils\Operators::isReference
 *
 * @group operators
 *
 * @since 1.0.0
 */
class IsReferenceDiffTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(Operators::isReference(self::$phpcsFile, 10000));
    }

    /**
     * Test correctly identifying that whether a "bitwise and" token is a reference or not.
     *
     * @dataProvider dataIsReference
     *
     * @param string $identifier Comment which precedes the test case.
     * @param bool   $expected   Expected function output.
     *
     * @return void
     */
    public function testIsReference($identifier, $expected)
    {
        $bitwiseAnd = $this->getTargetToken($identifier, \T_BITWISE_AND);
        $result     = Operators::isReference(self::$phpcsFile, $bitwiseAnd);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testIsReference()
     *
     * @return array
     */
    public function dataIsReference()
    {
        return [
            'issue-1971-list-first-in-file' => [
                '/* testTokenizerIssue1971PHPCSlt330gt271A */',
                true,
            ],
            'issue-1971-list-first-in-file-nested' => [
                '/* testTokenizerIssue1971PHPCSlt330gt271B */',
                true,
            ],
            'issue-1284-short-list-directly-after-close-curly-control-structure' => [
                '/* testTokenizerIssue1284PHPCSlt280A */',
                true,
            ],
            'issue-1284-short-list-directly-after-close-curly-control-structure-second-item' => [
                '/* testTokenizerIssue1284PHPCSlt280B */',
                true,
            ],
            'issue-1284-short-array-directly-after-close-curly-control-structure' => [
                '/* testTokenizerIssue1284PHPCSlt280C */',
                true,
            ],
        ];
    }
}
