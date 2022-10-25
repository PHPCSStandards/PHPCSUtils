<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Exceptions\TestFileNotFound;

use PHPCSUtils\Exceptions\TestFileNotFound;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Exceptions\TestFileNotFound
 *
 * @since 1.0.0
 */
final class TestFileNotFoundTest extends TestCase
{

    /**
     * Test that the text of the exception is as expected.
     *
     * @return void
     */
    public function testNoCustomMessage()
    {
        $this->expectException('PHPCSUtils\Exceptions\TestFileNotFound');
        $this->expectExceptionMessage(
            'Failed to find a tokenized test case file.' . \PHP_EOL
            . 'Make sure the UtilityMethodTestCase::setUpTestFile() method has run'
        );

        throw new TestFileNotFound();
    }

    /**
     * Test that a passed message overruled the default message.
     *
     * @return void
     */
    public function testWithCustomMessage()
    {
        $this->expectException('PHPCSUtils\Exceptions\TestFileNotFound');
        $this->expectExceptionMessage('foobar');

        throw new TestFileNotFound('foobar');
    }
}
