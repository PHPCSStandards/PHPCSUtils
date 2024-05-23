<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\FilePath;

use PHPCSUtils\Utils\FilePath;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Utils\FilePath::trailingSlashIt
 *
 * @since 1.1.0
 */
final class TrailingSlashItTest extends TestCase
{

    /**
     * Test ensuring that a directory path ends on a trailing slash.
     *
     * @dataProvider dataTrailingSlashIt
     *
     * @param string|bool $input    The input string.
     * @param string      $expected The expected function output.
     *
     * @return void
     */
    public function testTrailingSlashIt($input, $expected)
    {
        $this->assertSame($expected, FilePath::trailingSlashIt($input));
    }

    /**
     * Data provider.
     *
     * @see testTrailingSlashIt() For the array format.
     *
     * @return array<string, array<string, string|bool>>
     */
    public static function dataTrailingSlashIt()
    {
        return [
            'path is non-string' => [
                'input'    => false,
                'expected' => '',
            ],
            'path is empty string' => [
                'input'    => '',
                'expected' => '',
            ],
            'path is dot' => [
                'input'    => '.',
                'expected' => './',
            ],
            'path with trailing forward slash' => [
                'input'    => 'my/path/to/',
                'expected' => 'my/path/to/',
            ],
            'path with trailing back slash' => [
                'input'    => 'my\path\to\\',
                'expected' => 'my\path\to/',
            ],
            'path without trailing slash' => [
                'input'    => 'my/path/to',
                'expected' => 'my/path/to/',
            ],
            'path to a file with an extension' => [
                'input'    => 'my/path/to/filename.ext',
                'expected' => 'my/path/to/filename.ext',
            ],
            'path to a dot file' => [
                'input'    => 'my/path/to/.gitignore',
                'expected' => 'my/path/to/.gitignore',
            ],
            'path ending on a dot' => [
                'input'    => 'my/path/to/dot.',
                'expected' => 'my/path/to/dot./',
            ],
            'path with trailing forward slash and the last dir contains a dot' => [
                'input'    => 'my/path/to.ext/',
                'expected' => 'my/path/to.ext/',
            ],
        ];
    }
}
