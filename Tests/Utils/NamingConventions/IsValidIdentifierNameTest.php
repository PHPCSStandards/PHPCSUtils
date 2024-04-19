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
 * @since 1.0.0
 */
final class IsValidIdentifierNameTest extends TestCase
{

    /**
     * Test that non-string input is rejected as invalid for a PHP identifier name.
     *
     * @return void
     */
    public function testIsValidIdentifierNameReturnsFalseOnInvalidType()
    {
        $this->assertFalse(NamingConventions::isValidIdentifierName(12345));
    }

    /**
     * Test correctly detecting whether an arbitrary string can be a valid PHP identifier name.
     *
     * @dataProvider dataIsValidIdentifierName
     *
     * @param string $input    The input string.
     * @param bool   $expected The expected function output.
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
     * @return array<string, array<string, string|bool>>
     */
    public static function dataIsValidIdentifierName()
    {
        return [
            // Valid names.
            'a-z-only' => [
                'input'    => 'valid_name',
                'expected' => true,
            ],
            'a-z-uppercase' => [
                'input'    => 'VALID_NAME',
                'expected' => true,
            ],
            'a-z-camel-caps' => [
                'input'    => 'Valid_Name',
                'expected' => true,
            ],
            'alphanum-mixed-case' => [
                'input'    => 'VaLiD128NaMe',
                'expected' => true,
            ],
            'underscore-prefix' => [
                'input'    => '_valid_name',
                'expected' => true,
            ],
            'double-underscore-prefix' => [
                'input'    => '__valid_name',
                'expected' => true,
            ],
            'extended-ascii-lowercase' => [
                'input'    => 'пасха',
                'expected' => true,
            ],
            'extended-ascii-mixed-case' => [
                'input'    => 'Пасха',
                'expected' => true,
            ],
            'extended-ascii-non-letter' => [
                'input'    => '¢£¥ƒ¿½¼«»±÷˜°²',
                'expected' => true,
            ],
            'emoji-name-1' => [
                'input'    => '💩💩💩',
                'expected' => true,
            ],
            'emoji-name-2' => [
                'input'    => '😎',
                'expected' => true,
            ],

            // Invalid names.
            'empty-string' => [
                'input'    => '',
                'expected' => false,
            ],
            'name-with-whitespace' => [
                'input'    => 'aa bb',
                'expected' => false,
            ],
            'starts-with-number' => [
                'input'    => '2beornot2be',
                'expected' => false,
            ],
            'name-with-quotes-in-it' => [
                'input'    => "aa'1'",
                'expected' => false,
            ],
            'name-with-dash' => [
                'input'    => 'some-thing',
                'expected' => false,
            ],
            'name-with-punctuation-chars' => [
                'input'    => '!@#$%&*(){}[]',
                'expected' => false,
            ],
        ];
    }
}
