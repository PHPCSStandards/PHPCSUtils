<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\TestUtils\ConfigDouble;

use Exception;
use PHPCSUtils\TestUtils\ConfigDouble;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHPCSUtils\TestUtils\ConfigDouble class.
 *
 * @covers \PHPCSUtils\TestUtils\ConfigDouble::preventReadingCodeSnifferConfFile
 *
 * @group testutils
 *
 * @since 1.1.0
 */
final class PreventReadingCodeSnifferConfFileTest extends TestCase
{

    /**
     * Location of the applicable CodeSniffer.conf file.
     *
     * @var string
     */
    private static $pathToConfFile = '';

    /**
     * Backup of the contents of the contributor's CodeSniffer.conf file.
     *
     * @var string
     */
    private static $originalConfFile = '';

    /**
     * The contents for the CodeSniffer.conf file to be used in the test.
     *
     * @var string
     */
    private static $testConfFileContents = <<<'EOD'
<?php
$phpCodeSnifferConfig = array (
    'report_width' => '150',
    'php_version'  => '70000',
    'php_path'     => 'path/to/php.exe',
    'colors'       => '1',
);
EOD;

    /**
     * Skip message for when the tests need to be skipped due to errors while setting up.
     *
     * Note: "set up before class" methods can "mark a test as skipped", while "set up" methods can.
     * This property is used to pass any potential errors encountered during "set up before class"
     * to the "set up" method.
     *
     * @var string
     */
    private static $skip = '';

    /**
     * Create a backup of the CodeSniffer.conf file and create the CodeSniffer.conf file to be used in the test.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function backupCodeSnifferConf()
    {
        // Get the PHPCS dir from an environment variable (if set).
        $phpcsDir = \getenv('PHPCS_DIR');

        // This may be a Composer install.
        if ($phpcsDir === false && \is_dir(__DIR__ . '/../../../vendor')) {
            $vendorDir = __DIR__ . '/../../../vendor';
            if (\is_dir($vendorDir . '/squizlabs/php_codesniffer')) {
                $phpcsDir = $vendorDir . '/squizlabs/php_codesniffer';
            }
        }

        if ($phpcsDir === false) {
            self::$skip = 'Could not determine path to the PHPCS instance being used to run the tests.';
            return;
        }

        $phpcsDir             = \realpath($phpcsDir);
        self::$pathToConfFile = $phpcsDir . '/CodeSniffer.conf';

        // Safeguard the contributors CodeSniffer.conf file.
        if (\file_exists(self::$pathToConfFile)) {
            if (\copy(self::$pathToConfFile, self::$pathToConfFile . '.bak') === false) {
                self::$skip = 'Making a backup of the CodeSniffer.conf file failed.'
                    . ' Skipping the test to prevent damaging the contributors setup.';
                return;
            }

            self::$originalConfFile = \file_get_contents(self::$pathToConfFile);
        }

        if (\file_put_contents(self::$pathToConfFile, self::$testConfFileContents) === false) {
            self::$skip = 'Failed to create the CodeSniffer.conf file for the test.';
            return;
        }
    }

    /**
     * Skip the tests if the "set up before class" ran into trouble.
     *
     * @before
     *
     * @return void
     */
    protected function maybeSkip()
    {
        if (self::$skip !== '') {
            $this->markTestSkipped(self::$skip);
        }
    }

    /**
     * Restore the original CodeSniffer.conf file.
     *
     * @afterClass
     *
     * @return void
     */
    public static function restoreCodeSnifferConf()
    {
        if (self::$pathToConfFile === '' || self::$originalConfFile === '') {
            return;
        }

        if (\file_put_contents(self::$pathToConfFile, self::$originalConfFile) === false) {
            throw new Exception(
                \sprintf(
                    'Failed to restore the CodeSniffer.conf file. There is a backup of the original file available in %s.'
                    . ' Please restore the file manually.',
                    self::$pathToConfFile . '.bak'
                )
            );
        }
    }

    /**
     * Verify that config values set in a user `CodeSniffer.conf` file are disregarded when using the Double class.
     *
     * @dataProvider dataConfigDoesNotGetTakenFromConfFile
     *
     * @param string $name          The configuration setting name.
     * @param mixed  $expectedValue The expected value for the setting.
     *
     * @return void
     */
    public function testConfigDoesNotGetTakenFromConfFile($name, $expectedValue)
    {
        $config = new ConfigDouble();

        $this->assertSame($expectedValue, $config->$name);
    }

    /**
     * Data provider.
     *
     * @see testConfigDoesNotGetTakenFromConfFile()
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dataConfigDoesNotGetTakenFromConfFile()
    {
        return [
            'report-width' => [
                'name'          => 'reportWidth',
                'expectedValue' => 80,
            ],
            'standard' => [
                'name'          => 'standards',
                'expectedValue' => ['PSR1'],
            ],
            'colors' => [
                'name'          => 'colors',
                'expectedValue' => false,
            ],
        ];
    }

    /**
     * Verify that config values set in a user `CodeSniffer.conf` file are disregarded when using the Double class.
     *
     * @return void
     */
    public function testConfigDoesNotGetTakenFromConfFileForExecutablePaths()
    {
        $config = new ConfigDouble();

        $this->assertSame(\PHP_BINARY, $config->getExecutablePath('php'));
    }

    /**
     * Verify that config values set in a user `CodeSniffer.conf` file are disregarded when using the Double class.
     *
     * @return void
     */
    public function testConfigDoesNotGetTakenFromConfFileForConfigData()
    {
        $config = new ConfigDouble();

        $this->assertNull($config->getConfigData('php_version'));
    }
}
