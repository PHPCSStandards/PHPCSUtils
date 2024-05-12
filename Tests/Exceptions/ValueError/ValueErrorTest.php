<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Exceptions\ValueError;

use PHPCSUtils\Exceptions\ValueError;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Exceptions\ValueError
 *
 * @since 1.1.0
 */
final class ValueErrorTest extends TestCase
{

    /**
     * Test that the text of the exception is as expected.
     *
     * @return void
     */
    public function testCreate()
    {
        $message = \sprintf(
            '%s(): The value of argument #1 ($end) must be before $start',
            __METHOD__
        );

        $this->expectException('PHPCSUtils\Exceptions\ValueError');
        $this->expectExceptionMessage($message);

        throw ValueError::create(1, '$end', 'must be before $start');
    }
}
