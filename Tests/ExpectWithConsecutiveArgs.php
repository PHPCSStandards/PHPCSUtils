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
 * Furthermore, the use of `->will($this->onConsecutiveCalls(...))` was deprecated in PHPUnit 10/11
 * and will be removed in PHPUnit 12.0. The typical replacement for this is `->willReturn(...)`.
 * However, as this helper already uses `->willReturnCallback()`, if these two deprecations/removals
 * collide in the same expectation setting, it would break, so this is now also worked around via the
 * `$returnValues` parameter.
 *
 * @since 1.0.7
 * @since 1.1.0 Now also works round the deprecation of `->will($this->onConsecutiveCalls(...))`
 *              via the new optional `$returnValues` parameter.
 */
trait ExpectWithConsecutiveArgs
{

    /**
     * PHPUnit cross-version helper method to test the arguments passed to a method in a mocked object.
     *
     * @param object              $mockObject   The object mock.
     * @param object              $countMatcher Matcher for number of time the method is expected to be called.
     * @param string              $methodName   The name of the method on which to set the expectations.
     * @param array<array<mixed>> $expectedArgs Multi-dimentional array of arguments expected to be passed in
     *                                          consecutive calls.
     * @param array<mixed>        $returnValues Optional. Array of values to return on consecutive calls.
     *
     * @return object Expectation object.
     */
    final public function setExpectationWithConsecutiveArgs(
        $mockObject,
        $countMatcher,
        $methodName,
        $expectedArgs,
        $returnValues = []
    ) {
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

            $methodExpectation = \call_user_func_array([$methodExpectation, 'withConsecutive'], $expectationsArray);

            if (empty($returnValues)) {
                return $methodExpectation;
            }

            return $methodExpectation->will(\call_user_func_array([$this, 'onConsecutiveCalls'], $returnValues));
        }

        // PHPUnit 10+.
        return $methodExpectation->willReturnCallback(
            function () use (&$expectedArgs, $countMatcher, $methodName, $returnValues) {
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

                if (empty($returnValues) === false) {
                    return $returnValues[($countMatcher->numberOfInvocations() - 1)];
                }
            }
        );
    }
}
