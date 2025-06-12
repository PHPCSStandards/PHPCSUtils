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
 * @covers \PHPCSUtils\TestUtils\UtilityMethodTestCase::setUpTestFile
 * @covers \PHPCSUtils\TestUtils\UtilityMethodTestCase::parseFile
 *
 * @since 1.0.0
 */
final class MissingCaseFileTest extends PolyfilledTestCase
{

    /**
     * Overload the "normal" set up.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        // Deliberately left empty.
    }

    /**
     * Overload the "normal" test marker QA check - this test class does not have a File object.
     *
     * @coversNothing
     * @doesNotPerformAssertions
     *
     * @return void
     */
    public function testTestMarkersAreUnique()
    {
        // Deliberately left empty.
    }

    /**
     * Test that the setUpTestFile() fails a test when the test case file is missing.
     *
     * @return void
     */
    public function testMissingCaseFile()
    {
        $msg       = 'Test case file missing. Expected case file location: ';
        $exception = 'PHPUnit\Framework\AssertionFailedError';
        if (\class_exists('PHPUnit_Framework_AssertionFailedError')) {
            // PHPUnit < 6.
            $exception = 'PHPUnit_Framework_AssertionFailedError';
        }

        $this->expectException($exception);
        $this->expectExceptionMessage($msg);

        parent::setUpTestFile();
    }
}
