<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\ContextTracking\ImportUseTracker;

use PHPCSUtils\ContextTracking\ImportUseTracker;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests the exceptions thrown for the \PHPCSUtils\ContextTracking\ImportUseTracker class.
 *
 * @covers \PHPCSUtils\ContextTracking\ImportUseTracker
 *
 * @since 1.1.0
 */
final class ExceptionsTest extends UtilityMethodTestCase
{

    /**
     * Ensure the singleton pattern is being enforced.
     *
     * @return void
     */
    public function testSingletonPatternIsEnforced()
    {
        $message = 'Call to private PHPCSUtils\\ContextTracking\\ImportUseTracker::__construct()';
        if (\method_exists($this, 'expectError')) {
            // PHP 5.4 + 5.5 with PHPUnit Polyfills 1.x.
            $this->expectError();
            $this->expectErrorMessage($message);
        } elseif (\PHP_VERSION_ID >= 70000) {
            // PHP 7.0+
            $this->expectException('\Error');
            $this->expectExceptionMessage($message);
        } else {
            // PHP 5.6 with PHPUnit 5.2+ and PHPUnit Polyfills 2.x.
            $this->expectException('\PHPUnit_Framework_Error');
            $this->expectExceptionMessage($message);
        }

        new ImportUseTracker();
    }

    /**
     * Test receiving an expected exception when the use statements for a non-existent arbitrary token are requested.
     *
     * @return void
     */
    public function testGetUseStatementsInfoNonExistentToken()
    {
        $this->expectPhpcsException('$stackPtr does not exist in the current file');

        $tracker = ImportUseTracker::getInstance();
        $tracker->track(self::$phpcsFile, 0);

        $tracker->getUseStatementsInfo(self::$phpcsFile, 10000);
    }

    /**
     * Test receiving an expected exception when the use statements for a non-existent arbitrary token are requested.
     *
     * @return void
     */
    public function testGetUseStatementsNonExistentToken()
    {
        $this->expectPhpcsException('$stackPtr does not exist in the current file');

        $tracker = ImportUseTracker::getInstance();
        $tracker->track(self::$phpcsFile, 0);

        $tracker->getUseStatements(self::$phpcsFile, 10000);
    }
}
