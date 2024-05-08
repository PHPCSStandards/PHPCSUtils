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

use PHPCSUtils\Tests\ContextTracking\ImportUseTracker\ImportUseTrackerTestCase;

/**
 * Testcase for the \PHPCSUtils\ContextTracking\ImportUseTracker class.
 *
 * @covers \PHPCSUtils\ContextTracking\ImportUseTracker
 *
 * @since 1.1.0
 */
final class SingleUnscopedNamespaceNoImportsTest extends ImportUseTrackerTestCase
{

    /**
     * Data provider.
     *
     * @see ImportUseTrackerTestCase::testTrackSetsProperties() For the array format.
     *
     * @return array<string, array<string, string|int|array<string, string|int|array<int, array<int>>>>>
     */
    public static function dataTrackSetsProperties()
    {
        $fileName  = \str_replace('.php', '.inc', __FILE__);
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'No namespace: start of file' => [
                'marker'   => '/* testStartOfFile */',
                'target'   => \T_WHITESPACE,
                'expected' => [
                    'file'        => '',
                    'lastSeenPtr' => -1,
                    'seenInFile'  => [],
                ],
            ],
            'No namespace: namespace declaration' => [
                'marker'   => '/* testNamespaceDeclaration */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => 6,
                    'seenInFile'  => [],
                ],
            ],
            'No namespace: in class after non-import T_USE token' => [
                'marker'   => '/* testInClassAfterFirstTrackingToken */',
                'target'   => \T_CLOSE_CURLY_BRACKET,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 22 : 26,
                    'seenInFile'  => [
                        ($php8Names === true) ? 10 : 14 => ($php8Names === true) ? [22] : [26],
                    ],
                ],
            ],
            'No namespace: in closure after non-import T_USE token' => [
                'marker'   => '/* testInClosureAfterFirstTrackingToken */',
                'target'   => \T_RETURN,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 45 : 49,
                    'seenInFile'  => [
                        ($php8Names === true) ? 10 : 14 => ($php8Names === true) ? [22, 45] : [26, 49],
                    ],
                ],
            ],
            'No namespace: after scoped' => [
                'marker'   => '/* testAfterScoped */',
                'target'   => \T_ECHO,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 45 : 49,
                    'seenInFile'  => [
                        ($php8Names === true) ? 10 : 14 => ($php8Names === true) ? [22, 45] : [26, 49],
                    ],
                ],
            ],
        ];
    }

    /**
     * Data provider.
     *
     * @see ImportUseTrackerTestCase::testGetUseStatementsForTokenBeforeLastSeen() For the array format.
     * @see ImportUseTrackerTestCase::testGetUseStatementsForCurrentToken()        For the array format.
     *
     * @return array<string, array<string, string|int|array<int, array<string, int|array<string, array<string, string>>|null>>>>
     */
    public static function dataGetUseStatements()
    {
        $php8Names = parent::usesPhp8NameTokens();
        $expected  = [
            ($php8Names === true) ? 10 : 14 => parent::$noStatementsInfoSubArray,
        ];
        $data      = [
            'No use statements: start of file' => [
                'marker'   => '/* testStartOfFile */',
                'target'   => \T_WHITESPACE,
                'expected' => parent::$noStatementsInfoArray,
            ],
            'No use statements: in class before use tracking token in class' => [
                'marker'   => '/* testInClassBeforeFirstTrackingToken */',
                'target'   => \T_WHITESPACE,
                'expected' => $expected,
            ],
        ];

        // Trait use statement seen.
        $expected[($php8Names === true) ? 10 : 14]['lastPtr'] = ($php8Names === true) ? 22 : 26;

        $data['No use statements: in class after use tracking token in class'] = [
            'marker'   => '/* testInClassAfterFirstTrackingToken */',
            'target'   => \T_WHITESPACE,
            'expected' => $expected,
        ];

        $data['No use statements: closure before use tracking token'] = [
            'marker'   => '/* testClosureBeforeFirstTrackingToken */',
            'target'   => \T_VARIABLE,
            'expected' => $expected,
        ];

        // Closure use statement seen.
        $expected[($php8Names === true) ? 10 : 14]['lastPtr'] = ($php8Names === true) ? 45 : 49;

        $data['No use statements: in closure after use tracking token'] = [
            'marker'   => '/* testInClosureAfterFirstTrackingToken */',
            'target'   => \T_RETURN,
            'expected' => $expected,
        ];

        $data['No use statements: after scoped'] = [
            'marker'   => '/* testAfterScoped */',
            'target'   => \T_ECHO,
            'expected' => $expected,
        ];

        return $data;
    }

    /**
     * Data provider.
     *
     * @see ImportUseTrackerTestCase::testGetNamespaceArbitraryToken() For the array format.
     *
     * @return array<string, array<string, string|int|array<string, array<string, string>>>>
     */
    public static function dataGetUseStatementsArbitraryToken()
    {
        return [
            'No use statements: start of file' => [
                'marker'       => '/* testStartOfFile */',
                'target'       => \T_WHITESPACE,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testStartOfFile */',
                'stopAtTarget' => \T_WHITESPACE,
            ],
            'No use statements: in class before first tracking token in class; class not yet tracked' => [
                'marker'       => '/* testInClassBeforeFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testNamespaceDeclaration */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'No use statements: in class after first tracking token in class; class tracked' => [
                'marker'       => '/* testInClassAfterFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testNamespaceDeclaration */',
                'stopAtTarget' => \T_CLASS,
            ],
            'No use statements: in closure before first tracking token; closure not yet tracked' => [
                'marker'       => '/* testClosureBeforeFirstTrackingToken */',
                'target'       => \T_VARIABLE,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testInClassAfterFirstTrackingToken */',
                'stopAtTarget' => \T_WHITESPACE,
            ],
            'No use statements: in closure before first tracking token; closure tracked' => [
                'marker'       => '/* testClosureBeforeFirstTrackingToken */',
                'target'       => \T_OPEN_PARENTHESIS,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testClosureBeforeFirstTrackingToken */',
                'stopAtTarget' => \T_CLOSURE,
            ],
            'No use statements: in closure after first tracking token; closure not yet tracked' => [
                'marker'       => '/* testInClosureAfterFirstTrackingToken */',
                'target'       => \T_RETURN,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testInClassBeforeFirstTrackingToken */',
                'stopAtTarget' => \T_USE,
            ],
            'No use statements: in closure after first tracking token; closure tracked' => [
                'marker'       => '/* testInClosureAfterFirstTrackingToken */',
                'target'       => \T_RETURN,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testAfterScoped */',
                'stopAtTarget' => \T_ECHO,
            ],
            'No use statements: after scoped; not yet tracked' => [
                'marker'       => '/* testAfterScoped */',
                'target'       => \T_ECHO,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testStartOfFile */',
                'stopAtTarget' => \T_WHITESPACE,
            ],
            'No use statements: after scoped; partially tracked' => [
                'marker'       => '/* testAfterScoped */',
                'target'       => \T_ECHO,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testInClosureAfterFirstTrackingToken */',
                'stopAtTarget' => \T_RETURN,
            ],
        ];
    }
}
