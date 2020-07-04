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

use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\Utils\Orthography;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHPCSUtils\Utils\Orthography::isFirstCharCapitalized(),
 * the \PHPCSUtils\Utils\Orthography::isFirstCharLowercase() and the
 * \PHPCSUtils\Utils\Orthography::capitalizeFirstChar() methods.
 *
 * @covers \PHPCSUtils\Utils\Orthography::isFirstCharCapitalized
 * @covers \PHPCSUtils\Utils\Orthography::isFirstCharLowercase
 * @covers \PHPCSUtils\Utils\Orthography::capitalizeFirstChar
 *
 * @group orthography
 *
 * @since 1.0.0
 */
class FirstCharTest extends TestCase
{

    /**
     * Original encoding.
     *
     * @var string
     */
    public static $originalEncoding;

    /**
     * Set the PHPCS encoding for this test file to UTF-8.
     *
     * As the default encoding for PHPCS 2.x isn't UTF-8, the tests would fail otherwise.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setEncoding()
    {
        self::$originalEncoding = Helper::getConfigData('encoding');
        Helper::setConfigData('encoding', 'utf-8', true);
    }

    /**
     * Set the PHPCS encoding for this test file to UTF-8.
     *
     * @afterClass
     *
     * @return void
     */
    public static function resetEncoding()
    {
        Helper::setConfigData('encoding', self::$originalEncoding, true);
    }

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
        $this->assertSame($expected['is_capitalized'], Orthography::isFirstCharCapitalized($input));
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
        $this->assertSame($expected['is_lowercase'], Orthography::isFirstCharLowercase($input));
    }

    /**
     * Test correctly transforming the first character of a phrase to uppercase.
     *
     * @dataProvider dataFirstChar
     *
     * @param string $input    The input string.
     * @param array  $expected The expected function output for the respective functions.
     *
     * @return void
     */
    public function testCapitalizeFirstChar($input, $expected)
    {
        if (isset($expected['ucfirst']) === false) {
            $this->markTestSkipped('Capitalization for this test case is not stable.');
        }

        $this->assertSame($expected['ucfirst'], Orthography::capitalizeFirstChar($input));
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
                    'is_capitalized' => false,
                    'is_lowercase'   => false,
                    'ucfirst'        => '"This is a test"',
                ],
            ],
            'single-quoted' => [
                "'This is a test'",
                [
                    'is_capitalized' => false,
                    'is_lowercase'   => false,
                    'ucfirst'        => "'This is a test'",
                ],
            ],

            // Not starting with a letter.
            'start-numeric' => [
                '12 Foostreet',
                [
                    'is_capitalized' => false,
                    'is_lowercase'   => false,
                    'ucfirst'        => '12 Foostreet',
                ],
            ],
            'start-bracket' => [
                '[Optional]',
                [
                    'is_capitalized' => false,
                    'is_lowercase'   => false,
                    'ucfirst'        => '[Optional]',
                ],
            ],

            // Leading whitespace.
            'english-lowercase-leading-whitespace' => [
                '
                this is a test',
                [
                    'is_capitalized' => false,
                    'is_lowercase'   => true,
                    'ucfirst'        => '
                This is a test',
                ],
            ],
            'english-propercase-leading-whitespace' => [
                '
                This is a test',
                [
                    'is_capitalized' => true,
                    'is_lowercase'   => false,
                    'ucfirst'        => '
                This is a test',
                ],
            ],

            // First character lowercase.
            'english-lowercase' => [
                'this is a test',
                [
                    'is_capitalized' => false,
                    'is_lowercase'   => true,
                    'ucfirst'        => 'This is a test',
                ],
            ],
            'russian-lowercase' => [
                'предназначена для‎',
                [
                    'is_capitalized' => false,
                    'is_lowercase'   => true,
                    'ucfirst'        => 'Предназначена для‎',
                ],
            ],
            'latvian-lowercase' => [
                'ir domāta',
                [
                    'is_capitalized' => false,
                    'is_lowercase'   => true,
                    'ucfirst'        => 'Ir domāta',
                ],
            ],
            'armenian-lowercase' => [
                'սա թեստ է',
                [
                    'is_capitalized' => false,
                    'is_lowercase'   => true,
                    'ucfirst'        => 'Սա թեստ է',
                ],
            ],
            'mandinka-lowercase' => [
                'ŋanniya',
                [
                    'is_capitalized' => false,
                    'is_lowercase'   => true,
                    'ucfirst'        => 'Ŋanniya',
                ],
            ],
            'greek-lowercase' => [
                'δημιουργήθηκε από',
                [
                    'is_capitalized' => false,
                    'is_lowercase'   => true,
                    'ucfirst'        => 'Δημιουργήθηκε από',
                ],
            ],

            // First character capitalized.
            'english-propercase' => [
                'This is a test',
                [
                    'is_capitalized' => true,
                    'is_lowercase'   => false,
                    'ucfirst'        => 'This is a test',
                ],
            ],
            'russian-propercase' => [
                'Дата написания этой книги',
                [
                    'is_capitalized' => true,
                    'is_lowercase'   => false,
                    'ucfirst'        => 'Дата написания этой книги',
                ],
            ],
            'latvian-propercase' => [
                'Šodienas datums',
                [
                    'is_capitalized' => true,
                    'is_lowercase'   => false,
                    'ucfirst'        => 'Šodienas datums',
                ],
            ],
            'armenian-propercase' => [
                'Սա թեստ է',
                [
                    'is_capitalized' => true,
                    'is_lowercase'   => false,
                    'ucfirst'        => 'Սա թեստ է',
                ],
            ],
            'igbo-propercase' => [
                'Ụbọchị tata bụ',
                [
                    'is_capitalized' => true,
                    'is_lowercase'   => false,
                    'ucfirst'        => 'Ụbọchị tata bụ',
                ],
            ],
            'greek-propercase' => [
                'Η σημερινή ημερομηνία',
                [
                    'is_capitalized' => true,
                    'is_lowercase'   => false,
                    'ucfirst'        => 'Η σημερινή ημερομηνία',
                ],
            ],

            // No concept of "case", but starting with a letter.
            'arabic' => [
                'هذا اختبار',
                [
                    'is_capitalized' => true,
                    'is_lowercase'   => false,
                    'ucfirst'        => 'هذا اختبار',
                ],
            ],
            'pashto' => [
                'دا یوه آزموینه ده',
                [
                    'is_capitalized' => true,
                    'is_lowercase'   => false,
                    'ucfirst'        => 'دا یوه آزموینه ده',
                ],
            ],
            'hebrew' => [
                'זה מבחן',
                [
                    'is_capitalized' => true,
                    'is_lowercase'   => false,
                    'ucfirst'        => 'זה מבחן',
                ],
            ],
            'chinese-traditional' => [
                '這是一個測試',
                [
                    'is_capitalized' => true,
                    'is_lowercase'   => false,
                    'ucfirst'        => '這是一個測試',
                ],
            ],
            'urdu' => [
                'کا منشاء برائے',
                [
                    'is_capitalized' => true,
                    'is_lowercase'   => false,
                    'ucfirst'        => 'کا منشاء برائے',
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
                    'is_capitalized' => false,
                    'is_lowercase'   => true,
                ],
            ];
        } else {
            $data['georgian'] = [
                'ეს ტესტია',
                [
                    'is_capitalized' => true,
                    'is_lowercase'   => false,
                    'ucfirst'        => 'ეს ტესტია',
                ],
            ];
        }

        return $data;
    }
}
