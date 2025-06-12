<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\TestUtils\UtilityMethodTestCase;

use PHPCSUtils\Tests\PolyfilledTestCase;

/**
 * Tests for the \PHPCSUtils\TestUtils\UtilityMethodTestCase class.
 *
 * @covers \PHPCSUtils\TestUtils\UtilityMethodTestCase::testTestMarkersAreUnique
 * @covers \PHPCSUtils\TestUtils\UtilityMethodTestCase::assertTestMarkersAreUnique
 *
 * @since 1.1.0
 */
final class TestMarkersAreUniqueFailsTest extends PolyfilledTestCase
{

    /**
     * Overload the "normal" test marker QA check - this test class does not have a valid File object.
     *
     * @return void
     */
    public function testTestMarkersAreUnique()
    {
        $msg       = "Duplicate test markers found.\nFailed asserting that ";
        $exception = 'PHPUnit\Framework\AssertionFailedError';
        if (\class_exists('PHPUnit_Framework_AssertionFailedError')) {
            // PHPUnit < 6.
            $exception = 'PHPUnit_Framework_AssertionFailedError';
        }

        $this->expectException($exception);
        $this->expectExceptionMessage($msg);

        parent::testTestMarkersAreUnique();
    }
}
