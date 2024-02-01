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
use PHPCSUtils\Internal\StableCollections;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for specific PHPCS tokenizer issues which can affect the "is short list vs short array"
 * determination.
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
        $stackPtr = $this->getTargetToken($testMarker, StableCollections::$shortArrayListOpenTokensBC);
        $solver   = new IsShortArrayOrList(self::$phpcsFile, $stackPtr);
        $type     = $solver->solve();

        $this->assertSame($expected, $type);
    }

    /**
     * Data provider.
     *
     * @see testIsShortArrayBracket() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataIsShortArrayBracket()
    {
        return [
            'issue-1971-list-first-in-file' => [
                'testMarker' => '/* testTokenizerIssue1971PHPCSlt330gt271A */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'issue-1971-list-first-in-file-nested' => [
                'testMarker' => '/* testTokenizerIssue1971PHPCSlt330gt271B */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'issue-1381-array-dereferencing-1-array' => [
                'testMarker' => '/* testTokenizerIssue1381PHPCSlt290A1 */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'issue-1381-array-dereferencing-1-deref' => [
                'testMarker' => '/* testTokenizerIssue1381PHPCSlt290A2 */',
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-1381-array-dereferencing-2' => [
                'testMarker' => '/* testTokenizerIssue1381PHPCSlt290B */',
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-1381-array-dereferencing-3' => [
                'testMarker' => '/* testTokenizerIssue1381PHPCSlt290C */',
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-1381-array-dereferencing-4' => [
                'testMarker' => '/* testTokenizerIssue1381PHPCSlt290D1 */',
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-1381-array-dereferencing-4-deref-deref' => [
                'testMarker' => '/* testTokenizerIssue1381PHPCSlt290D2 */',
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-1284-short-list-directly-after-close-curly-control-structure' => [
                'testMarker' => '/* testTokenizerIssue1284PHPCSlt280A */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'issue-1284-short-array-directly-after-close-curly-control-structure' => [
                'testMarker' => '/* testTokenizerIssue1284PHPCSlt280B */',
                'expected'   => IsShortArrayOrList::SHORT_ARRAY,
            ],
            'issue-1284-array-access-variable-variable' => [
                'testMarker' => '/* testTokenizerIssue1284PHPCSlt290C */',
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-1284-array-access-variable-property' => [
                'testMarker' => '/* testTokenizerIssue1284PHPCSlt280D */',
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-3013-magic-constant-dereferencing' => [
                'testMarker' => '/* testTokenizerIssue3013PHPCSlt356 */',
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-more-magic-constant-dereferencing-1' => [
                'testMarker' => '/* testTokenizerIssuePHPCS28xA */',
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-nested-magic-constant-dereferencing-2' => [
                'testMarker' => '/* testTokenizerIssuePHPCS28xB */',
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-nested-magic-constant-dereferencing-3' => [
                'testMarker' => '/* testTokenizerIssuePHPCS28xC */',
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-nested-magic-constant-dereferencing-4' => [
                'testMarker' => '/* testTokenizerIssuePHPCS28xD */',
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-interpolated-string-dereferencing' => [
                'testMarker' => '/* testTokenizerIssue3172PHPCSlt360A */',
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-interpolated-string-dereferencing-nested' => [
                'testMarker' => '/* testTokenizerIssue3172PHPCSlt360B */',
                'expected'   => IsShortArrayOrList::SQUARE_BRACKETS,
            ],
            'issue-3632-short-list-in-non-braced-control-structure' => [
                'testMarker' => '/* testTokenizerIssue3632PHPCSlt372 */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
        ];
    }
}
