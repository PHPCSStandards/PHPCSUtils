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
 * @since 1.0.0
 */
final class IsLastCharPunctuationTest extends TestCase
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
     * @return array<string, array<string, string|bool>>
     */
    public static function dataIsLastCharPunctuation()
    {
        return [
            // Quotes should be stripped before passing the string.
            'double-quoted' => [
                'input'    => '"This is a test."',
                'expected' => false,
            ],
            'single-quoted' => [
                'input'    => "'This is a test?'",
                'expected' => false,
            ],

            // Invalid end char.
            'no-punctuation' => [
                'input'    => 'This is a test',
                'expected' => false,
            ],
            'invalid-punctuation' => [
                'input'    => 'This is a test;',
                'expected' => false,
            ],
            'invalid-punctuationtrailing-whitespace' => [
                'input'    => 'This is a test;       ',
                'expected' => false,
            ],

            // Valid end char, default charset.
            'valid' => [
                'input'    => 'This is a test.',
                'expected' => true,
            ],
            'valid-trailing-whitespace' => [
                'input'    => 'This is a test.
',
                'expected' => true,
            ],

            // Invalid end char, custom charset.
            'invalid-custom' => [
                'input'        => 'This is a test.',
                'expected'     => false,
                'allowedChars' => '!?,;#',
            ],

            // Valid end char, custom charset.
            'valid-custom-1' => [
                'input'        => 'This is a test;',
                'expected'     => true,
                'allowedChars' => '!?,;#',
            ],
            'valid-custom-2' => [
                'input'        => 'This is a test!',
                'expected'     => true,
                'allowedChars' => '!?,;#',
            ],
            'valid-custom-3' => [
                'input'        => 'Is this is a test?',
                'expected'     => true,
                'allowedChars' => '!?,;#',
            ],
            'valid-custom-4' => [
                'input'        => 'This is a test,',
                'expected'     => true,
                'allowedChars' => '!?,;#',
            ],
            'valid-custom-5' => [
                'input'        => 'This is a test#',
                'expected'     => true,
                'allowedChars' => '!?,;#',
            ],
        ];
    }
}
