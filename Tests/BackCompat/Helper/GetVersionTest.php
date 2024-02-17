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
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\BackCompat\Helper::getVersion
 *
 * @group helper
 *
 * @since 1.0.0
 */
final class GetVersionTest extends TestCase
{

    /**
     * Version number of the last PHPCS release.
     *
     * {@internal This should be updated regularly, but shouldn't cause issues if it isn't.}
     *
     * @var string
     */
    const DEVMASTER = '3.9.0';

    /**
     * Test the method.
     *
     * @return void
     */
    public function testGetVersion()
    {
        $expected = \getenv('PHPCS_VERSION');
        if ($expected === false) {
            $this->markTestSkipped('The test for the Helper::getVersion() method will only run'
                . ' if the PHPCS_VERSION environment variable is set, such as during a CI build'
                . ' or when this variable has been set in the PHPUnit configuration file.');
        }

        if ($expected === 'lowest') {
            $expected = '3.8.0';
        }

        $result = Helper::getVersion();

        if ($expected === 'dev-master') {
            $this->assertTrue(\version_compare(self::DEVMASTER, $result, '<='));
        } elseif ($expected === '4.0.x-dev@dev') {
            $this->assertTrue(\version_compare('4.0.0', $result, '=='));
        } else {
            $this->assertSame($expected, $result);
        }
    }
}
