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
 * Tests for the \PHPCSUtils\TestUtils\UtilityMethodTestCase::getTargetToken() method.
 *
 * @covers \PHPCSUtils\TestUtils\UtilityMethodTestCase::getTargetToken
 *
 * @since 1.0.0
 */
final class GetTargetTokenFileNotFoundTest extends PolyfilledTestCase
{

    /**
     * Overload the "normal" set up to prevent a test case file from being tokenized.
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
     * Test the behaviour of the getTargetToken() method when the test case file has not been tokenized.
     *
     * @return void
     */
    public function testGetTargetTokenFileNotFound()
    {
        $this->expectException('PHPCSUtils\Exceptions\TestFileNotFound');
        $this->expectExceptionMessage(
            'Failed to find a tokenized test case file.' . \PHP_EOL
            . 'Make sure the UtilityMethodTestCase::setUpTestFile() method has run'
        );

        $this->getTargetToken('/* testSomething */', [\T_VARIABLE], '$a');
    }
}
