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
 * Tests for the \PHPCSUtils\Utils\Orthography::isFirstCharCapitalized()
 * and the \PHPCSUtils\Utils\Orthography::isFirstCharLowercase() methods.
 *
 * @covers \PHPCSUtils\Utils\Orthography::isFirstCharCapitalized
 * @covers \PHPCSUtils\Utils\Orthography::isFirstCharLowercase
 *
 * @since 1.0.0
 */
final class FirstCharTest extends TestCase
{

    /**
     * Test correctly detecting whether the first character of a phrase is capitalized.
     *
     * @dataProvider dataFirstChar
     *
     * @param string              $input    The input string.
     * @param array<string, bool> $expected The expected function output for the respective functions.
     *
     * @return void
     */
    public function testIsFirstCharCapitalized($input, $expected)
    {
        $this->assertSame($expected['capitalized'], Orthography::isFirstCharCapitalized($input));
    }

    /**
     * Test correctly detecting whether the first character of a phrase is lowercase.
     *
     * @dataProvider dataFirstChar
     *
     * @param string              $input    The input string.
     * @param array<string, bool> $expected The expected function output for the respective functions.
     *
     * @return void
     */
    public function testIsFirstCharLowercase($input, $expected)
    {
        $this->assertSame($expected['lowercase'], Orthography::isFirstCharLowercase($input));
    }

    /**
     * Data provider.
     *
     * @see testIsFirstCharCapitalized() For the array format.
     * @see testIsFirstCharLowercase()   For the array format.
     *
     * @return array<string, array<string, string|array<string, bool>>>
     */
    public static function dataFirstChar()
    {
        $data = [
            // Quotes should be stripped before passing the string.
            'double-quoted' => [
                'input'    => '"This is a test"',
                'expected' => [
                    'capitalized' => false,
                    'lowercase'   => false,
                ],
            ],
            'single-quoted' => [
                'input'    => "'This is a test'",
                'expected' => [
                    'capitalized' => false,
                    'lowercase'   => false,
                ],
            ],

            // Not starting with a letter.
            'start-numeric' => [
                'input'    => '12 Foostreet',
                'expected' => [
                    'capitalized' => false,
                    'lowercase'   => false,
                ],
            ],
            'start-bracket' => [
                'input'    => '[Optional]',
                'expected' => [
                    'capitalized' => false,
                    'lowercase'   => false,
                ],
            ],

            // Leading whitespace.
            'english-lowercase-leading-whitespace' => [
                'input'    => '
                this is a test',
                'expected' => [
                    'capitalized' => false,
                    'lowercase'   => true,
                ],
            ],
            'english-propercase-leading-whitespace' => [
                'input'    => '
                This is a test',
                'expected' => [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],

            // First character lowercase.
            'english-lowercase' => [
                'input'    => 'this is a test',
                'expected' => [
                    'capitalized' => false,
                    'lowercase'   => true,
                ],
            ],
            'russian-lowercase' => [
                'input'    => 'предназначена для‎',
                'expected' => [
                    'capitalized' => false,
                    'lowercase'   => true,
                ],
            ],
            'latvian-lowercase' => [
                'input'    => 'ir domāta',
                'expected' => [
                    'capitalized' => false,
                    'lowercase'   => true,
                ],
            ],
            'armenian-lowercase' => [
                'input'    => 'սա թեստ է',
                'expected' => [
                    'capitalized' => false,
                    'lowercase'   => true,
                ],
            ],
            'mandinka-lowercase' => [
                'input'    => 'ŋanniya',
                'expected' => [
                    'capitalized' => false,
                    'lowercase'   => true,
                ],
            ],
            'greek-lowercase' => [
                'input'    => 'δημιουργήθηκε από',
                'expected' => [
                    'capitalized' => false,
                    'lowercase'   => true,
                ],
            ],

            // First character capitalized.
            'english-propercase' => [
                'input'    => 'This is a test',
                'expected' => [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'russian-propercase' => [
                'input'    => 'Дата написания этой книги',
                'expected' => [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'latvian-propercase' => [
                'input'    => 'Šodienas datums',
                'expected' => [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'armenian-propercase' => [
                'input'    => 'Սա թեստ է',
                'expected' => [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'igbo-propercase' => [
                'input'    => 'Ụbọchị tata bụ',
                'expected' => [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'greek-propercase' => [
                'input'    => 'Η σημερινή ημερομηνία',
                'expected' => [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],

            // No concept of "case", but starting with a letter.
            'arabic' => [
                'input'    => 'هذا اختبار',
                'expected' => [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'pashto' => [
                'input'    => 'دا یوه آزموینه ده',
                'expected' => [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'hebrew' => [
                'input'    => 'זה מבחן',
                'expected' => [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'chinese-traditional' => [
                'input'    => '這是一個測試',
                'expected' => [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'urdu' => [
                'input'    => 'کا منشاء برائے',
                'expected' => [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
        ];

        /*
         * PCRE2 - included in PHP 7.3+ - recognizes Georgian as a language with
         * upper and lowercase letters as defined in Unicode v 11.0 / June 2018.
         * While, as far as I can tell, this is linguistically incorrect - the upper
         * and lowercase letters are from different alphabets used to write Georgian -,
         * the unit test should allow for the reality as implemented in ICU/PCRE2/PHP.
         *
         * @link https://en.wikipedia.org/wiki/Georgian_scripts#Unicode
         * @link https://unicode.org/charts/PDF/U10A0.pdf
         */

        if (\PCRE_VERSION >= 10) {
            $data['georgian'] = [
                'input'    => 'ეს ტესტია',
                'expected' => [
                    'capitalized' => false,
                    'lowercase'   => true,
                ],
            ];
        } else {
            $data['georgian'] = [
                'input'    => 'ეს ტესტია',
                'expected' => [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ];
        }

        return $data;
    }
}
