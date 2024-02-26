<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2021 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\MessageHelper;

use PHPCSUtils\Utils\MessageHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Utils\MessageHelper::showEscapeChars
 *
 * @since 1.0.0
 */
final class ShowEscapeCharsTest extends TestCase
{

    /**
     * Test the showEscapeChars() method.
     *
     * @dataProvider dataShowEscapeChars
     *
     * @param string $input    The input string.
     * @param string $expected The expected function output.
     *
     * @return void
     */
    public function testShowEscapeChars($input, $expected)
    {
        $this->assertSame($expected, MessageHelper::showEscapeChars($input));
    }

    /**
     * Data provider.
     *
     * @see testShowEscapeChars() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataShowEscapeChars()
    {
        return [
            'no-escape-chars' => [
                'input'    => 'if ($var === true) {',
                'expected' => 'if ($var === true) {',
            ],
            'has-escape-chars-in-single-quoted-string' => [
                'input'    => 'if ($var === true) {\r\n\t// Do something.\r\n}',
                'expected' => 'if ($var === true) {\r\n\t// Do something.\r\n}',
            ],
            'has-real-tabs' => [
                'input'    => '$var		= 123;',
                'expected' => '$var\t\t= 123;',
            ],
            'has-tab-escape-chars-in-double-quoted-string' => [
                'input'    => "\$var\t\t= 123;",
                'expected' => '$var\t\t= 123;',
            ],
            'has-real-new-line' => [
                'input'    => '$foo = 123;
$bar = 456;',
                'expected' => '$foo = 123;\n$bar = 456;',
            ],
            'has-new-line-escape-char-in-double-quoted-string' => [
                'input'    => "\$foo = 123;\n\$bar = 456;",
                'expected' => '$foo = 123;\n$bar = 456;',
            ],
            'has-real-tab-and-new-lines' => [
                'input'    => 'if ($var === true) {
	// Do something.
}',
                'expected' => 'if ($var === true) {\n\t// Do something.\n}',
            ],
            'has-tab-and-new-lines-escape-chars-in-double-quoted-string' => [
                'input'    => "if (\$var === true) {\r\n\t// Do something.\r\n}",
                'expected' => 'if ($var === true) {\r\n\t// Do something.\r\n}',
            ],
        ];
    }
}
