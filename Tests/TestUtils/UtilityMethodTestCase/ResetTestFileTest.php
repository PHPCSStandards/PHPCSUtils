<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\TestUtils\UtilityMethodTestCase;

use PHP_CodeSniffer\Config;
use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\Tests\PolyfilledTestCase;
use ReflectionProperty;

/**
 * Tests for the \PHPCSUtils\TestUtils\UtilityMethodTestCase class.
 *
 * @covers \PHPCSUtils\TestUtils\UtilityMethodTestCase::resetTestFile
 *
 * @since 1.0.0
 */
final class ResetTestFileTest extends PolyfilledTestCase
{

    /**
     * Overload the "normal" set up as it needs to be run from within the actual test(s) to ensure we have a valid test.
     *
     * Note: We don't rely on this method being called "before class" as the tests in this class reset the statics on
     * the UtilityMethodTestCase, so we need to be sure the static $caseFile property is set (again) before parsing
     * a file and do that by calling this method directly from within each of the tests.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = __DIR__ . '/SetUpTestFileTest.inc';
        // Deliberately not running the actual setUpTestFile() method.
    }

    /**
     * Overload the "normal" test marker QA check - this test class resets the property containing the File object,
     * so there will be no valid File + the case file is already validated in the `SetUpTestFileTest` class anyway.
     *
     * @coversNothing
     * @doesNotPerformAssertions
     *
     * @return void
     */
    public function testTestMarkersAreUnique()
    {
        // Deliberately left empty.
    }

    /**
     * Test that the static class properties in the class are correctly reset.
     *
     * @return void
     */
    public function testTearDownCleansUpStaticTestCaseClassProperties()
    {
        // Initialize a test, which should change the values of most static properties.
        self::$tabWidth      = 2;
        self::$selectedSniff = ['Test.Test.Test'];
        self::setUpTestFile();
        parent::setUpTestFile();

        // Verify that (most) properties no longer have their original value.
        $this->assertNotSame('0', self::$phpcsVersion, 'phpcsVersion was not updated');
        $this->assertSame('inc', self::$fileExtension, 'fileExtension was (not) updated');
        $this->assertNotSame('', self::$caseFile, 'caseFile was not updated');
        $this->assertNotSame(4, self::$tabWidth, 'tabWidth was not updated');
        $this->assertNotNull(self::$phpcsFile, 'phpcsFile was not updated');
        $this->assertNotSame(['Dummy.Dummy.Dummy'], self::$selectedSniff, 'selectedSniff was not updated');

        // Reset the file as per the "afterClass"/tear down method.
        parent::resetTestFile();

        // Verify the properties in the class have been cleaned up.
        $this->assertSame('0', self::$phpcsVersion, 'phpcsVersion was not reset');
        $this->assertSame('inc', self::$fileExtension, 'fileExtension was not reset');
        $this->assertSame('', self::$caseFile, 'caseFile was not reset');
        $this->assertSame(4, self::$tabWidth, 'tabWidth was not reset');
        $this->assertNull(self::$phpcsFile, 'phpcsFile was not reset');
        $this->assertSame(['Dummy.Dummy.Dummy'], self::$selectedSniff, 'selectedSniff was not reset');
    }

    /**
     * Test that the static properties in the Config class are correctly reset.
     *
     * Ensure that Config set for one test does not influence the next test.
     *
     * @return void
     */
    public function testTearDownCleansUpStaticConfigProperties()
    {
        $fakeConfFile = 'path/to/file.conf';
        $toolName     = 'a_tool';

        // Set up preconditions.
        self::setUpTestFile();
        parent::setUpTestFile();

        $config = self::$phpcsFile->config;
        Helper::setConfigData('arbitraryKey', 'arbitraryValue', true, $config);
        $config->setStaticConfigProperty('configDataFile', $fakeConfFile);
        $config::getExecutablePath($toolName);

        // Verify the static properties in the Config are set to something other than their default value.
        $this->assertSame(['PSR1'], $config->standards, 'Precondition check: Standards was not set to PSR1');

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

        // Reset the file as per the "afterClass"/tear down method.
        parent::resetTestFile();

        // Verify that the reset also reset the static properties on the Config class.
        $this->assertSame(
            [],
            $this->getStaticConfigProperty('overriddenDefaults'),
            'overriddenDefaults reset failed'
        );
        $this->assertSame([], $this->getStaticConfigProperty('executablePaths'), 'executablePaths reset failed');
        $this->assertNull($this->getStaticConfigProperty('configData'), 'configData reset failed');
        $this->assertNull($this->getStaticConfigProperty('configDataFile'), 'configDataFile reset failed');

        $this->assertNull(Helper::getConfigData('arbitraryKey'), 'arbitraryKey property is still set');

        // Now check that if a new Config is created for another test, that previously overridden defaults can be set again.
        $newConfig = new Config(['--standard=Squiz']);

        // Verify the new Config does not have leaked property values from the Config from the "previous test".
        $this->assertSame(['Squiz'], $newConfig->standards, 'New standards choice was not set to Squiz');
        $this->assertSame(
            ['standards' => true],
            $this->getStaticConfigProperty('overriddenDefaults', $newConfig),
            'overriddenDefaults has not been reinitialized'
        );

        // Verify that previously overridden config property can be written to.
        $newValue = 'new value';
        $this->assertTrue(
            Helper::setConfigData('arbitraryKey', $newValue, true, $newConfig),
            'New value for arbitraryKey is not accepted'
        );
        $this->assertSame(
            $newValue,
            Helper::getConfigData('arbitraryKey'),
            'Previously overridden default was not allowed to be set'
        );
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
        $property->setAccessible(true);

        if ($name === 'overriddenDefaults'
            && (self::$phpcsVersion === '0' || \version_compare(self::$phpcsVersion, '3.99.99', '>'))
        ) {
            // The `overriddenDefaults` property is no longer static on PHPCS 4.0+.
            if (isset($config)) {
                return $property->getValue($config);
            } else {
                return [];
            }
        }

        return $property->getValue();
    }
}
