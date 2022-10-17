<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Exceptions\TestTargetNotFound;

use PHPCSUtils\Exceptions\TestTargetNotFound;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Exceptions\TestTargetNotFound
 *
 * @since 1.0.0
 */
final class TestTargetNotFoundTest extends TestCase
{

    /**
     * Test that the text of the exception is as expected.
     *
     * @return void
     */
    public function testCreateWithoutContent()
    {
        $this->expectException('PHPCSUtils\Exceptions\TestTargetNotFound');
        $this->expectExceptionMessage(
            'Failed to find test target token for comment string: /* testDummy */ in test case file: filename.inc'
        );

        throw TestTargetNotFound::create('/* testDummy */', null, 'filename.inc');
    }

    /**
     * Test that the text of the exception is as expected.
     *
     * @return void
     */
    public function testCreateWithContent()
    {
        $this->expectException('PHPCSUtils\Exceptions\TestTargetNotFound');
        $this->expectExceptionMessage(
            'Failed to find test target token for comment string: /* testDummy */'
            . ' with token content: foo in test case file: filename.inc'
        );

        throw TestTargetNotFound::create('/* testDummy */', 'foo', 'filename.inc');
    }
}
