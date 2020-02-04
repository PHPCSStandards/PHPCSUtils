<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\TextStrings;

use PHPCSUtils\Utils\TextStrings;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHPCSUtils\Utils\TextStrings::stripQuotes() method.
 *
 * @covers \PHPCSUtils\Utils\TextStrings::stripQuotes
 *
 * @group textstrings
 *
 * @since 1.0.0
 */
class StripQuotesTest extends TestCase
{

    /**
     * Test correctly stripping quotes surrounding text strings.
     *
     * @dataProvider dataStripQuotes
     *
     * @param string $input    The input string.
     * @param string $expected The expected function output.
     *
     * @return void
     */
    public function testStripQuotes($input, $expected)
    {
        $this->assertSame($expected, TextStrings::stripQuotes($input));
    }

    /**
     * Data provider.
     *
     * @see testStripQuotes() For the array format.
     *
     * @return array
     */
    public function dataStripQuotes()
    {
        return [
            'simple-string-double-quotes' => [
                '"dir_name"',
                'dir_name',
            ],
            'simple-string-single-quotes' => [
                "'soap.wsdl_cache'",
                'soap.wsdl_cache',
            ],
            'string-with-escaped-quotes-within-1' => [
                '"arbitrary-\'string\" with\' quotes within"',
                'arbitrary-\'string\" with\' quotes within',
            ],
            'string-with-escaped-quotes-within-2' => [
                '"\'quoted_name\'"',
                '\'quoted_name\'',
            ],
            'string-with-different-quotes-at-start-of-string' => [
                "'\"quoted\" start of string'",
                '"quoted" start of string',
            ],
            'incomplete-quote-set-only-start-quote' => [
                "'no stripping when there is only a start quote",
                "'no stripping when there is only a start quote",
            ],
            'incomplete-quote-set-only-end-quote' => [
                'no stripping when there is only an end quote"',
                'no stripping when there is only an end quote"',
            ],
            'start-end-quote-mismatch' => [
                "'no stripping when quotes at start/end are mismatched\"",
                "'no stripping when quotes at start/end are mismatched\"",
            ],
            'multi-line-string-single-quotes' => [
                "'some
    text
        and
more'",
                'some
    text
        and
more',
            ],
        ];
    }
}
