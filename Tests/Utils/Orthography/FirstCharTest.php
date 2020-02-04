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
 * @group orthography
 *
 * @since 1.0.0
 */
class FirstCharTest extends TestCase
{

    /**
     * Test correctly detecting whether the first character of a phrase is capitalized.
     *
     * @dataProvider dataFirstChar
     *
     * @param string $input    The input string.
     * @param array  $expected The expected function output for the respective functions.
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
     * @param string $input    The input string.
     * @param array  $expected The expected function output for the respective functions.
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
     * @return array
     */
    public function dataFirstChar()
    {
        $data = [
            // Quotes should be stripped before passing the string.
            'double-quoted' => [
                '"This is a test"',
                [
                    'capitalized' => false,
                    'lowercase'   => false,
                ],
            ],
            'single-quoted' => [
                "'This is a test'",
                [
                    'capitalized' => false,
                    'lowercase'   => false,
                ],
            ],

            // Not starting with a letter.
            'start-numeric' => [
                '12 Foostreet',
                [
                    'capitalized' => false,
                    'lowercase'   => false,
                ],
            ],
            'start-bracket' => [
                '[Optional]',
                [
                    'capitalized' => false,
                    'lowercase'   => false,
                ],
            ],

            // Leading whitespace.
            'english-lowercase-leading-whitespace' => [
                '
                this is a test',
                [
                    'capitalized' => false,
                    'lowercase'   => true,
                ],
            ],
            'english-propercase-leading-whitespace' => [
                '
                This is a test',
                [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],

            // First character lowercase.
            'english-lowercase' => [
                'this is a test',
                [
                    'capitalized' => false,
                    'lowercase'   => true,
                ],
            ],
            'russian-lowercase' => [
                'предназначена для‎',
                [
                    'capitalized' => false,
                    'lowercase'   => true,
                ],
            ],
            'latvian-lowercase' => [
                'ir domāta',
                [
                    'capitalized' => false,
                    'lowercase'   => true,
                ],
            ],
            'armenian-lowercase' => [
                'սա թեստ է',
                [
                    'capitalized' => false,
                    'lowercase'   => true,
                ],
            ],
            'mandinka-lowercase' => [
                'ŋanniya',
                [
                    'capitalized' => false,
                    'lowercase'   => true,
                ],
            ],
            'greek-lowercase' => [
                'δημιουργήθηκε από',
                [
                    'capitalized' => false,
                    'lowercase'   => true,
                ],
            ],

            // First character capitalized.
            'english-propercase' => [
                'This is a test',
                [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'russian-propercase' => [
                'Дата написания этой книги',
                [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'latvian-propercase' => [
                'Šodienas datums',
                [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'armenian-propercase' => [
                'Սա թեստ է',
                [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'igbo-propercase' => [
                'Ụbọchị tata bụ',
                [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'greek-propercase' => [
                'Η σημερινή ημερομηνία',
                [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],

            // No concept of "case", but starting with a letter.
            'arabic' => [
                'هذا اختبار',
                [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'pashto' => [
                'دا یوه آزموینه ده',
                [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'hebrew' => [
                'זה מבחן',
                [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'chinese-traditional' => [
                '這是一個測試',
                [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ],
            'urdu' => [
                'کا منشاء برائے',
                [
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
                'ეს ტესტია',
                [
                    'capitalized' => false,
                    'lowercase'   => true,
                ],
            ];
        } else {
            $data['georgian'] = [
                'ეს ტესტია',
                [
                    'capitalized' => true,
                    'lowercase'   => false,
                ],
            ];
        }

        return $data;
    }
}
