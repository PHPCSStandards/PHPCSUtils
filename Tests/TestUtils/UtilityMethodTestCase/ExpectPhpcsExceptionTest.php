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

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Exceptions\TokenizerException;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\TestUtils\UtilityMethodTestCase class.
 *
 * @covers \PHPCSUtils\TestUtils\UtilityMethodTestCase::expectPhpcsException
 *
 * @since 1.0.0
 */
final class ExpectPhpcsExceptionTest extends UtilityMethodTestCase
{

    /**
     * Overload the "normal" "set up before class" to do nothing.
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
     * Overload the "normal" "set up" to do nothing.
     *
     * @before
     *
     * @return void
     */
    public function skipJSCSSTestsOnPHPCS4()
    {
        // Deliberately left empty.
    }

    /**
     * Overload the "normal" "tear down after class" to do nothing.
     *
     * @afterClass
     *
     * @return void
     */
    public static function resetTestFile()
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
     * Test that the helper method to handle cross-version testing of exceptions in PHPUnit
     * works correctly.
     *
     * @return void
     */
    public function testExpectPhpcsRuntimeException()
    {
        $this->expectPhpcsException('testing-1-2-3');
        throw new RuntimeException('testing-1-2-3');
    }

    /**
     * Test that the helper method to handle cross-version testing of exceptions in PHPUnit
     * works correctly.
     *
     * @return void
     */
    public function testExpectPhpcsTokenizerException()
    {
        $this->expectPhpcsException('testing-1-2-3', 'tokenizer');
        throw new TokenizerException('testing-1-2-3');
    }
}
