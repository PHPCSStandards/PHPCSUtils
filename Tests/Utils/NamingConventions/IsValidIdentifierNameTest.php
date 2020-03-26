<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\NamingConventions;

use PHPCSUtils\Utils\NamingConventions;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHPCSUtils\Utils\NamingConventions::isValidIdentifierName() method.
 *
 * @covers \PHPCSUtils\Utils\NamingConventions::isValidIdentifierName
 *
 * @group namingconventions
 *
 * @since 1.0.0
 */
class IsValidIdentifierNameTest extends TestCase
{

    /**
     * Test correctly detecting whether an arbitrary string can be a valid PHP identifier name.
     *
     * @dataProvider dataIsValidIdentifierName
     *
     * @param string $input    The input string.
     * @param array  $expected The expected function output.
     *
     * @return void
     */
    public function testIsValidIdentifierName($input, $expected)
    {
        $this->assertSame($expected, NamingConventions::isValidIdentifierName($input));
    }

    /**
     * Data provider.
     *
     * @see testIsValidIdentifierName() For the array format.
     *
     * @return array
     */
    public function dataIsValidIdentifierName()
    {
        return [
            // Valid names.
            'a-z-only' => [
                'valid_name',
                true,
            ],
            'a-z-uppercase' => [
                'VALID_NAME',
                true,
            ],
            'a-z-camel-caps' => [
                'Valid_Name',
                true,
            ],
            'alphanum-mixed-case' => [
                'VaLiD128NaMe',
                true,
            ],
            'underscore-prefix' => [
                '_valid_name',
                true,
            ],
            'double-underscore-prefix' => [
                '__valid_name',
                true,
            ],
            'extended-ascii-lowercase' => [
                'пасха',
                true,
            ],
            'extended-ascii-mixed-case' => [
                'Пасха',
                true,
            ],
            'extended-ascii-non-letter' => [
                '¢£¥ƒ¿½¼«»±÷˜°²',
                true,
            ],
            'emoji-name-1' => [
                '💩💩💩',
                true,
            ],
            'emoji-name-2' => [
                '😎',
                true,
            ],

            // Invalid names.
            'not-a-string' => [
                12345,
                false,
            ],
            'empty-string' => [
                '',
                false,
            ],
            'name-with-whitespace' => [
                'aa bb',
                false,
            ],
            'starts-with-number' => [
                '2beornot2be',
                false,
            ],
            'name-with-quotes-in-it' => [
                "aa'1'",
                false,
            ],
            'name-with-dash' => [
                'some-thing',
                false,
            ],
            'name-with-punctuation-chars' => [
                '!@#$%&*(){}[]',
                false,
            ],
        ];
    }
}
