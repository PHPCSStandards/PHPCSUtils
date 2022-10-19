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

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\Arrays;
use PHPCSUtils\Utils\Lists;

/**
 * Tests for specific PHPCS tokenizer issues which can affect the \PHPCSUtils\Utils\Arrays::isShortArray()
 * and the \PHPCSUtils\Utils\Lists::isShortList() methods.
 *
 * @group arrays
 * @group lists
 *
 * @since 1.0.0
 */
final class IsShortArrayOrListTokenizerBC1Test extends UtilityMethodTestCase
{

    /**
     * Data integrity test. Verify that the data provider is consistent.
     *
     * Something is either a short array or a short list or neither. It can never be both at the same time.
     *
     * @dataProvider  dataIsShortArrayOrList
     * @coversNothing
     *
     * @param string $ignore Unused.
     * @param bool[] $data   The expected boolean return value for list and array.
     *
     * @return void
     */
    public function testValidDataProvider($ignore, $data)
    {
        $forbidden = [
            'list'  => true,
            'array' => true,
        ];

        $this->assertNotSame($forbidden, $data);
    }

    /**
     * Test correctly determining whether a short array open token is a short array,
     * even when the token is incorrectly tokenized.
     *
     * @dataProvider dataIsShortArrayOrList
     * @covers       \PHPCSUtils\Internal\IsShortArrayOrList
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool[] $expected   The expected boolean return value for list and array.
     *
     * @return void
     */
    public function testIsShortArray($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, Collections::shortArrayListOpenTokensBC());
        $result   = Arrays::isShortArray(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected['array'], $result);
    }

    /**
     * Test correctly determining whether a short array open token is a short list,
     * even when the token is incorrectly tokenized.
     *
     * @dataProvider dataIsShortArrayOrList
     * @covers       \PHPCSUtils\Internal\IsShortArrayOrList
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool[] $expected   The expected boolean return value for list and array.
     *
     * @return void
     */
    public function testIsShortList($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, Collections::shortArrayListOpenTokensBC());
        $result   = Lists::isShortList(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected['list'], $result);
    }

    /**
     * Data provider.
     *
     * @see testIsShortArray() For the array format.
     * @see testIsShortList()  For the array format.
     *
     * @return array
     */
    public function dataIsShortArrayOrList()
    {
        return [
            'issue-1971-list-first-in-file' => [
                '/* testTokenizerIssue1971PHPCSlt330gt271A */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'issue-1971-list-first-in-file-nested' => [
                '/* testTokenizerIssue1971PHPCSlt330gt271B */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'issue-1381-array-dereferencing-1-array' => [
                '/* testTokenizerIssue1381PHPCSlt290A1 */',
                [
                    'array' => true,
                    'list'  => false,
                ],
            ],
            'issue-1381-array-dereferencing-1-deref' => [
                '/* testTokenizerIssue1381PHPCSlt290A2 */',
                [
                    'array' => false,
                    'list'  => false,
                ],
            ],
            'issue-1381-array-dereferencing-2' => [
                '/* testTokenizerIssue1381PHPCSlt290B */',
                [
                    'array' => false,
                    'list'  => false,
                ],
            ],
            'issue-1381-array-dereferencing-3' => [
                '/* testTokenizerIssue1381PHPCSlt290C */',
                [
                    'array' => false,
                    'list'  => false,
                ],
            ],
            'issue-1381-array-dereferencing-4' => [
                '/* testTokenizerIssue1381PHPCSlt290D1 */',
                [
                    'array' => false,
                    'list'  => false,
                ],
            ],
            'issue-1381-array-dereferencing-4-deref-deref' => [
                '/* testTokenizerIssue1381PHPCSlt290D2 */',
                [
                    'array' => false,
                    'list'  => false,
                ],
            ],
            'issue-1284-short-list-directly-after-close-curly-control-structure' => [
                '/* testTokenizerIssue1284PHPCSlt280A */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'issue-1284-short-array-directly-after-close-curly-control-structure' => [
                '/* testTokenizerIssue1284PHPCSlt280B */',
                [
                    'array' => true,
                    'list'  => false,
                ],
            ],
            'issue-1284-array-access-variable-variable' => [
                '/* testTokenizerIssue1284PHPCSlt290C */',
                [
                    'array' => false,
                    'list'  => false,
                ],
            ],
            'issue-1284-array-access-variable-property' => [
                '/* testTokenizerIssue1284PHPCSlt280D */',
                [
                    'array' => false,
                    'list'  => false,
                ],
            ],
            'issue-3013-magic-constant-dereferencing' => [
                '/* testTokenizerIssue3013PHPCSlt356 */',
                [
                    'array' => false,
                    'list'  => false,
                ],
            ],
            'issue-more-magic-constant-dereferencing-1' => [
                '/* testTokenizerIssuePHPCS28xA */',
                [
                    'array' => false,
                    'list'  => false,
                ],
            ],
            'issue-nested-magic-constant-dereferencing-2' => [
                '/* testTokenizerIssuePHPCS28xB */',
                [
                    'array' => false,
                    'list'  => false,
                ],
            ],
            'issue-nested-magic-constant-dereferencing-3' => [
                '/* testTokenizerIssuePHPCS28xC */',
                [
                    'array' => false,
                    'list'  => false,
                ],
            ],
            'issue-nested-magic-constant-dereferencing-4' => [
                '/* testTokenizerIssuePHPCS28xD */',
                [
                    'array' => false,
                    'list'  => false,
                ],
            ],
            'issue-interpolated-string-dereferencing' => [
                '/* testTokenizerIssue3172PHPCSlt360A */',
                [
                    'array' => false,
                    'list'  => false,
                ],
            ],
            'issue-interpolated-string-dereferencing-nested' => [
                '/* testTokenizerIssue3172PHPCSlt360B */',
                [
                    'array' => false,
                    'list'  => false,
                ],
            ],
        ];
    }
}
