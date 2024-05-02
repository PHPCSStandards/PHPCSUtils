<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\ContextTracking\NamespaceTracker;

use PHPCSUtils\Tests\ContextTracking\NamespaceTracker\NamespaceTrackerTestCase;

/**
 * Tests for the \PHPCSUtils\ContextTracking\NamespaceTracker class.
 *
 * @covers \PHPCSUtils\ContextTracking\NamespaceTracker
 *
 * @since 1.1.0
 */
final class NoNamespaceTest extends NamespaceTrackerTestCase
{

    /**
     * Helper function defining the "seenInFile" array.
     *
     * @return array<int, array<string, int|string|null>>
     */
    protected static function getSeenInFile()
    {
        return [
            0 => [
                'start' => 0,
                'end'   => null,
                'name'  => '',
            ],
        ];
    }

    /**
     * Data provider.
     *
     * @see NamespaceTrackerTestCase::testTrackSetsProperties() For the array format.
     *
     * @return array<string, array<string, string|int|array<string, string|int|array<int, array<string, int|string|null>>>>>
     */
    public static function dataTrackSetsProperties()
    {
        $fileName   = \str_replace('.php', '.inc', __FILE__);
        $php8Names  = parent::usesPhp8NameTokens();
        $seenInFile = self::getSeenInFile();

        return [
            'No namespace: start of file' => [
                'marker'   => '/* testStartOfFile */',
                'target'   => \T_WHITESPACE,
                'expected' => [
                    'file'         => '',
                    'lastSeenPtr'  => -1,
                    'currentNSPtr' => 0,
                    'seenInFile'   => $seenInFile,
                ],
            ],
            'No namespace: in class after first tracking token' => [
                'marker'   => '/* testInClassAfterFirstTrackingToken */',
                'target'   => \T_WHITESPACE,
                'expected' => [
                    'file'         => ($php8Names === true) ? '' : $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? -1 : 33,
                    'currentNSPtr' => 0,
                    'seenInFile'   => $seenInFile,
                ],
            ],
            'No namespace: after scoped' => [
                'marker'   => '/* testAfterScoped */',
                'target'   => \T_WHITESPACE,
                'expected' => [
                    'file'         => ($php8Names === true) ? '' : $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? -1 : 67,
                    'currentNSPtr' => 0,
                    'seenInFile'   => $seenInFile,
                ],
            ],
        ];
    }

    /**
     * Data provider.
     *
     * @see NamespaceTrackerTestCase::testGetNamespaceForTokenBeforeLastSeen() For the array format.
     * @see NamespaceTrackerTestCase::testGetNamespaceForCurrentToken()        For the array format.
     *
     * @return array<string, array<string, int|string|array<string, int|string|null>>>
     */
    public static function dataGetNamespace()
    {
        $php8Names = parent::usesPhp8NameTokens();
        $expected  = self::getSeenInFile()[0];

        return [
            'No namespace: start of file' => [
                'marker'   => '/* testStartOfFile */',
                'target'   => \T_WHITESPACE,
                'expected' => $expected,
            ],
            'No namespace: in class before first tracking token in class' => [
                'marker'   => '/* testInClassBeforeFirstTrackingToken */',
                'target'   => \T_WHITESPACE,
                'expected' => $expected,
            ],
            'No namespace: in class namespace operator' => [
                'marker'   => '/* testNamespaceOperatorInClass */',
                'target'   => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected' => $expected,
            ],
            'No namespace: in class after first tracking token in class' => [
                'marker'   => '/* testInClassAfterFirstTrackingToken */',
                'target'   => \T_WHITESPACE,
                'expected' => $expected,
            ],
            'No namespace: in function before first tracking token in function' => [
                'marker'   => '/* testInFunctionBeforeFirstTrackingToken */',
                'target'   => \T_VARIABLE,
                'expected' => $expected,
            ],
            'No namespace: in function namespace operator' => [
                'marker'   => '/* testInFunctionBeforeFirstTrackingToken */',
                'target'   => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected' => $expected,
            ],
            'No namespace: in function after first tracking token in function' => [
                'marker'   => '/* testInFunctionAfterFirstTrackingToken */',
                'target'   => \T_OPEN_SHORT_ARRAY,
                'expected' => $expected,
            ],
            'No namespace: after scoped' => [
                'marker'   => '/* testAfterScoped */',
                'target'   => \T_ECHO,
                'expected' => $expected,
            ],
        ];
    }

    /**
     * Data provider.
     *
     * @see NamespaceTrackerTestCase::testGetNamespaceArbitraryToken() For the array format.
     *
     * @return array<string, array<string, string|int>>
     */
    public static function dataGetNamespaceArbitraryToken()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'No namespace: start of file' => [
                'marker'       => '/* testStartOfFile */',
                'target'       => \T_WHITESPACE,
                'expected'     => '',
                'stopAt'       => '/* testStartOfFile */',
                'stopAtTarget' => \T_WHITESPACE,
            ],
            'No namespace: in class before first tracking token in class; class not yet tracked' => [
                'marker'       => '/* testInClassBeforeFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => '',
                'stopAt'       => '/* testStartOfFile */',
                'stopAtTarget' => \T_WHITESPACE,
            ],
            'No namespace: in class before first tracking token in class; class tracked' => [
                'marker'       => '/* testInClassBeforeFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => '',
                'stopAt'       => '/* testNamespaceOperatorInClass */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'No namespace: in class after first tracking token in class; class not yet tracked' => [
                'marker'       => '/* testInClassAfterFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => '',
                'stopAt'       => '/* testStartOfFile */',
                'stopAtTarget' => \T_WHITESPACE,
            ],
            'No namespace: in class after first tracking token in class; class tracked' => [
                'marker'       => '/* testInClassAfterFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => '',
                'stopAt'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'No namespace: in function before first tracking token in function; function not yet tracked' => [
                'marker'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'target'       => \T_VARIABLE,
                'expected'     => '',
                'stopAt'       => '/* testNamespaceOperatorInClass */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'No namespace: in function before first tracking token in function; function tracked' => [
                'marker'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'target'       => \T_VARIABLE,
                'expected'     => '',
                'stopAt'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'No namespace: in function namespace operator; function tracked' => [
                'marker'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'target'       => \T_OPEN_PARENTHESIS,
                'expected'     => '',
                'stopAt'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'No namespace: in function after first tracking token in function; function not yet tracked' => [
                'marker'       => '/* testInFunctionAfterFirstTrackingToken */',
                'target'       => \T_RETURN,
                'expected'     => '',
                'stopAt'       => '/* testNamespaceOperatorInClass */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'No namespace: in function after first tracking token in function; function tracked' => [
                'marker'       => '/* testInFunctionAfterFirstTrackingToken */',
                'target'       => \T_RETURN,
                'expected'     => '',
                'stopAt'       => '/* testAfterScoped */',
                'stopAtTarget' => \T_ECHO,
            ],
            'No namespace: after scoped; not yet tracked' => [
                'marker'       => '/* testAfterScoped */',
                'target'       => \T_ECHO,
                'expected'     => '',
                'stopAt'       => '/* testStartOfFile */',
                'stopAtTarget' => \T_WHITESPACE,
            ],
        ];
    }
}
