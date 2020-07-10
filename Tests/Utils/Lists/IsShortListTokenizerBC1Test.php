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
class IsShortListTokenizerBC1Test extends UtilityMethodTestCase
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
            'issue-1971-list-first-in-file' => [
                '/* testTokenizerIssue1971PHPCSlt330gt271A */',
                true,
            ],
            'issue-1971-list-first-in-file-nested' => [
                '/* testTokenizerIssue1971PHPCSlt330gt271B */',
                true,
            ],
            'issue-1381-array-dereferencing-1' => [
                '/* testTokenizerIssue1381PHPCSlt290A1 */',
                false,
            ],
            'issue-1381-array-dereferencing-1-deref' => [
                '/* testTokenizerIssue1381PHPCSlt290A2 */',
                false,
            ],
            'issue-1381-array-dereferencing-2' => [
                '/* testTokenizerIssue1381PHPCSlt290B */',
                false,
            ],
            'issue-1381-array-dereferencing-3' => [
                '/* testTokenizerIssue1381PHPCSlt290C */',
                false,
            ],
            'issue-1381-array-dereferencing-4' => [
                '/* testTokenizerIssue1381PHPCSlt290D1 */',
                false,
            ],
            'issue-1381-array-dereferencing-4-deref-deref' => [
                '/* testTokenizerIssue1381PHPCSlt290D2 */',
                false,
            ],
            'issue-1284-short-list-directly-after-close-curly-control-structure' => [
                '/* testTokenizerIssue1284PHPCSlt280A */',
                true,
            ],
            'issue-1284-short-array-directly-after-close-curly-control-structure' => [
                '/* testTokenizerIssue1284PHPCSlt280B */',
                false,
            ],
            'issue-1284-array-access-variable-variable' => [
                '/* testTokenizerIssue1284PHPCSlt290C */',
                false,
            ],
            'issue-1284-array-access-variable-property' => [
                '/* testTokenizerIssue1284PHPCSlt280D */',
                false,
            ],
            'issue-3013-magic-constant-dereferencing' => [
                '/* testTokenizerIssue3013PHPCSlt3xx */',
                false,
            ],
        ];
    }
}
