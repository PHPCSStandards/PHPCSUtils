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
 * @covers \PHPCSUtils\Utils\FilePath::startsWith
 *
 * @since 1.1.0
 */
final class StartsWithTest extends TestCase
{

    /**
     * Test verifying whether a certain path starts with another path.
     *
     * @dataProvider dataStartsWith
     *
     * @param string $haystack Directory path to search in.
     * @param string $needle   Path the haystack path should start with.
     * @param string $expected The expected function output.
     *
     * @return void
     */
    public function testStartsWith($haystack, $needle, $expected)
    {
        $this->assertSame($expected, FilePath::startsWith($haystack, $needle));
    }

    /**
     * Data provider.
     *
     * @see testStartsWith() For the array format.
     *
     * @return array<string, array<string, string|bool>>
     */
    public static function dataStartsWith()
    {
        return [
            'path equal to other path, forward slashes' => [
                'haystack' => '/my/path/to/',
                'needle'   => '/my/path/to/',
                'expected' => true,
            ],
            'path starting with other path, forward slashes' => [
                'haystack' => '/my/path/to/some/sub/directory',
                'needle'   => '/my/path/to/',
                'expected' => true,
            ],
            'path equal to other path, back slashes' => [
                'haystack' => 'C:\my\path\to\\',
                'needle'   => 'C:\my\path\to\\',
                'expected' => true,
            ],
            'path starting with other path, back slashes' => [
                'haystack' => 'C:\my\path\to\some\sub\directory',
                'needle'   => 'C:\my\path\to\\',
                'expected' => true,
            ],
            'path starting with other path, but slashes are different' => [
                'haystack' => '\my\path\to\some\sub\directory',
                'needle'   => '/my/path/to/',
                'expected' => false,
            ],
            'path starting with other path, but case is different' => [
                'haystack' => '/My/path/To/some/Sub/directory',
                'needle'   => '/my/path/to/',
                'expected' => false,
            ],
            'path NOT starting with other path, forward slashes' => [
                'haystack' => '/my/path/too/some/sub/directory',
                'needle'   => '/my/path/to/',
                'expected' => false,
            ],
            'path NOT starting with other path, back slashes' => [
                'haystack' => 'C:\my\path\too\some\sub\directory',
                'needle'   => 'C:\my\path\to\\',
                'expected' => false,
            ],
            'completely different paths' => [
                'haystack' => '/your/subdir/',
                'needle'   => 'my/path/to/',
                'expected' => false,
            ],
        ];
    }
}
