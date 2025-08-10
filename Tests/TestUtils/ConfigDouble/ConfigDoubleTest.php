<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\TestUtils\ConfigDouble;

use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\TestUtils\ConfigDouble;
use ReflectionProperty;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Tests for the \PHPCSUtils\TestUtils\ConfigDouble class.
 *
 * @coversDefaultClass \PHPCSUtils\TestUtils\ConfigDouble
 *
 * @group testutils
 *
 * @since 1.1.0
 */
final class ConfigDoubleTest extends TestCase
{

    /**
     * Reset the static properties in the Config class to their true defaults to prevent this class
     * from influencing other tests.
     *
     * @afterClass
     *
     * @return void
     */
    public static function resetConfigToDefaults()
    {
        self::setStaticConfigProperty('overriddenDefaults', []);
        self::setStaticConfigProperty('executablePaths', []);
        self::setStaticConfigProperty('configData', null);
        self::setStaticConfigProperty('configDataFile', null);
        $_SERVER['argv'] = [];
    }

    /**
     * Verify that the static properties in the Config class get cleared between instances.
     *
     * @covers ::resetSelectProperties
     * @covers ::getStaticConfigProperty
     * @covers ::setStaticConfigProperty
     *
     * @return void
     */
    public function testOverriddenDefaultsGetCleared()
    {
        $configA = new ConfigDouble(['--standard=PSR12', '--severity=2', '-sp']);

        // Verify that the CLI args initialized the "overriddenDefaults" property and that the settings took effect.
        $this->assertCount(
            5,
            $configA->getStaticConfigProperty('overriddenDefaults'),
            'Initialize: expected overriddenDefaults entry count does not match'
        );
        $this->assertSame(['PSR12'], $configA->standards, 'Initialize: standards was not set to PSR12');
        $this->assertSame(2, $configA->warningSeverity, 'Initialize: warningSeverity was not set to 2');
        $this->assertSame(2, $configA->errorSeverity, 'Initialize: errorSeverity was not set to 2');
        $this->assertTrue($configA->showSources, 'Initialize: showSources was not set to "true"');
        $this->assertTrue($configA->showProgress, 'Initialize: showProgress was not set to "true"');

        $configB = new ConfigDouble();

        // Verify that the "overriddenDefaults" do not persist to the next Config instance.
        $this->assertLessThanOrEqual(
            2, // Standards should be the only thing set, though files _may_ also be set.
            \count($configB->getStaticConfigProperty('overriddenDefaults')),
            'Reset did not wipe overriddenDefaults. Found: '
                . \var_export($configB->getStaticConfigProperty('overriddenDefaults'), true)
        );
        $this->assertSame(['PSR1'], $configB->standards, 'Ruleset search prevention did not set standard to PSR1');
        $this->assertSame(5, $configB->warningSeverity, 'Reset did not wipe warningSeverity');
        $this->assertSame(5, $configB->errorSeverity, 'Reset did not wipe errorSeverity');
        $this->assertFalse($configB->showSources, 'Reset did not wipe showSources');
        $this->assertFalse($configB->showProgress, 'Reset did not wipe showProgress');
    }

    /**
     * Verify that when no standard is given, the default standard (PEAR) is overridden with the smaller PSR1.
     *
     * Additionally verifies that `standards` is added to the "overriddenDefaults" array, which is what prevents
     * the file system search for a ruleset.
     *
     * @covers ::setCommandLineValues
     * @covers ::preventSearchingForRuleset
     *
     * @return void
     */
    public function testDefaultStandardIsOverridden()
    {
        $config = new ConfigDouble();

        $this->assertSame(['PSR1'], $config->standards, 'Standards was not set to PSR1');

        $overriddenDefaults = $config->getStaticConfigProperty('overriddenDefaults');
        $this->assertArrayHasKey('standards', $overriddenDefaults, 'Standards was not added to overriddenDefaults');
        $this->assertTrue($overriddenDefaults['standards'], 'Standards was not marked as overridden');
    }

