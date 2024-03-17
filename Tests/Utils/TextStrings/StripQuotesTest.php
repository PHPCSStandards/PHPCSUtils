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
 * @since 1.0.0
 */
final class StripQuotesTest extends TestCase
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
     * @return array<string, array<string, string>>
     */
    public static function dataStripQuotes()
    {
        return [
            'simple-string-double-quotes' => [
                'input'      => '"dir_name"',
                'expected'   => 'dir_name',
            ],
            'simple-string-single-quotes' => [
                'input'      => "'soap.wsdl_cache'",
                'expected'   => 'soap.wsdl_cache',
            ],
            'string-with-escaped-quotes-within-1' => [
                'input'      => '"arbitrary-\'string\" with\' quotes within"',
                'expected'   => 'arbitrary-\'string\" with\' quotes within',
            ],
            'string-with-escaped-quotes-within-2' => [
                'input'      => '"\'quoted_name\'"',
                'expected'   => '\'quoted_name\'',
            ],
            'string-with-different-quotes-at-start-of-string' => [
                'input'      => "'\"quoted\" start of string'",
                'expected'   => '"quoted" start of string',
            ],
            'incomplete-quote-set-only-start-quote' => [
                'input'      => "'no stripping when there is only a start quote",
                'expected'   => "'no stripping when there is only a start quote",
            ],
            'incomplete-quote-set-only-end-quote' => [
                'input'      => 'no stripping when there is only an end quote"',
                'expected'   => 'no stripping when there is only an end quote"',
            ],
            'start-end-quote-mismatch' => [
                'input'      => "'no stripping when quotes at start/end are mismatched\"",
                'expected'   => "'no stripping when quotes at start/end are mismatched\"",
            ],
            'multi-line-string-single-quotes' => [
                'input' => "'some
    text
        and
more'",
                'expected' => 'some
    text
        and
more',
            ],
        ];
    }
}
