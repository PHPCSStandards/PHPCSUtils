<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Orthography;

use PHPCSUtils\Utils\Orthography;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHPCSUtils\Utils\Orthography::isLastCharPunctuation() method.
 *
 * @covers \PHPCSUtils\Utils\Orthography::isLastCharPunctuation
 *
 * @group orthography
 *
 * @since 1.0.0
 */
class IsLastCharPunctuationTest extends TestCase
{

    /**
     * Test correctly detecting sentence end punctuation.
     *
     * @dataProvider dataIsLastCharPunctuation
     *
     * @param string $input        The input string.
     * @param bool   $expected     The expected function output.
     * @param string $allowedChars Optional. Custom punctuation character set.
     *
     * @return void
     */
    public function testIsLastCharPunctuation($input, $expected, $allowedChars = null)
    {
        if (isset($allowedChars) === true) {
            $result = Orthography::isLastCharPunctuation($input, $allowedChars);
        } else {
            $result = Orthography::isLastCharPunctuation($input);
        }

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testIsLastCharPunctuation() For the array format.
     *
     * @return array
     */
    public function dataIsLastCharPunctuation()
    {
        return [
            // Quotes should be stripped before passing the string.
            'double-quoted' => [
                '"This is a test."',
                false,
            ],
            'single-quoted' => [
                "'This is a test?'",
                false,
            ],

            // Invalid end char.
            'no-punctuation' => [
                'This is a test',
                false,
            ],
            'invalid-punctuation' => [
                'This is a test;',
                false,
            ],
            'invalid-punctuationtrailing-whitespace' => [
                'This is a test;       ',
                false,
            ],

            // Valid end char, default charset.
            'valid' => [
                'This is a test.',
                true,
            ],
            'valid-trailing-whitespace' => [
                'This is a test.
',
                true,
            ],

            // Invalid end char, custom charset.
            'invalid-custom' => [
                'This is a test.',
                false,
                '!?,;#',
            ],

            // Valid end char, custom charset.
            'valid-custom-1' => [
                'This is a test;',
                true,
                '!?,;#',
            ],
            'valid-custom-2' => [
                'This is a test!',
                true,
                '!?,;#',
            ],
            'valid-custom-3' => [
                'Is this is a test?',
                true,
                '!?,;#',
            ],
            'valid-custom-4' => [
                'This is a test,',
                true,
                '!?,;#',
            ],
            'valid-custom-5' => [
                'This is a test#',
                true,
                '!?,;#',
            ],
        ];
    }
}
