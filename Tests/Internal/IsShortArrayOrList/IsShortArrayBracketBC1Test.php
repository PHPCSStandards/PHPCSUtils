<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Internal\IsShortArrayOrList;

use PHPCSUtils\Internal\IsShortArrayOrList;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Tokens\Collections;

/**
 * Tests for specific PHPCS tokenizer issues which can affect the "is short list vs short array"
 * determination.
 *
 * @group arrays
 * @group lists
 *
 * @covers \PHPCSUtils\Internal\IsShortArrayOrList::isShortArrayBracket
 *
 * @since 1.0.0
 */
final class IsShortArrayBracketBC1Test extends UtilityMethodTestCase
{

    /**
     * Test correctly determining whether a short array/square bracket open token is a short array,
     * a short list or real square brackets, even when the token is incorrectly tokenized.
     *
     * @dataProvider dataIsShortArrayBracket
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param string $expected   The expected return value.
     *
     * @return void
     */
    public function testIsShortArrayBracket($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, Collections::shortArrayListOpenTokensBC());
        $solver   = new IsShortArrayOrList(self::$phpcsFile, $stackPtr);
        $type     = $solver->solve();

        $this->assertSame($expected, $type);
    }

    /**
     * Data provider.
     *
     * @see testIsShortArrayBracket() For the array format.
     *
     * @return array
     */
    public function dataIsShortArrayBracket()
    {
        return [
            'issue-1971-list-first-in-file' => [
                '/* testTokenizerIssue1971PHPCSlt330gt271A */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'issue-1971-list-first-in-file-nested' => [
                '/* testTokenizerIssue1971PHPCSlt330gt271B */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'issue-1381-array-dereferencing-1-array' => [
                '/* testTokenizerIssue1381PHPCSlt290A1 */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'issue-1381-array-dereferencing-1-deref' => [
                '/* testTokenizerIssue1381PHPCSlt290A2 */',
                IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-1381-array-dereferencing-2' => [
                '/* testTokenizerIssue1381PHPCSlt290B */',
                IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-1381-array-dereferencing-3' => [
                '/* testTokenizerIssue1381PHPCSlt290C */',
                IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-1381-array-dereferencing-4' => [
                '/* testTokenizerIssue1381PHPCSlt290D1 */',
                IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-1381-array-dereferencing-4-deref-deref' => [
                '/* testTokenizerIssue1381PHPCSlt290D2 */',
                IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-1284-short-list-directly-after-close-curly-control-structure' => [
                '/* testTokenizerIssue1284PHPCSlt280A */',
                IsShortArrayOrList::SHORT_LIST,
            ],
            'issue-1284-short-array-directly-after-close-curly-control-structure' => [
                '/* testTokenizerIssue1284PHPCSlt280B */',
                IsShortArrayOrList::SHORT_ARRAY,
            ],
            'issue-1284-array-access-variable-variable' => [
                '/* testTokenizerIssue1284PHPCSlt290C */',
                IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-1284-array-access-variable-property' => [
                '/* testTokenizerIssue1284PHPCSlt280D */',
                IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-3013-magic-constant-dereferencing' => [
                '/* testTokenizerIssue3013PHPCSlt356 */',
                IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-more-magic-constant-dereferencing-1' => [
                '/* testTokenizerIssuePHPCS28xA */',
                IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-nested-magic-constant-dereferencing-2' => [
                '/* testTokenizerIssuePHPCS28xB */',
                IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-nested-magic-constant-dereferencing-3' => [
                '/* testTokenizerIssuePHPCS28xC */',
                IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-nested-magic-constant-dereferencing-4' => [
                '/* testTokenizerIssuePHPCS28xD */',
                IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-interpolated-string-dereferencing' => [
                '/* testTokenizerIssue3172PHPCSlt360A */',
                IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-interpolated-string-dereferencing-nested' => [
                '/* testTokenizerIssue3172PHPCSlt360B */',
                IsShortArrayOrList::SQUARE_BRACKETS,
            ],
        ];
    }
}
