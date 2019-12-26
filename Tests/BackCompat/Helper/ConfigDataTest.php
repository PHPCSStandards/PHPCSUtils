<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\BackCompat\Helper;

use PHPCSUtils\BackCompat\Helper;
use PHPUnit\Framework\TestCase;

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

    /**
     * Test the getConfigData() and setConfigData() method.
     *
     * @return void
     */
    public function testConfigData()
    {
        $original = Helper::getConfigData('arbitrary_name');
        $expected = 'expected';

        $return = Helper::setConfigData('arbitrary_name', $expected, true);
        $this->assertTrue($return);

        $result = Helper::getConfigData('arbitrary_name');
        $this->assertSame($expected, $result);

        // Reset the value after the test.
        $return = Helper::setConfigData('arbitrary_name', $original, true);
    }
}
