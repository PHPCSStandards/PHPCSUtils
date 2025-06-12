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
 * @covers \PHPCSUtils\Utils\FilePath::isStdin
 *
 * @since 1.1.0
 */
final class IsStdinTest extends TestCase
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
     * Test checking whether the input comes from STDIN or not.
     *
     * @dataProvider dataIsStdin
     *
     * @param string $fileName The file name to pass.
     * @param bool   $expected The expected function return value.
     *
     * @return void
     */
    public function testIsStdin($fileName, $expected)
    {
        $content  = 'phpcs_input_file: ' . $fileName . \PHP_EOL;
        $content .= '<?php ' . \PHP_EOL . '$var = FALSE;' . \PHP_EOL;

        $phpcsFile = new DummyFile($content, self::$ruleset, self::$config);

        $this->assertSame($expected, FilePath::isStdin($phpcsFile));
    }

    /**
     * Data provider.
     *
     * @see testIsStdin() For the array format.
     *
     * @return array<string, array<string, string|bool>>
     */
    public static function dataIsStdin()
    {
        return [
            'path is dot' => [
                'fileName' => '.',
                'expected' => false,
            ],
            'path to file' => [
                'fileName' => 'my/path/to/',
                'expected' => false,
            ],
            'path is stdin' => [
                'fileName' => 'STDIN',
                'expected' => true,
            ],
            'path is stdin (single-quoted)' => [
                'fileName' => "'STDIN'",
                'expected' => true,
            ],
            'path is stdin (double-quoted)' => [
                'fileName' => '"STDIN"',
                'expected' => true,
            ],
        ];
    }
}
