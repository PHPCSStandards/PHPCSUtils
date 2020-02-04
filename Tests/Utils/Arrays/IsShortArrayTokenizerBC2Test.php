<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Arrays;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Arrays;

/**
 * Tests for specific PHPCS tokenizer issues which can affect the \PHPCSUtils\Utils\Arrays::isShortArray() method.
 *
 * @covers \PHPCSUtils\Utils\Arrays::isShortArray
 *
 * @group arrays
 *
 * @since 1.0.0
 */
class IsShortArrayTokenizerBC2Test extends UtilityMethodTestCase
{

    /**
     * Full path to the test case file associated with this test class.
     *
     * @var string
     */
    protected static $caseFile = '';

    /**
     * Initialize PHPCS & tokenize the test case file.
     *
     * Overloaded to re-use the `$caseFile` from the Lists::isShortList() test.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = \dirname(__DIR__) . '/Lists/IsShortListTokenizerBC2Test.inc';
        parent::setUpTestFile();
    }

    /**
     * Test correctly determining whether a short array open token is a short array,
     * even when the token is incorrectly tokenized.
     *
     * @dataProvider dataIsShortArray
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   The expected boolean return value.
     *
     * @return void
     */
    public function testIsShortArray($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [\T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET]);
        $result   = Arrays::isShortArray(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testIsShortArray() For the array format.
     *
     * @return array
     */
    public function dataIsShortArray()
    {
        return [
            // Make sure the utility method does not throw false positives for short array at start of file.
            'issue-1971-short-array-first-in-file' => [
                '/* testTokenizerIssue1971PHPCSlt330gt271C */',
                true,
            ],
            'issue-1971-short-array-first-in-file-nested' => [
                '/* testTokenizerIssue1971PHPCSlt330gt271D */',
                true,
            ],
        ];
    }
}
