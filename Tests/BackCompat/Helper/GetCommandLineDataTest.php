<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\BackCompat\Helper;

use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Test class.
 *
 * @group helper
 *
 * @since 1.0.0
 */
class GetCommandLineDataTest extends UtilityMethodTestCase
{

    /**
     * Test the getCommandLineData() method.
     *
     * @covers \PHPCSUtils\BackCompat\Helper::getCommandLineData
     *
     * @return void
     */
    public function testGetCommandLineData()
    {
        // Use the default values which are different across PHPCS versions.
        $expected = 'utf-8';
        if (\version_compare(Helper::getVersion(), '2.99.99', '<=') === true) {
            // Will effectively come down to `iso-8859-1`.
            $expected = null;
        }

        $result = Helper::getCommandLineData(self::$phpcsFile, 'encoding');
        $this->assertSame($expected, $result);
    }

    /**
     * Test the getCommandLineData() method when requesting an unknown setting.
     *
     * @covers \PHPCSUtils\BackCompat\Helper::getCommandLineData
     *
     * @return void
     */
    public function testGetCommandLineDataNull()
    {
        $result = Helper::getCommandLineData(self::$phpcsFile, 'foobar');
        $this->assertNull($result);
    }

    /**
     * Test the getTabWidth() method.
     *
     * @covers \PHPCSUtils\BackCompat\Helper::getTabWidth
     *
     * @return void
     */
    public function testGetTabWidth()
    {
        $result = Helper::getTabWidth(self::$phpcsFile);
        $this->assertSame(4, $result, 'Failed retrieving the default tab width');

        if (\version_compare(Helper::getVersion(), '2.99.99', '>') === true) {
            // PHPCS 3.x.
            self::$phpcsFile->config->tabWidth = 2;
        } else {
            // PHPCS 2.x.
            self::$phpcsFile->phpcs->cli->setCommandLineValues(['--tab-width=2']);
        }

        $result = Helper::getTabWidth(self::$phpcsFile);
        $this->assertSame(2, $result, 'Failed retrieving the custom set tab width');

        // Restore defaults before moving to the next test.
        if (\version_compare(Helper::getVersion(), '2.99.99', '>') === true) {
            self::$phpcsFile->config->restoreDefaults();
        } else {
            self::$phpcsFile->phpcs->cli->setCommandLineValues(['--tab-width=4']);
        }
    }

    /**
     * Test the ignoreAnnotations() method.
     *
     * @covers \PHPCSUtils\BackCompat\Helper::ignoreAnnotations
     *
     * @return void
     */
    public function testIgnoreAnnotationsV2()
    {
        if (\version_compare(Helper::getVersion(), '2.99.99', '>') === true) {
            $this->markTestSkipped('Test only applicable to PHPCS 2.x');
        }

        $this->assertFalse(Helper::ignoreAnnotations());
    }

    /**
     * Test the ignoreAnnotations() method.
     *
     * @covers \PHPCSUtils\BackCompat\Helper::ignoreAnnotations
     *
     * @return void
     */
    public function testIgnoreAnnotationsV3Default()
    {
        if (\version_compare(Helper::getVersion(), '2.99.99', '<=') === true) {
            $this->markTestSkipped('Test only applicable to PHPCS 3.x');
        }

        $result = Helper::ignoreAnnotations();
        $this->assertFalse($result, 'Failed default ignoreAnnotations test without passing $phpcsFile');

        $result = Helper::ignoreAnnotations(self::$phpcsFile);
        $this->assertFalse($result, 'Failed default ignoreAnnotations test while passing $phpcsFile');

        // Restore defaults before moving to the next test.
        self::$phpcsFile->config->restoreDefaults();
    }

    /**
     * Test the ignoreAnnotations() method.
     *
     * @covers \PHPCSUtils\BackCompat\Helper::ignoreAnnotations
     *
     * @return void
     */
    public function testIgnoreAnnotationsV3SetViaMethod()
    {
        if (\version_compare(Helper::getVersion(), '2.99.99', '<=') === true) {
            $this->markTestSkipped('Test only applicable to PHPCS 3.x');
        }

        Helper::setConfigData('annotations', false, true);

        $result = Helper::ignoreAnnotations();
        $this->assertTrue($result);

        // Restore defaults before moving to the next test.
        Helper::setConfigData('annotations', true, true);
    }

    /**
     * Test the ignoreAnnotations() method.
     *
     * @covers \PHPCSUtils\BackCompat\Helper::ignoreAnnotations
     *
     * @return void
     */
    public function testIgnoreAnnotationsV3SetViaProperty()
    {
        if (\version_compare(Helper::getVersion(), '2.99.99', '<=') === true) {
            $this->markTestSkipped('Test only applicable to PHPCS 3.x');
        }

        self::$phpcsFile->config->annotations = false;

        $result = Helper::ignoreAnnotations(self::$phpcsFile);
        $this->assertTrue($result);

        // Restore defaults before moving to the next test.
        self::$phpcsFile->config->restoreDefaults();
    }
}
