<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Exceptions\MissingArgumentError;

use PHPCSUtils\Exceptions\MissingArgumentError;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Exceptions\MissingArgumentError
 *
 * @since 1.1.0
 */
final class MissingArgumentErrorTest extends TestCase
{

    /**
     * Test that the text of the exception is as expected.
     *
     * @return void
     */
    public function testCreate()
    {
        $message = \sprintf(
            '%s(): Argument #2 ($config) is required for PHPCS 4.x',
            __METHOD__
        );

        $this->expectException('PHPCSUtils\Exceptions\MissingArgumentError');
        $this->expectExceptionMessage($message);

        throw MissingArgumentError::create(2, '$config', 'for PHPCS 4.x');
    }
}
