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
 * @since 1.0.0
 */
final class GetVersionTest extends TestCase
{

    /**
     * Version number of the last PHPCS 3.x release.
     *
     * {@internal This should be updated regularly, but shouldn't cause issues if it isn't.}
     *
     * @var string
     */
    const LATEST_3X_VERSION = '3.13.5';

    /**
     * Version number of the last PHPCS 4.x release.
     *
     * {@internal This should be updated regularly, but shouldn't cause issues if it isn't.}
     *
     * @var string
     */
    const LATEST_4X_VERSION = '4.0.1';

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
            $expected = '3.13.5';
        }

        $result = Helper::getVersion();

        if ($expected === '3.x-dev') {
            $this->assertTrue(\version_compare(self::LATEST_3X_VERSION, $result, '<='));
        } elseif ($expected === '4.x-dev') {
            $this->assertTrue(\version_compare(self::LATEST_4X_VERSION, $result, '<='));
        } else {
            $this->assertSame($expected, $result);
        }
    }
}
