<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests;

/**
 * PHPUnit cross-version compatibility helper.
 *
 * Provides a work-around for testing arguments passed to a method in a mocked object.
 * Previously, the `InvocationMocker->withConsecutive()` method could be used to test
 * this, but that method was removed in PHPUnit 10.0.
 *
 * @since 1.0.7
 */
trait ExpectWithConsecutiveArgs
{

    /**
     * PHPUnit cross-version helper method to test the arguments passed to a method in a mocked object.
     *
     * @param object $mockObject   The object mock.
     * @param object $countMatcher Matcher for number of time the method is expected to be called.
     * @param string $methodName   The name of the method on which to set the expectations.
     * @param array  $expectedArgs Multi-dimentional array of arguments expected to be passed in consecutive calls.
     *
     * @return object Expectation object.
     */
    final public function setExpectationWithConsecutiveArgs($mockObject, $countMatcher, $methodName, $expectedArgs)
    {
        $methodExpectation = $mockObject->expects($countMatcher)
            ->method($methodName);

        if (\method_exists($methodExpectation, 'withConsecutive')) {
            // PHPUnit 4.x - 9.x.

            $expectationsArray = [];
            foreach ($expectedArgs as $key => $series) {
                foreach ($series as $arg) {
                    $expectationsArray[$key][] = $this->identicalTo($arg);
                }
            }

            return \call_user_func_array([$methodExpectation, 'withConsecutive'], $expectationsArray);
        }

        // PHPUnit 10+.
        return $methodExpectation->willReturnCallback(
            function () use (&$expectedArgs, $countMatcher, $methodName) {
                $actualArgs = \func_get_args();
                $expected   = \array_shift($expectedArgs);

                $this->assertCount(
                    \count($expected),
                    $actualArgs,
                    \sprintf(
                        'Actual number of arguments received does not match expected for call %d to method %s',
                        $countMatcher->numberOfInvocations(),
                        $methodName
                    )
                );

                $this->assertSame(
                    $expected,
                    $actualArgs,
                    \sprintf(
                        'Arguments received do not match expected arguments for call %d to method %s',
                        $countMatcher->numberOfInvocations(),
                        $methodName
                    )
                );
            }
        );
    }
}
