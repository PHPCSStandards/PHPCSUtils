<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Exceptions\InvalidTokenArray;

use PHPCSUtils\Exceptions\InvalidTokenArray;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Exceptions\InvalidTokenArray
 *
 * @since 1.0.0
 */
final class InvalidTokenArrayTest extends TestCase
{

    /**
     * Test that the text of the exception is as expected.
     *
     * @return void
     */
    public function testCreate()
    {
        $this->expectException('PHPCSUtils\Exceptions\InvalidTokenArray');
        $this->expectExceptionMessage(\sprintf('Call to undefined method %s::dummy()', __CLASS__));

        throw InvalidTokenArray::create('dummy');
    }
}
