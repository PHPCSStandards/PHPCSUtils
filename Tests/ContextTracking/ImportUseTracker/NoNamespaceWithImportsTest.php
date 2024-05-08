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
final class NoNamespaceWithImportsTest extends ImportUseTrackerTestCase
{

    /**
     * Use statements array as expected after the first import use statement in the test case file.
     *
     * {@internal Should be a class constant, but can't be until minimum PHP is 5.6+.}
     *
     * @var array<string, array<string, string>>
     */
    private static $statementsFirst = [
        'name'     => [],
        'function' => [
            'myFunction' => 'Package\myFunction',
        ],
        'const'    => [],
    ];

    /**
     * Use statements array as expected after all import use statements in the test case file.
     *
     * {@internal Should be a class constant, but can't be until minimum PHP is 5.6+.}
     *
     * @var array<string, array<string, string>>
     */
    private static $statementsFull = [
        'name'     => [
            'Package' => 'Another\Package',
            'Thing'   => 'Another\Some\Thing',
        ],
        'function' => [
            'myFunction' => 'Package\myFunction',
        ],
        'const'    => [],
    ];

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
            'No use keywords seen: start of file' => [
                'marker'   => '/* testStartOfFile */',
                'target'   => \T_WHITESPACE,
                'expected' => [
                    'file'        => '',
                    'lastSeenPtr' => -1,
                    'seenInFile'  => [],
                ],
            ],
            'Seen one: first import use statement' => [
                'marker'   => '/* testSingleImportUse */',
                'target'   => \T_USE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => 6,
                    'seenInFile'  => [
                        0 => [6],
                    ],
                ],
            ],
            'Seen two: second import use statement' => [
                'marker'   => '/* testGroupImportUse */',
                'target'   => \T_USE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 15 : 17,
                    'seenInFile'  => [
                        0 => ($php8Names === true) ? [6, 15] : [6, 17],
                    ],
                ],
            ],
            'Seen three: in class after non-import T_USE token' => [
                'marker'   => '/* testInClassAfterFirstTrackingToken */',
                'target'   => \T_CLOSE_CURLY_BRACKET,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 53 : 57,
                    'seenInFile'  => [
                        0 => ($php8Names === true) ? [6, 15, 53] : [6, 17, 57],
                    ],
                ],
            ],
            'Seen four: in closure after non-import T_USE token' => [
                'marker'   => '/* testInClosureAfterFirstTrackingToken */',
                'target'   => \T_RETURN,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 76 : 80,
                    'seenInFile'  => [
                        0 => ($php8Names === true) ? [6, 15, 53, 76] : [6, 17, 57, 80],
                    ],
                ],
            ],
            'Seen four: after scoped' => [
                'marker'   => '/* testAfterScoped */',
                'target'   => \T_ECHO,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 76 : 80,
                    'seenInFile'  => [
                        0 => ($php8Names === true) ? [6, 15, 53, 76] : [6, 17, 57, 80],
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
        $expected  = parent::$noStatementsInfoArray;
        $data      = [
            'No use statements: start of file' => [
                'marker'   => '/* testStartOfFile */',
                'target'   => \T_WHITESPACE,
                'expected' => $expected,
            ],
            'No use statements: first import use keyword' => [
                'marker'   => '/* testSingleImportUse */',
                'target'   => \T_USE,
                'expected' => $expected,
            ],
            'No use statements: in first import use statement' => [
                'marker'   => '/* testSingleImportUse */',
                'target'   => \T_STRING, // `function` keyword.
                'expected' => $expected,
            ],
        ];

        // First import use statement seen.
        $expected[0]['lastPtr']       = 6;
        $expected[0]['statements']    = self::$statementsFirst;
        $expected[0]['effectiveFrom'] = ($php8Names === true) ? 12 : 14;

        $data['Partial use statements: second import use keyword'] = [
            'marker'   => '/* testGroupImportUse */',
            'target'   => \T_USE,
            'expected' => $expected,
        ];

        $data['Partial use statements: in second import use statement'] = [
            'marker'   => '/* testGroupImportUse */',
            'target'   => \T_COMMA,
            'expected' => $expected,
        ];

        // Second import use statement seen.
        $expected[0]['lastPtr']       = ($php8Names === true) ? 15 : 17;
        $expected[0]['statements']    = self::$statementsFull;
        $expected[0]['effectiveFrom'] = ($php8Names === true) ? 31 : 35;

        $data['Full set use statements: in class before trait import use'] = [
            'marker'   => '/* testInClassBeforeFirstTrackingToken */',
            'target'   => \T_WHITESPACE,
            'expected' => $expected,
        ];

        // Trait use statement seen.
        $expected[0]['lastPtr'] = ($php8Names === true) ? 53 : 57;

        $data['Full set use statements: in class trait import use'] = [
            'marker'   => '/* testInClassBeforeFirstTrackingToken */',
            'target'   => \T_STRING,
            'expected' => $expected,
        ];

        $data['Full set use statements: at closure'] = [
            'marker'   => '/* testClosureBeforeFirstTrackingToken */',
            'target'   => \T_CLOSURE,
            'expected' => $expected,
        ];

        // Closure use statement seen.
        $expected[0]['lastPtr'] = ($php8Names === true) ? 76 : 80;

        $data['Full set use statements: after scoped'] = [
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
            'Partial use statements: in group use statement, not yet tracked' => [
                'marker'       => '/* testGroupImportUse */',
                'target'       => \T_COMMA,
                'expected'     => self::$statementsFirst,
                'stopAt'       => '/* testStartOfFile */',
                'stopAtTarget' => \T_WHITESPACE,
            ],
            'Partial use statements: in group use statement, partially tracked' => [
                'marker'       => '/* testGroupImportUse */',
                'target'       => \T_COMMA,
                'expected'     => self::$statementsFirst,
                'stopAt'       => '/* testSingleImportUse */',
                'stopAtTarget' => \T_STRING,
            ],
            'Full set use statements: in class before first tracking token in class; class not yet tracked' => [
                'marker'       => '/* testInClassBeforeFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => self::$statementsFull,
                'stopAt'       => '/* testSingleImportUse */',
                'stopAtTarget' => \T_STRING,
            ],
            'Full set use statements: in class after first tracking token in class; class not yet tracked' => [
                'marker'       => '/* testInClassAfterFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => self::$statementsFull,
                'stopAt'       => '/* testGroupImportUse */',
                'stopAtTarget' => \T_DOC_COMMENT_OPEN_TAG,
            ],
            'Full set use statements: in class after first tracking token in class; class tracked' => [
                'marker'       => '/* testInClassAfterFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => self::$statementsFull,
                'stopAt'       => '/* testGroupImportUse */',
                'stopAtTarget' => \T_CLASS,
            ],
            'Full set use statements: after scoped; not yet tracked' => [
                'marker'       => '/* testAfterScoped */',
                'target'       => \T_ECHO,
                'expected'     => self::$statementsFull,
                'stopAt'       => '/* testStartOfFile */',
                'stopAtTarget' => \T_WHITESPACE,
            ],
            'Full set use statements: after scoped; partially tracked' => [
                'marker'       => '/* testAfterScoped */',
                'target'       => \T_ECHO,
                'expected'     => self::$statementsFull,
                'stopAt'       => '/* testInClassBeforeFirstTrackingToken */',
                'stopAtTarget' => \T_USE,
            ],
        ];
    }
}
