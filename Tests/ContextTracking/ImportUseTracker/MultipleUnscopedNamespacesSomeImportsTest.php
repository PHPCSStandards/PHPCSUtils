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
final class MultipleUnscopedNamespacesSomeImportsTest extends ImportUseTrackerTestCase
{

    /**
     * Use statements array as expected after the first import use statement in the first namespace in the test case file.
     *
     * {@internal Should be a class constant, but can't be until minimum PHP is 5.6+.}
     *
     * @var array<string, array<string, string>>
     */
    private static $statementsFirstNSFirstUse = [
        'name'     => [
            'Package' => 'Grouping\Package',
            'Thing'   => 'Grouping\Some\Thing',
        ],
        'function' => [],
        'const'    => [],
    ];

    /**
     * Use statements array as expected after all import use statements in the first namespace in the test case file.
     *
     * {@internal Should be a class constant, but can't be until minimum PHP is 5.6+.}
     *
     * @var array<string, array<string, string>>
     */
    private static $statementsFirstNSFull = [
        'name'     => [
            'Package' => 'Grouping\Package',
            'Thing'   => 'Grouping\Some\Thing',
        ],
        'function' => [
            'get_version' => 'get_version',
        ],
        'const'    => [],
    ];

    /**
     * Use statements array as expected after all import use statements in the third namespace in the test case file.
     *
     * {@internal Should be a class constant, but can't be until minimum PHP is 5.6+.}
     *
     * @var array<string, array<string, string>>
     */
    private static $statementsThirdNSFull = [
        'name'     => [],
        'function' => [],
        'const'    => [
            'M_PI'                => 'M_PI',
            'PHP_ROUND_HALF_DOWN' => 'PHP_ROUND_HALF_DOWN',
            'NAN'                 => 'Package\NAN',
        ],
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
            'No namespace: namespace declaration A' => [
                'marker'   => '/* testNamespaceDeclarationA */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => 6,
                    'seenInFile'  => [],
                ],
            ],
            'First namespace: first import use statement' => [
                'marker'   => '/* testGroupImportUse */',
                'target'   => \T_USE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 14 : 18,
                    'seenInFile'  => [
                        ($php8Names === true) ? 10 : 14 => ($php8Names === true) ? [14] : [18],
                    ],
                ],
            ],
            'First namespace: second import use statement' => [
                'marker'   => '/* testSingleImportUse */',
                'target'   => \T_USE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 33 : 39,
                    'seenInFile'  => [
                        ($php8Names === true) ? 10 : 14 => ($php8Names === true) ? [14, 33] : [18, 39],
                    ],
                ],
            ],
            'First namespace: in class after non-import T_USE token' => [
                'marker'   => '/* testInClassAfterFirstTrackingToken */',
                'target'   => \T_CLOSE_CURLY_BRACKET,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 51 : 57,
                    'seenInFile'  => [
                        ($php8Names === true) ? 10 : 14 => ($php8Names === true) ? [14, 33, 51] : [18, 39, 57],
                    ],
                ],
            ],
            'No namespace: namespace declaration B' => [
                'marker'   => '/* testNamespaceDeclarationB */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 64 : 70,
                    'seenInFile'  => [
                        ($php8Names === true) ? 10 : 14 => ($php8Names === true) ? [14, 33, 51] : [18, 39, 57],
                    ],
                ],
            ],
            'Second namespace: in closure after non-import T_USE token' => [
                'marker'   => '/* testInClosureAfterFirstTrackingToken */',
                'target'   => \T_RETURN,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 82 : 88,
                    'seenInFile'  => [
                        ($php8Names === true) ? 10 : 14 => ($php8Names === true) ? [14, 33, 51] : [18, 39, 57],
                        ($php8Names === true) ? 68 : 74 => ($php8Names === true) ? [82] : [88],
                    ],
                ],
            ],
            'No namespace: namespace declaration C' => [
                'marker'   => '/* testNamespaceDeclarationC */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 109 : 115,
                    'seenInFile'  => [
                        ($php8Names === true) ? 10 : 14 => ($php8Names === true) ? [14, 33, 51] : [18, 39, 57],
                        ($php8Names === true) ? 68 : 74 => ($php8Names === true) ? [82] : [88],
                    ],
                ],
            ],
            'Third namespace: third import use statement' => [
                'marker'   => '/* testMultiImportUse */',
                'target'   => \T_USE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 117 : 125,
                    'seenInFile'  => [
                        ($php8Names === true) ? 10 : 14   => ($php8Names === true) ? [14, 33, 51] : [18, 39, 57],
                        ($php8Names === true) ? 68 : 74   => ($php8Names === true) ? [82] : [88],
                        ($php8Names === true) ? 113 : 121 => ($php8Names === true) ? [117] : [125],
                    ],
                ],
            ],
            'Third namespace: after scoped' => [
                'marker'   => '/* testAfterScoped */',
                'target'   => \T_ECHO,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 117 : 125,
                    'seenInFile'  => [
                        ($php8Names === true) ? 10 : 14   => ($php8Names === true) ? [14, 33, 51] : [18, 39, 57],
                        ($php8Names === true) ? 68 : 74   => ($php8Names === true) ? [82] : [88],
                        ($php8Names === true) ? 113 : 121 => ($php8Names === true) ? [117] : [125],
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
            'No namespace | No use statements: in first namespace declaration' => [
                'marker'   => '/* testNamespaceDeclarationA */',
                'target'   => ($php8Names === true) ? \T_NAME_QUALIFIED : \T_NS_SEPARATOR,
                'expected' => parent::$noStatementsInfoArray,
            ],
            'First namespace | No use statements: in first import use statement' => [
                'marker'   => '/* testGroupImportUse */',
                'target'   => \T_NS_SEPARATOR,
                'expected' => $expected,
            ],
            'First namespace | No use statements: end of first import close group curly' => [
                'marker'   => '/* testGroupImportUse */',
                'target'   => \T_CLOSE_USE_GROUP,
                'expected' => $expected,
            ],
        ];

        // First import use statement seen in the first namespace.
        $expected[($php8Names === true) ? 10 : 14]['lastPtr']       = ($php8Names === true) ? 14 : 18;
        $expected[($php8Names === true) ? 10 : 14]['statements']    = self::$statementsFirstNSFirstUse;
        $expected[($php8Names === true) ? 10 : 14]['effectiveFrom'] = ($php8Names === true) ? 30 : 36;

        $data['First namespace | Has first use statement: end of second import use statement'] = [
            'marker'   => '/* testSingleImportUse */',
            'target'   => \T_SEMICOLON,
            'expected' => $expected,
        ];

        // Second import use statement seen in the first namespace.
        $expected[($php8Names === true) ? 10 : 14]['lastPtr']       = ($php8Names === true) ? 51 : 57;
        $expected[($php8Names === true) ? 10 : 14]['statements']    = self::$statementsFirstNSFull;
        $expected[($php8Names === true) ? 10 : 14]['effectiveFrom'] = ($php8Names === true) ? 39 : 45;

        $data['First namespace | Full set use statements: trait name in in-class import'] = [
            'marker'   => '/* testInClassBeforeFirstTrackingToken */',
            'target'   => \T_STRING,
            'expected' => $expected,
        ];

        // Start of second namespace declaration seen.
        $expected = [
            ($php8Names === true) ? 64 : 70 => parent::$noStatementsInfoSubArray,
        ];

        $data['No namespace | No use statements: in second namespace declaration'] = [
            'marker'   => '/* testNamespaceDeclarationB */',
            'target'   => \T_NAMESPACE,
            'expected' => $expected,
        ];

        // Closure use statement seen in the second namespace.
        $expected = [
            ($php8Names === true) ? 68 : 74 => parent::$noStatementsInfoSubArray,
        ];
        $expected[($php8Names === true) ? 68 : 74]['lastPtr'] = ($php8Names === true) ? 82 : 88;

        $data['Second namespace | No use statements: closure close curly'] = [
            'marker'   => '/* testInClosureAfterFirstTrackingToken */',
            'target'   => \T_CLOSE_CURLY_BRACKET,
            'expected' => $expected,
        ];

        // Start of third namespace declaration seen.
        $expected = [
            ($php8Names === true) ? 109 : 115 => parent::$noStatementsInfoSubArray,
        ];

        $data['No namespace | No use statements: in third namespace declaration'] = [
            'marker'   => '/* testNamespaceDeclarationC */',
            'target'   => \T_SEMICOLON,
            'expected' => $expected,
        ];

        // Third namespace seen.
        $expected = [
            ($php8Names === true) ? 113 : 121 => parent::$noStatementsInfoSubArray,
        ];

        $data['Third namespace | No use statements: in third import use statement'] = [
            'marker'   => '/* testMultiImportUse */',
            'target'   => \T_STRING,
            'expected' => $expected,
        ];

        // First import use statement seen in the third namespace.
        $expected[($php8Names === true) ? 113 : 121]['lastPtr']       = ($php8Names === true) ? 117 : 125;
        $expected[($php8Names === true) ? 113 : 121]['statements']    = self::$statementsThirdNSFull;
        $expected[($php8Names === true) ? 113 : 121]['effectiveFrom'] = ($php8Names === true) ? 130 : 141;

        $data['Third namespace | Full set use statements: after scoped'] = [
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
            'No namespace | No use statements: start of file' => [
                'marker'       => '/* testNamespaceDeclarationA */',
                'target'       => \T_SEMICOLON,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testStartOfFile */',
                'stopAtTarget' => \T_WHITESPACE,
            ],
            'First namespace | Has first use statement: second import use statement, not yet tracked' => [
                'marker'       => '/* testSingleImportUse */',
                'target'       => \T_STRING,
                'expected'     => self::$statementsFirstNSFirstUse,
                'stopAt'       => '/* testNamespaceDeclarationA */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'First namespace | Full set use statements: trait import, not yet tracked' => [
                'marker'       => '/* testInClassBeforeFirstTrackingToken */',
                'target'       => \T_USE,
                'expected'     => self::$statementsFirstNSFull,
                'stopAt'       => '/* testNamespaceDeclarationA */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'First namespace | Full set use statements: trait import, partially tracked' => [
                'marker'       => '/* testInClassBeforeFirstTrackingToken */',
                'target'       => \T_USE,
                'expected'     => self::$statementsFirstNSFull,
                'stopAt'       => '/* testSingleImportUse */',
                'stopAtTarget' => \T_STRING,
            ],
            'Second namespace | No use statements: return in closure, partially tracked' => [
                'marker'       => '/* testInClosureAfterFirstTrackingToken */',
                'target'       => \T_RETURN,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testSingleImportUse */',
                'stopAtTarget' => \T_CLASS,
            ],
            'No namespace | No use statements: third namespace declaration, partially tracked' => [
                'marker'       => '/* testNamespaceDeclarationC */',
                'target'       => \T_NAMESPACE,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testSingleImportUse */',
                'stopAtTarget' => \T_CLASS,
            ],
            'Third namespace | Full set use statements: after scoped; partially tracked' => [
                'marker'       => '/* testAfterScoped */',
                'target'       => \T_ECHO,
                'expected'     => self::$statementsThirdNSFull,
                'stopAt'       => '/* testInClosureAfterFirstTrackingToken */',
                'stopAtTarget' => \T_RETURN,
            ],
        ];
    }
}
