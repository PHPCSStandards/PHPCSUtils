<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Exceptions\TypeError;

use PHPCSUtils\Exceptions\TypeError;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Exceptions\TypeError
 *
 * @since 1.1.0
 */
final class TypeErrorTest extends TestCase
{

    /**
     * Test that the text of the exception is as expected.
     *
     * @return void
     */
    public function testCreate()
    {
        $message = \sprintf(
            '%s(): Argument #1 ($typeString) must be of type string, integer given',
            __METHOD__
        );

        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage($message);

        throw TypeError::create(1, '$typeString', 'string', 1);
    }
}
