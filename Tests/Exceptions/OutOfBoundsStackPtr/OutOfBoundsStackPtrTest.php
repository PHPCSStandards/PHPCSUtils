<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Exceptions\OutOfBoundsStackPtr;

use PHPCSUtils\Exceptions\OutOfBoundsStackPtr;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Exceptions\OutOfBoundsStackPtr
 *
 * @since 1.1.0
 */
final class OutOfBoundsStackPtrTest extends TestCase
{

    /**
     * Test that the text of the exception is as expected.
     *
     * @return void
     */
    public function testCreateForScalar()
    {
        $message = \sprintf(
            '%s(): Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object, 100000 given',
            __METHOD__
        );

        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage($message);

        throw OutOfBoundsStackPtr::create(2, '$stackPtr', 100000);
    }

    /**
     * Test that the text of the exception is as expected.
     *
     * @return void
     */
    public function testCreateForNonScalar()
    {
        $message = \sprintf(
            '%s(): Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object, array given',
            __METHOD__
        );

        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage($message);

        throw OutOfBoundsStackPtr::create(2, '$stackPtr', []);
    }
}