    /**
     * Verify that if a standard is set via the Config $args, that the Double doesn't override this to PSR1.
     *
     * @covers ::setCommandLineValues
     * @covers ::preventSearchingForRuleset
     *
     * @return void
     */
    public function testStandardSetViaArgsIsRespected()
    {
        $config = new ConfigDouble(['--standard=Squiz']);

        $this->assertSame(['Squiz'], $config->standards, 'Standards was not set to Squiz');

        $overriddenDefaults = $config->getStaticConfigProperty('overriddenDefaults');
        $this->assertArrayHasKey('standards', $overriddenDefaults, 'Standards was not added to overriddenDefaults');
        $this->assertTrue($overriddenDefaults['standards'], 'Standards was not marked as overridden');
    }

    /**
     * Test that the `$skipSettingStandard` option prevents the standard being set by the double.
     *
     * @covers ::__construct
     * @covers ::setCommandLineValues
     *
     * @return void
     */
    public function testStandardOverrideIsSkippedOnRequest()
    {
        $config = new ConfigDouble([], true);

        $this->assertNotSame(['PSR1'], $config->standards, 'Standards was still overloaded to be PSR1');

        // This will normally be `phpcs.xml.dist`, but the contributor running the tests may have an overload file in place.
        $this->assertStringContainsString('phpcs.xml', $config->standards[0], 'Standards auto-discovery did not take place');

        $overriddenDefaults = $config->getStaticConfigProperty('overriddenDefaults');
        $this->assertArrayNotHasKey('standards', $overriddenDefaults, 'Standards was still added to overriddenDefaults');
    }

    /**
     * Verify that the reportWidth will be set to the default width when implicitly set to "auto".
     *
     * @covers ::preventAutoDiscoveryScreenWidth
     *
     * @return void
     */
    public function testDefaultReportWidthAutoIsOverridden()
    {
        $config = new ConfigDouble();

        $this->assertSame(80, $config->reportWidth, 'Report width was not set to 80');
    }

    /**
     * Verify that the reportWidth will be set to the default width when explicitly set to "auto".
     *
     * @covers ::preventAutoDiscoveryScreenWidth
     *
     * @return void
     */
    public function testReportWidthSetToAutoViaArgsIsOverridden()
    {
        $config = new ConfigDouble(['--report-width=auto']);

        $this->assertSame(80, $config->reportWidth, 'Report width was not set to 80');

        $overriddenDefaults = $config->getStaticConfigProperty('overriddenDefaults');
        $this->assertArrayHasKey('reportWidth', $overriddenDefaults, 'reportWidth was not added to overriddenDefaults');
        $this->assertTrue($overriddenDefaults['reportWidth'], 'reportWidth was not marked as overridden');
    }

    /**
     * Verify that if a reportWidth is set via the Config $args, that the Double doesn't override this to the default width.
     *
     * @covers ::preventAutoDiscoveryScreenWidth
     *
     * @return void
     */
    public function testReportWidthSetToIntViaArgsIsRespected()
    {
        $config = new ConfigDouble(['--report-width=1250']);

        $this->assertSame(1250, $config->reportWidth, 'Report width was not set to 1250');

        $overriddenDefaults = $config->getStaticConfigProperty('overriddenDefaults');
        $this->assertArrayHasKey('reportWidth', $overriddenDefaults, 'reportWidth was not added to overriddenDefaults');
        $this->assertTrue($overriddenDefaults['reportWidth'], 'reportWidth was not marked as overridden');
    }

    /**
     * Test that the `$skipSettingReportWidth` option prevents the report width being set by the double.
     *
     * @covers ::__construct
     *
     * @return void
     */
    public function testReportWidthOverrideIsSkippedOnRequest()
    {
        $config = new ConfigDouble([], false, true);

        // Can't test the exact value as "auto" will resolve differently depending on the machine running the tests.
        $this->assertIsInt($config->reportWidth, 'Report width is not an integer');
        $this->assertGreaterThan(0, $config->reportWidth, 'Report width is not greater than 0');

        if (\getenv('CI') === false) {
            // Not entirely stable as an contributors screen _may_ actually be 80 wide,
            // though in this modern age this is unlikely.
            // Skipping for CI as GH Actions **will** identify the screen width as 80 wide.
            $this->assertNotSame(80, $config->reportWidth, 'Report width has still been set to 80');
        }
    }

