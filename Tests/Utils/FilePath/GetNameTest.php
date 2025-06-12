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

use PHP_CodeSniffer\Files\DummyFile;
use PHPCSUtils\TestUtils\ConfigDouble;
use PHPCSUtils\TestUtils\RulesetDouble;
use PHPCSUtils\Utils\FilePath;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Utils\FilePath::getName
 *
 * @since 1.1.0
 */
final class GetNameTest extends TestCase
{

    /**
     * Config object for use in the tests.
     *
     * @var \PHP_CodeSniffer\Config
     */
    private static $config;

    /**
     * Ruleset object for use in the tests.
     *
     * @var \PHP_CodeSniffer\Ruleset
     */
    private static $ruleset;

    /**
     * Initialize a PHPCS config and ruleset objects.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpConfigRuleset()
    {
        parent::setUpBeforeClass();

        self::$config         = new ConfigDouble();
        self::$config->sniffs = ['Dummy.Dummy.Dummy']; // Limiting it to just one (dummy) sniff.
        self::$config->cache  = false;

        self::$ruleset = new RulesetDouble(self::$config);
    }

    /**
     * Test retrieving the normalized file name.
     *
     * @dataProvider dataGetName
     *
     * @param string $fileName The file name to pass.
     * @param string $expected The expected function return value.
     *
     * @return void
     */
    public function testGetName($fileName, $expected)
    {
        $content  = 'phpcs_input_file: ' . $fileName . \PHP_EOL;
        $content .= '<?php ' . \PHP_EOL . '$var = FALSE;' . \PHP_EOL;

        $phpcsFile = new DummyFile($content, self::$ruleset, self::$config);

        $this->assertSame($expected, FilePath::getName($phpcsFile));
    }

    /**
     * Data provider.
     *
     * @see testGetName() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataGetName()
    {
        return [
            'file path is empty string' => [
                'fileName' => '',
                'expected' => '',
            ],
            'file path is stdin' => [
                'fileName' => 'STDIN',
                'expected' => 'STDIN',
            ],
            'file path is stdin (single-quoted)' => [
                'fileName' => "'STDIN'",
                'expected' => 'STDIN',
            ],
            'file path is stdin (double-quoted)' => [
                'fileName' => '"STDIN"',
                'expected' => 'STDIN',
            ],
            'file path is dot' => [
                'fileName' => '.',
                'expected' => './',
            ],
            'file path is file name only' => [
                'fileName' => 'filename.php',
                'expected' => 'filename.php',
            ],
            'file path is file name only (single-quoted)' => [
                'fileName' => "'filename.php'",
                'expected' => 'filename.php',
            ],
            'file path is file name only (double-quoted)' => [
                'fileName' => '"filename.php"',
                'expected' => 'filename.php',
            ],
            'file path with forward slashes' => [
                'fileName' => 'my/path/to/filename.php',
                'expected' => 'my/path/to/filename.php',
            ],
            'file path with backslashes' => [
                'fileName' => 'my\path\to\filename.js',
                'expected' => 'my/path/to/filename.js',
            ],
            'file path containing a mix of forward and backslashes' => [
                'fileName' => '/my\path/to\myfile.inc',
                'expected' => '/my/path/to/myfile.inc',
            ],
            'full windows file path, backslashes only' => [
                'fileName' => 'C:\my\path\to\filename.css',
                'expected' => 'C:/my/path/to/filename.css',
            ],
        ];
    }
}
