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
 * @covers \PHPCSUtils\Utils\FilePath::normalizeAbsolutePath
 *
 * @since 1.1.0
 */
final class NormalizePathTest extends TestCase
{

    /**
     * Test normalizing an absolute directory path.
     *
     * @dataProvider dataNormalizePath
     *
     * @param string $input    The input string.
     * @param string $absolute The expected function output.
     *
     * @return void
     */
    public function testNormalizeAbsolutePath($input, $absolute)
    {
        $this->assertSame($absolute, FilePath::normalizeAbsolutePath($input));
    }

    /**
     * Data provider.
     *
     * @see testNormalizeAbsolutePath() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataNormalizePath()
    {
        return [
            'path is dot' => [
                'input'    => '.',
                'absolute' => './',
            ],
            'path containing forward slashes only with trailing slash' => [
                'input'    => 'my/path/to/',
                'absolute' => 'my/path/to/',
            ],
            'path containing forward slashes only without trailing slash' => [
                'input'    => 'my/path/to',
                'absolute' => 'my/path/to/',
            ],
            'path containing forward slashes only with leading and trailing slash' => [
                'input'    => '/my/path/to/',
                'absolute' => '/my/path/to/',
            ],
            'path containing back-slashes only with trailing slash' => [
                'input'    => 'my\path\to\\',
                'absolute' => 'my/path/to/',
            ],
            'path containing back-slashes only without trailing slash' => [
                'input'    => 'my\path\to',
                'absolute' => 'my/path/to/',
            ],
            'path containing back-slashes only with leading, no trailing slash' => [
                'input'    => '\my\path\to',
                'absolute' => '/my/path/to/',
            ],
            'path containing a mix of forward and backslashes with leading and trailing slash' => [
                'input'    => '/my\path/to\\',
                'absolute' => '/my/path/to/',
            ],
            'path containing a mix of forward and backslashes without trailing slash' => [
                'input'    => 'my\path/to',
                'absolute' => 'my/path/to/',
            ],
        ];
    }
}