    /**
     * Verify that the static properties in the Config class are reset when the object is destroyed.
     *
     * @covers ::__destruct
     *
     * @return void
     */
    public function testDestruct()
    {
        $standard     = 'Squiz';
        $reportWidth  = 1250;
        $fakeConfFile = 'path/to/file.conf';
        $toolName     = 'a_tool';

        // Create the ConfigDouble object and change the value of a few static properties to allow for testing the reset.
        $cliArgs = [
            '--standard=' . $standard,
            '--report-width=' . $reportWidth,
            '--runtime-set',
            'arbitraryKey',
            'arbitraryValue',
        ];
        $config  = new ConfigDouble($cliArgs);

        $config->setStaticConfigProperty('configDataFile', $fakeConfFile);
        $config::getExecutablePath($toolName);

        // Verify the static properties in the Config are set to something other than their default value.
        $this->assertSame([$standard], $config->standards, 'Precondition check: Standards was not set to Squiz');
        $this->assertSame($reportWidth, $config->reportWidth, 'Precondition check: Report width was not set to 1250');
        $this->assertSame(
            'arbitraryValue',
            Helper::getConfigData('arbitraryKey'),
            'Precondition check: ArbitraryKey property was not set on the Config class'
        );

        $overriddenDefaults = $this->getStaticConfigProperty('overriddenDefaults', $config);
        $this->assertIsArray($overriddenDefaults, 'Precondition check: overriddenDefaults property is not an array');
        $this->assertNotEmpty($overriddenDefaults, 'Precondition check: overriddenDefaults property is an empty array');

        $this->assertSame(
            [$toolName => null],
            $this->getStaticConfigProperty('executablePaths', $config),
            'Precondition check: executablePaths is still an empty array'
        );

        $configData = $this->getStaticConfigProperty('configData', $config);
        $this->assertIsArray($configData, 'Precondition check: configData property is not an array');
        $this->assertNotEmpty($configData, 'Precondition check: configData property is an empty array');

        $this->assertSame(
            $fakeConfFile,
            $this->getStaticConfigProperty('configDataFile', $config),
            'Precondition check: configDataFile property has not been set'
        );

        // Destroy the object.
        unset($config);

        // Verify the static properties in the Config are reset to their default values.
        $this->assertSame([], $this->getStaticConfigProperty('overriddenDefaults'), 'overriddenDefaults reset failed');
        $this->assertSame([], $this->getStaticConfigProperty('executablePaths'), 'executablePaths reset failed');
        $this->assertNull($this->getStaticConfigProperty('configData'), 'configData reset failed');
        $this->assertNull($this->getStaticConfigProperty('configDataFile'), 'configDataFile reset failed');

        // This assertion must be last as it re-initializes the $configData and $configDataFile properties.
        $this->assertNull(Helper::getConfigData('arbitraryKey'), 'arbitraryKey property is still set');
    }

    /**
     * Helper function to retrieve the value of a private static property on the Config class.
     *
     * @param string                  $name   The name of the property to retrieve.
     * @param \PHP_CodeSniffer\Config $config Optional. The config object.
     *
     * @return mixed
     */
    private function getStaticConfigProperty($name, $config = null)
    {
        $property = new ReflectionProperty('PHP_CodeSniffer\Config', $name);
        (\PHP_VERSION_ID < 80100) && $property->setAccessible(true);

        if ($name === 'overriddenDefaults' && \version_compare(Helper::getVersion(), '3.99.99', '>')) {
            // The `overriddenDefaults` property is no longer static on PHPCS 4.0+.
            if (isset($config)) {
                return $property->getValue($config);
            } else {
                return [];
            }
        }

        return $property->getValue();
    }

    /**
     * Helper function to set the value of a private static property on the Config class.
     *
     * @param string $name  The name of the property to set.
     * @param mixed  $value The value to set the property to.
     *
     * @return void
     */
    public static function setStaticConfigProperty($name, $value)
    {
        // The `overriddenDefaults` property is no longer static on PHPCS 4.0+, so ignore it.
        if ($name === 'overriddenDefaults' && \version_compare(Helper::getVersion(), '3.99.99', '>')) {
            return;
        }

        $property = new ReflectionProperty('PHP_CodeSniffer\Config', $name);
        (\PHP_VERSION_ID < 80100) && $property->setAccessible(true);
        $property->setValue(null, $value);
        (\PHP_VERSION_ID < 80100) && $property->setAccessible(false);
    }
}
