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
final class IsShortArrayBracketBC5Test extends UtilityMethodTestCase
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
            'issue-1971-short-list-first-in-file' => [
                'testMarker' => '/* testTokenizerIssue1971PHPCSlt330gt271I */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
            'issue-1971-short-list-first-in-file-nested' => [
                'testMarker' => '/* testTokenizerIssue1971PHPCSlt330gt271J */',
                'expected'   => IsShortArrayOrList::SHORT_LIST,
            ],
        ];
    }
}
