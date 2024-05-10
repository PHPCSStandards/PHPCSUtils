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
 * @covers \PHPCSUtils\Utils\FilePath::normalizeDirectorySeparators
 *
 * @since 1.1.0
 */
final class NormalizeDirectorySeparatorsTest extends TestCase
{

    /**
     * Test normalizing the directory separators in a path.
     *
     * @dataProvider dataNormalizeDirectorySeparators
     *
     * @param string $input    The input string.
     * @param string $expected The expected function output.
     *
     * @return void
     */
    public function testNormalizeDirectorySeparators($input, $expected)
    {
        $this->assertSame($expected, FilePath::normalizeDirectorySeparators($input));
    }

    /**
     * Data provider.
     *
     * @see testNormalizeDirectorySeparators() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataNormalizeDirectorySeparators()
    {
        return [
            'path is dot' => [
                'input'    => '.',
                'expected' => '.',
            ],
            'path containing forward slashes only' => [
                'input'    => 'my/path/to/',
                'expected' => 'my/path/to/',
            ],
            'path containing back-slashes only' => [
                'input'    => 'my\path\to\\',
                'expected' => 'my/path/to/',
            ],
            'path containing a mix of forward and backslashes' => [
                'input'    => 'my\path/to\\',
                'expected' => 'my/path/to/',
            ],
        ];
    }
}
