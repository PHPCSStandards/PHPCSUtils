<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Lists;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Lists;

/**
 * Tests for specific PHPCS tokenizer issues which can affect the \PHPCSUtils\Utils\Lists::isShortList() method.
 *
 * @covers \PHPCSUtils\Utils\Lists::isShortList
 *
 * @group lists
 *
 * @since 1.0.0
 */
class IsShortListTokenizerBC2Test extends UtilityMethodTestCase
{

    /**
     * Test correctly determining whether a short array open token is a short array or a short list,
     * even when the token is incorrectly tokenized.
     *
     * @dataProvider dataIsShortList
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   The expected boolean return value.
     *
     * @return void
     */
    public function testIsShortList($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [\T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]);
        $result   = Lists::isShortList(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testIsShortList() For the array format.
     *
     * @return array
     */
    public function dataIsShortList()
    {
        return [
            // Make sure the utility method does not throw false positives for a short array at the start of a file.
            'issue-1971-short-array-first-in-file' => [
                '/* testTokenizerIssue1971PHPCSlt330gt271C */',
                false,
            ],
            'issue-1971-short-array-first-in-file-nested' => [
                '/* testTokenizerIssue1971PHPCSlt330gt271D */',
                false,
            ],
        ];
    }
}
