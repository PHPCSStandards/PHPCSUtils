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

use PHP_CodeSniffer\Config;
use PHPCSUtils\BackCompat\Helper;
use PHPUnit\Framework\TestCase;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectException;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\BackCompat\Helper::setConfigData
 * @covers \PHPCSUtils\BackCompat\Helper::getConfigData
 *
 * @group helper
 *
 * @since 1.0.0
 */
class ConfigDataTest extends TestCase
{
    use ExpectException;

    /**
     * Test the getConfigData() and setConfigData() method when used in a cross-version compatible manner.
     *
     * @return void
     */
    public function testConfigData34()
    {
        $config   = new Config();
        $original = Helper::getConfigData('arbitrary_name');
        $expected = 'expected';

        $return = Helper::setConfigData('arbitrary_name', $expected, true, $config);
        $this->assertTrue($return);

        $result = Helper::getConfigData('arbitrary_name');
        $this->assertSame($expected, $result);

        // Reset the value after the test.
        Helper::setConfigData('arbitrary_name', $original, true, $config);
    }

    /**
     * Test the getConfigData() and setConfigData() method when used in a non-PHPCS 4.x compatible manner.
     *
     * @return void
     */
    public function testConfigDataPHPCS3()
    {
        if (\version_compare(Helper::getVersion(), '3.99.99', '>') === true) {
            $this->markTestSkipped('Test only applicable to PHPCS < 4.x');
        }

        $original = Helper::getConfigData('arbitrary_name');
        $expected = 'expected';

        $return = Helper::setConfigData('arbitrary_name', $expected, true);
        $this->assertTrue($return);

        $result = Helper::getConfigData('arbitrary_name');
        $this->assertSame($expected, $result);

        // Reset the value after the test.
        Helper::setConfigData('arbitrary_name', $original, true);
    }

    /**
     * Test the getConfigData() and setConfigData() method when used in a non-PHPCS 4.x compatible manner.
     *
     * @return void
     */
    public function testConfigDataPHPCS4Exception()
    {
        if (\version_compare(Helper::getVersion(), '3.99.99', '<=') === true) {
            $this->markTestSkipped('Test only applicable to PHPCS 4.x');
        }

        $this->expectException('PHP_CodeSniffer\Exceptions\RuntimeException');
        $this->expectExceptionMessage('Passing the $config parameter is required in PHPCS 4.x');

        Helper::setConfigData('arbitrary_name', 'test', true);
    }
}
