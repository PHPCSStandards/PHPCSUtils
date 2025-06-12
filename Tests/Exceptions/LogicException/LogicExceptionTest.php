<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Exceptions\LogicException;

use PHPCSUtils\Exceptions\LogicException;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Exceptions\LogicException
 *
 * @since 1.1.0
 */
final class LogicExceptionTest extends TestCase
{

    /**
     * Test that the text of the exception is as expected.
     *
     * @return void
     */
    public function testCreate()
    {
        $message = \sprintf(
            '%s(): your message',
            __METHOD__
        );

        $this->expectException('PHPCSUtils\Exceptions\LogicException');
        $this->expectExceptionMessage($message);

        throw LogicException::create('your message');
    }
}
