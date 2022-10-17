<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Exceptions\TestMarkerNotFound;

use PHPCSUtils\Exceptions\TestMarkerNotFound;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Exceptions\TestMarkerNotFound
 *
 * @since 1.0.0
 */
final class TestMarkerNotFoundTest extends TestCase
{

    /**
     * Test that the text of the exception is as expected.
     *
     * @return void
     */
    public function testCreate()
    {
        $this->expectException('PHPCSUtils\Exceptions\TestMarkerNotFound');
        $this->expectExceptionMessage('Failed to find the test marker: /* testDummy */ in test case file filename.inc');

        throw TestMarkerNotFound::create('/* testDummy */', 'filename.inc');
    }
}
