<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\ContextTracking\DeclaredFunctionsTracker;

use PHPCSUtils\ContextTracking\DeclaredFunctionsTracker;
use PHPCSUtils\Tests\PolyfilledTestCase;

/**
 * Test for the \PHPCSUtils\ContextTracking\DeclaredFunctionsTracker class.
 *
 * @covers \PHPCSUtils\ContextTracking\DeclaredFunctionsTracker
 *
 * @since 1.1.0
 */
final class ExceptionsTest extends PolyfilledTestCase
{

    /**
     * Ensure the singleton pattern is being enforced.
     *
     * @return void
     */
    public function testSingletonPatternIsEnforced()
    {
        $message = 'Call to private PHPCSUtils\\ContextTracking\\DeclaredFunctionsTracker::__construct()';
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

        new DeclaredFunctionsTracker();
    }

    /**
     * Test receiving an expected exception when a non-string is passed as the function name.
     *
     * @return void
     */
    public function testFindInFileNonStringFunctionName()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #2 ($fqnFunctionName) must be of type string, integer given');

        $tracker = DeclaredFunctionsTracker::getInstance();
        $tracker->findInFile(self::$phpcsFile, 10);
    }

    /**
     * Test receiving an expected exception when an empty string is passed as the function name.
     *
     * @return void
     */
    public function testFindInFileEmptyStringFunctionName()
    {
        $this->expectException('PHPCSUtils\Exceptions\ValueError');
        $this->expectExceptionMessage('The value of argument #2 ($fqnFunctionName) must be a non-empty string');

        $tracker = DeclaredFunctionsTracker::getInstance();
        $tracker->findInFile(self::$phpcsFile, '');
    }

    /**
     * Test receiving an expected exception when a non-fully qualified function name is passed.
     *
     * @return void
     */
    public function testFindInFileNonFullyQualifiedFunctionName()
    {
        $this->expectException('PHPCSUtils\Exceptions\ValueError');
        $this->expectExceptionMessage(
            'The value of argument #2 ($fqnFunctionName) must be a fully qualified function name; received unqualified name'
        );

        $tracker = DeclaredFunctionsTracker::getInstance();
        $tracker->findInFile(self::$phpcsFile, 'doSomething');
    }
}
