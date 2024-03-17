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
use stdClass;

/**
 * Test class.
 *
 * @coversDefaultClass \PHPCSUtils\BackCompat\Helper
 *
 * @since 1.0.0
 */
final class GetCommandLineDataTest extends UtilityMethodTestCase
{

    /**
     * Initialize PHPCS & tokenize the test case file.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = \dirname(\dirname(__DIR__)) . '/DummyFile.inc';
        parent::setUpTestFile();
    }

    /**
     * Test the getCommandLineData() method.
     *
     * @covers ::getCommandLineData
     *
     * @return void
     */
    public function testGetCommandLineData()
    {
        $expected = 'utf-8';
        $result   = Helper::getCommandLineData(self::$phpcsFile, 'encoding');

        $this->assertSame($expected, $result);
    }

    /**
     * Test the getCommandLineData() method when requesting an unknown setting.
     *
     * @covers ::getCommandLineData
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
     * @covers ::getTabWidth
     *
     * @return void
     */
    public function testGetTabWidth()
    {
        self::$phpcsFile->config->tabWidth = null;

        $result = Helper::getTabWidth(self::$phpcsFile);
        $this->assertSame(4, $result, 'Failed retrieving the default tab width');

        self::$phpcsFile->config->tabWidth = 2;

        $result = Helper::getTabWidth(self::$phpcsFile);

        // Restore default before moving to the next test.
        self::$phpcsFile->config->restoreDefaults();

        $this->assertSame(2, $result, 'Failed retrieving the custom set tab width');
    }

    /**
     * Test the getEncoding() method.
     *
     * @covers ::getEncoding
     *
     * @return void
     */
    public function testGetEncoding()
    {
        self::$phpcsFile->config->encoding = null;

        $result   = Helper::getEncoding(self::$phpcsFile);
        $expected = 'utf-8';
        $this->assertSame($expected, $result, 'Failed retrieving the default encoding');

        self::$phpcsFile->config->encoding = 'utf-16';

        $result = Helper::getEncoding(self::$phpcsFile);

        // Restore default before moving to the next test.
        self::$phpcsFile->config->restoreDefaults();

        $this->assertSame('utf-16', $result, 'Failed retrieving the custom set encoding');
    }

    /**
     * Test the getEncoding() method when not passing the PHPCS file parameter.
     *
     * @covers ::getEncoding
     *
     * @return void
     */
    public function testGetEncodingWithoutPHPCSFile()
    {
        self::$phpcsFile->config->encoding = null;

        $result   = Helper::getEncoding();
        $expected = 'utf-8';
        $this->assertSame($expected, $result, 'Failed retrieving the default encoding');

        Helper::setConfigData('encoding', 'utf-16', true, self::$phpcsFile->config);

        $result = Helper::getEncoding();

        // Restore defaults before moving to the next test.
        Helper::setConfigData('encoding', 'utf-8', true, self::$phpcsFile->config);

        $this->assertSame('utf-16', $result, 'Failed retrieving the custom set encoding');
    }

    /**
     * Test the ignoreAnnotations() method.
     *
     * @covers ::ignoreAnnotations
     *
     * @return void
     */
    public function testIgnoreAnnotationsDefault()
    {
        $result = Helper::ignoreAnnotations();
        $this->assertFalse($result, 'Failed default ignoreAnnotations test without passing $phpcsFile');

        $result = Helper::ignoreAnnotations(self::$phpcsFile);

        // Restore defaults before moving to the next test.
        self::$phpcsFile->config->restoreDefaults();

        $this->assertFalse($result, 'Failed default ignoreAnnotations test while passing $phpcsFile');
    }

    /**
     * Test the ignoreAnnotations() method.
     *
     * @covers ::ignoreAnnotations
     *
     * @return void
     */
    public function testIgnoreAnnotationsSetViaMethod()
    {
        $config = null;
        if (isset(self::$phpcsFile->config) === true) {
            $config = self::$phpcsFile->config;
        }

        Helper::setConfigData('annotations', false, true, $config);

        $result = Helper::ignoreAnnotations();

        // Restore defaults before moving to the next test.
        Helper::setConfigData('annotations', true, true, $config);

        $this->assertTrue($result);
    }

    /**
     * Test the ignoreAnnotations() method.
     *
     * @covers ::ignoreAnnotations
     *
     * @return void
     */
    public function testIgnoreAnnotationsSetViaProperty()
    {
        self::$phpcsFile->config->annotations = false;

        $result = Helper::ignoreAnnotations(self::$phpcsFile);

        // Restore defaults before moving to the next test.
        self::$phpcsFile->config->restoreDefaults();

        $this->assertTrue($result);
    }

    /**
     * Test the ignoreAnnotations() method.
     *
     * @covers ::ignoreAnnotations
     *
     * @return void
     */
    public function testIgnoreAnnotationsUsesGetConfigDataWhenInvalidFileParamPassed()
    {
        $config = null;
        if (isset(self::$phpcsFile->config) === true) {
            $config = self::$phpcsFile->config;
        }

        Helper::setConfigData('annotations', false, true, $config);

        $result = Helper::ignoreAnnotations(new stdClass());

        // Restore defaults before moving to the next test.
        Helper::setConfigData('annotations', true, true, $config);

        $this->assertTrue($result);
    }
}
