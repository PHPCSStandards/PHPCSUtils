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
final class InvalidFirstNamespaceNotAtStartWithImportsTest extends ImportUseTrackerTestCase
{

    /**
     * Use statements array as expected after all import use statements in the first (global) namespace in the test case file.
     *
     * {@internal Should be a class constant, but can't be until minimum PHP is 5.6+.}
     *
     * @var array<string, array<string, string>>
     */
    private static $statementsNoNSFull = [
        'name'     => [
            'ClassName' => 'Package\ClassName',
        ],
        'function' => [],
        'const'    => [],
    ];

    /**
     * Use statements array as expected after all import use statements in the second (named) namespace in the test case file.
     *
     * {@internal Should be a class constant, but can't be until minimum PHP is 5.6+.}
     *
     * @var array<string, array<string, string>>
     */
    private static $statementsNSFull = [
        'name'     => [
            'Thing' => 'Another\Some\Thing',
        ],
        'function' => [
            'do_something' => 'Another\do_something',
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
            'No namespace: start of file' => [
                'marker'   => '/* testStartOfFile */',
                'target'   => \T_WHITESPACE,
                'expected' => [
                    'file'        => '',
                    'lastSeenPtr' => -1,
                    'seenInFile'  => [],
                ],
            ],
            'No namespace: first import use statement' => [
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
            'No namespace: in class after non-import T_USE token' => [
                'marker'   => '/* testInClassAfterFirstTrackingToken */',
                'target'   => \T_CLOSE_CURLY_BRACKET,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 22 : 24,
                    'seenInFile'  => [
                        0 => ($php8Names === true) ? [6, 22] : [6, 24],
                    ],
                ],
            ],
            'No namespace: namespace declaration' => [
                'marker'   => '/* testNamespaceDeclaration */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 36 : 38,
                    'seenInFile'  => [
                        0 => ($php8Names === true) ? [6, 22] : [6, 24],
                    ],
                ],
            ],
            'Namespaced: second import use statement' => [
                'marker'   => '/* testGroupImportUse */',
                'target'   => \T_USE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 44 : 50,
                    'seenInFile'  => [
                        0                               => ($php8Names === true) ? [6, 22] : [6, 24],
                        ($php8Names === true) ? 40 : 46 => ($php8Names === true) ? [44] : [50],
                    ],
                ],
            ],
            'Namespaced: in closure after non-import T_USE token' => [
                'marker'   => '/* testInClosureAfterFirstTrackingToken */',
                'target'   => \T_RETURN,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 76 : 84,
                    'seenInFile'  => [
                        0                               => ($php8Names === true) ? [6, 22] : [6, 24],
                        ($php8Names === true) ? 40 : 46 => ($php8Names === true) ? [44, 76] : [50, 84],
                    ],
                ],
            ],
            'Namespaced: after scoped' => [
                'marker'   => '/* testAfterScoped */',
                'target'   => \T_ECHO,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 76 : 84,
                    'seenInFile'  => [
                        0                               => ($php8Names === true) ? [6, 22] : [6, 24],
                        ($php8Names === true) ? 40 : 46 => ($php8Names === true) ? [44, 76] : [50, 84],
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
        $data      = [
            'No namespace | No use statements: start of file' => [
                'marker'   => '/* testStartOfFile */',
                'target'   => \T_WHITESPACE,
                'expected' => parent::$noStatementsInfoArray,
            ],
            'No namespace | No use statements: in first import use statement' => [
                'marker'   => '/* testSingleImportUse */',
                'target'   => ($php8Names === true) ? \T_NAME_QUALIFIED : \T_NS_SEPARATOR,
                'expected' => parent::$noStatementsInfoArray,
            ],
        ];

        // First import use statement seen in the global namespace.
        $expected                     = parent::$noStatementsInfoArray;
        $expected[0]['lastPtr']       = 6;
        $expected[0]['statements']    = self::$statementsNoNSFull;
        $expected[0]['effectiveFrom'] = ($php8Names === true) ? 10 : 12;

        $data['No namespace | Full set use statements: trait use'] = [
            'marker'   => '/* testInClassBeforeFirstTrackingToken */',
            'target'   => \T_USE,
            'expected' => $expected,
        ];

        // Start of namespace declaration seen.
        $expected[0]['lastPtr'] = ($php8Names === true) ? 22 : 24;

        $data['No namespace | No use statements: namespace declaration (parse error)'] = [
            'marker'   => '/* testNamespaceDeclaration */',
            'target'   => ($php8Names === true) ? \T_NAME_QUALIFIED : \T_STRING,
            // This expectation that the namespace declaration statement will have the use statements of the
            // unnamed global namespace may be unexpected, but I see no need to add a work-around
            // when this is a parse error anyway.
            'expected' => $expected,
        ];

        // Named namespace seen.
        $expected = [
            ($php8Names === true) ? 40 : 46 => parent::$noStatementsInfoSubArray,
        ];

        $data['First namespace | No use statements: in import use statement'] = [
            'marker'   => '/* testGroupImportUse */',
            'target'   => \T_STRING,
            'expected' => $expected,
        ];

        // First import use statement seen in the named namespace.
        $expected[($php8Names === true) ? 40 : 46]['lastPtr']       = ($php8Names === true) ? 44 : 50;
        $expected[($php8Names === true) ? 40 : 46]['statements']    = self::$statementsNSFull;
        $expected[($php8Names === true) ? 40 : 46]['effectiveFrom'] = ($php8Names === true) ? 62 : 70;

        $data['First namespace | Full set use statements: in closure declaration'] = [
            'marker'   => '/* testClosureBeforeFirstTrackingToken */',
            'target'   => \T_CLOSE_PARENTHESIS,
            'expected' => $expected,
        ];

        // Closure use statement seen in the named namespace.
        $expected[($php8Names === true) ? 40 : 46]['lastPtr'] = ($php8Names === true) ? 76 : 84;

        $data['First namespace | Full set use statements: after scoped'] = [
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
            'No namespace | No use statements: end of first use statement' => [
                'marker'       => '/* testSingleImportUse */',
                'target'       => \T_SEMICOLON,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testStartOfFile */',
                'stopAtTarget' => \T_WHITESPACE,
            ],
            'No namespace | Full set use statements: trait use name, not yet tracked' => [
                'marker'       => '/* testInClassBeforeFirstTrackingToken */',
                'target'       => \T_STRING,
                'expected'     => self::$statementsNoNSFull,
                'stopAt'       => '/* testStartOfFile */',
                'stopAtTarget' => \T_WHITESPACE,
            ],
            'First namespace | No use statements: in import use statement, partially tracked' => [
                'marker'       => '/* testGroupImportUse */',
                'target'       => \T_STRING,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testInClassBeforeFirstTrackingToken */',
                'stopAtTarget' => \T_USE,
            ],
            'First namespace | Full set use statements: in closure, partially tracked' => [
                'marker'       => '/* testInClosureAfterFirstTrackingToken */',
                'target'       => \T_RETURN,
                'expected'     => self::$statementsNSFull,
                'stopAt'       => '/* testNamespaceDeclaration */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'First namespace | Full set use statements: in closure, tracked' => [
                'marker'       => '/* testInClosureAfterFirstTrackingToken */',
                'target'       => \T_MULTIPLY,
                'expected'     => self::$statementsNSFull,
                'stopAt'       => '/* testClosureBeforeFirstTrackingToken */',
                'stopAtTarget' => \T_CLOSURE,
            ],
            'Full set use statements: after scoped; partially tracked' => [
                'marker'       => '/* testAfterScoped */',
                'target'       => \T_ECHO,
                'expected'     => self::$statementsNSFull,
                'stopAt'       => '/* testInClassBeforeFirstTrackingToken */',
                'stopAtTarget' => \T_USE,
            ],
        ];
    }
}
