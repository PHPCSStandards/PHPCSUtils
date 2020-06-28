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

use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\TestUtils\UtilityMethodTestCase class.
 *
 * @covers \PHPCSUtils\TestUtils\UtilityMethodTestCase::skipJSCSSTestsOnPHPCS4
 *
 * @group testutils
 *
 * @since 1.0.0
 */
class SkipJSCSSTest extends UtilityMethodTestCase
{

    /**
     * The file extension of the test case file (without leading dot).
     *
     * @var string
     */
    protected static $fileExtension = 'js';

    /**
     * Overload the test skipping method.
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
     * Test that the skipJSCSSTestsOnPHPCS4() skips JS/CSS file tests on PHPCS 4.x.
     *
     * @doesNotPerformAssertions
     *
     * @return void
     */
    public function testSkipJsCss()
    {
        if (\version_compare(static::$phpcsVersion, '3.99.99', '>') === true) {
            $msg       = 'JS and CSS support has been removed in PHPCS 4.';
            $exception = 'PHPUnit\Framework\SkippedTestError';
            if (\class_exists('PHPUnit_Framework_SkippedTestError')) {
                // PHPUnit < 6.
                $exception = 'PHPUnit_Framework_SkippedTestError';
            }

            if (\method_exists($this, 'expectException')) {
                // PHPUnit 5+.
                $this->expectException($exception);
                $this->expectExceptionMessage($msg);
            } else {
                // PHPUnit 4.
                $this->setExpectedException($exception, $msg);
            }
        }

        parent::skipJSCSSTestsOnPHPCS4();
    }
}
