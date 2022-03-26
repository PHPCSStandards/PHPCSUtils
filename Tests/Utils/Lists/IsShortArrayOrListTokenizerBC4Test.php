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
class IsShortArrayOrListTokenizerBC4Test extends UtilityMethodTestCase
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
     * @covers       \PHPCSUtils\Utils\Arrays::isShortArray
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool[] $expected   The expected boolean return value for list and array.
     *
     * @return void
     */
    public function testIsShortArray($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [\T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]);
        $result   = Arrays::isShortArray(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected['array'], $result);
    }

    /**
     * Test correctly determining whether a short array open token is a short array or a short list,
     * even when the token is incorrectly tokenized.
     *
     * @dataProvider dataIsShortArrayOrList
     * @covers       \PHPCSUtils\Utils\Lists::isShortList
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool[] $expected   The expected boolean return value for list and array.
     *
     * @return void
     */
    public function testIsShortList($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [\T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]);
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
            'issue-1971-short-list-first-in-file' => [
                '/* testTokenizerIssue1971PHPCSlt330gt271G */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
            'issue-1971-short-list-first-in-file-nested' => [
                '/* testTokenizerIssue1971PHPCSlt330gt271H */',
                [
                    'array' => false,
                    'list'  => true,
                ],
            ],
        ];
    }
}
