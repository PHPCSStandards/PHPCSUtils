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
final class MultipleScopedNamespacesSomeImportsTest extends ImportUseTrackerTestCase
{

    /**
     * Use statements array as expected after the first import use statement in the first namespace in the test case file.
     *
     * {@internal Should be a class constant, but can't be until minimum PHP is 5.6+.}
     *
     * @var array<string, array<string, string>>
     */
    private static $statementsFirstNSFirstUse = [
        'name'     => [],
        'function' => [
            'array_map' => 'array_map',
        ],
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
            'Something' => 'Dependency\Something',
            'Template'  => 'Dependency\View\Template',
        ],
        'function' => [
            'array_map' => 'array_map',
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
            'PHP_EOL'             => 'PHP_EOL',
            'DIRECTORY_SEPARATOR' => 'DIRECTORY_SEPARATOR',
            'MYCONST'             => 'Package\MYCONST',
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
                'marker'   => '/* testSingleImportUse */',
                'target'   => \T_USE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 17 : 21,
                    'seenInFile'  => [
                        ($php8Names === true) ? 11 : 15 => ($php8Names === true) ? [17] : [21],
                    ],
                ],
            ],
            'First namespace: second import use statement' => [
                'marker'   => '/* testGroupImportUse */',
                'target'   => \T_USE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 28 : 32,
                    'seenInFile'  => [
                        ($php8Names === true) ? 11 : 15 => ($php8Names === true) ? [17, 28] : [21, 32],
                    ],
                ],
            ],
            'First namespace: in class after non-import T_USE token' => [
                'marker'   => '/* testNScloser */',
                'target'   => \T_CLOSE_CURLY_BRACKET,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 58 : 64,
                    'seenInFile'  => [
                        ($php8Names === true) ? 11 : 15 => ($php8Names === true) ? [17, 28, 58] : [21, 32, 64],
                    ],
                ],
            ],
            'No namespace: namespace declaration B' => [
                'marker'   => '/* testNamespaceDeclarationB */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 74 : 80,
                    'seenInFile'  => [
                        ($php8Names === true) ? 11 : 15 => ($php8Names === true) ? [17, 28, 58] : [21, 32, 64],
                    ],
                ],
            ],
            'Second namespace: in closure after non-import T_USE token' => [
                'marker'   => '/* testInSecond */',
                'target'   => \T_RETURN,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 95 : 101,
                    'seenInFile'  => [
                        ($php8Names === true) ? 11 : 15 => ($php8Names === true) ? [17, 28, 58] : [21, 32, 64],
                        ($php8Names === true) ? 79 : 85 => ($php8Names === true) ? [95] : [101],
                    ],
                ],
            ],
            'No namespace: namespace declaration C' => [
                'marker'   => '/* testNamespaceDeclarationC */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 122 : 128,
                    'seenInFile'  => [
                        ($php8Names === true) ? 11 : 15 => ($php8Names === true) ? [17, 28, 58] : [21, 32, 64],
                        ($php8Names === true) ? 79 : 85 => ($php8Names === true) ? [95] : [101],
                    ],
                ],
            ],
            'Third namespace: third import use statement' => [
                'marker'   => '/* testMultiImportUse */',
                'target'   => \T_USE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 133 : 139,
                    'seenInFile'  => [
                        ($php8Names === true) ? 11 : 15   => ($php8Names === true) ? [17, 28, 58] : [21, 32, 64],
                        ($php8Names === true) ? 79 : 85   => ($php8Names === true) ? [95] : [101],
                        ($php8Names === true) ? 127 : 133 => ($php8Names === true) ? [133] : [139],
                    ],
                ],
            ],
            'Third namespace: after scoped' => [
                'marker'   => '/* testAfterScoped */',
                'target'   => \T_ECHO,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 133 : 139,
                    'seenInFile'  => [
                        ($php8Names === true) ? 11 : 15   => ($php8Names === true) ? [17, 28, 58] : [21, 32, 64],
                        ($php8Names === true) ? 79 : 85   => ($php8Names === true) ? [95] : [101],
                        ($php8Names === true) ? 127 : 133 => ($php8Names === true) ? [133] : [139],
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
            ($php8Names === true) ? 11 : 15 => parent::$noStatementsInfoSubArray,
        ];
        $data      = [
            'No namespace | No use statements: in first namespace declaration' => [
                'marker'   => '/* testNamespaceDeclarationA */',
                'target'   => \T_OPEN_CURLY_BRACKET,
                'expected' => parent::$noStatementsInfoArray,
            ],
            'First namespace | No use statements: end of first import use statement' => [
                'marker'   => '/* testSingleImportUse */',
                'target'   => \T_SEMICOLON,
                'expected' => $expected,
            ],
        ];

        // First import use statement seen in the first namespace.
        $expected[($php8Names === true) ? 11 : 15]['lastPtr']       = ($php8Names === true) ? 17 : 21;
        $expected[($php8Names === true) ? 11 : 15]['statements']    = self::$statementsFirstNSFirstUse;
        $expected[($php8Names === true) ? 11 : 15]['effectiveFrom'] = ($php8Names === true) ? 23 : 27;

        $data['First namespace | Has first use statement: in second import use statement'] = [
            'marker'   => '/* testGroupImportUse */',
            'target'   => \T_COMMA,
            'expected' => $expected,
        ];

        // Second import use statement seen in the first namespace.
        $expected[($php8Names === true) ? 11 : 15]['lastPtr']       = ($php8Names === true) ? 28 : 32;
        $expected[($php8Names === true) ? 11 : 15]['statements']    = self::$statementsFirstNSFull;
        $expected[($php8Names === true) ? 11 : 15]['effectiveFrom'] = ($php8Names === true) ? 45 : 51;

        $data['First namespace | Full set use statements: class name in declaration'] = [
            'marker'   => '/* testInFirst */',
            'target'   => \T_STRING,
            'expected' => $expected,
        ];

        // Trait use statement seen.
        $expected[($php8Names === true) ? 11 : 15]['lastPtr'] = ($php8Names === true) ? 58 : 64;

        $data['First namespace | Full set use statements: namespace closer'] = [
            'marker'   => '/* testNScloser */',
            'target'   => \T_CLOSE_CURLY_BRACKET,
            'expected' => $expected,
        ];

        // Start of second namespace declaration seen.
        $expected = [
            ($php8Names === true) ? 70 : 76 => parent::$noStatementsInfoSubArray,
        ];

        $data['No namespace | No use statements: in second namespace declaration'] = [
            'marker'   => '/* testNamespaceDeclarationB */',
            'target'   => \T_COMMENT,
            'expected' => $expected,
        ];

        // Second namespace seen.
        $expected = [
            ($php8Names === true) ? 79 : 85 => parent::$noStatementsInfoSubArray,
        ];

        $data['Second namespace | No use statements: closure use'] = [
            'marker'   => '/* testInSecond */',
            'target'   => \T_USE,
            'expected' => $expected,
        ];

        // Start of third namespace declaration seen.
        $expected = [
            ($php8Names === true) ? 118 : 124 => parent::$noStatementsInfoSubArray,
        ];

        $data['No namespace | No use statements: in third namespace declaration'] = [
            'marker'   => '/* testNamespaceDeclarationC */',
            'target'   => \T_STRING,
            'expected' => $expected,
        ];

        // Third namespace seen.
        $expected = [
            ($php8Names === true) ? 127 : 133 => parent::$noStatementsInfoSubArray,
        ];

        $data['Third namespace | No use statements: in third import use statement'] = [
            'marker'   => '/* testMultiImportUse */',
            'target'   => \T_COMMA,
            'expected' => $expected,
        ];

        // First import use statement seen in the third namespace.
        $expected[($php8Names === true) ? 127 : 133]['lastPtr']       = ($php8Names === true) ? 133 : 139;
        $expected[($php8Names === true) ? 127 : 133]['statements']    = self::$statementsThirdNSFull;
        $expected[($php8Names === true) ? 127 : 133]['effectiveFrom'] = ($php8Names === true) ? 145 : 154;

        $data['Third namespace | Full set use statements: function declaration'] = [
            'marker'   => '/* testInThird */',
            'target'   => \T_FUNCTION,
            'expected' => $expected,
        ];

        // End of third namespace seen.
        $expected = [
            ($php8Names === true) ? 173 : 182 => parent::$noStatementsInfoSubArray,
        ];

        $data['No namespace | No use statements: after scoped (parse error)'] = [
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
                'target'       => \T_NAMESPACE,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testStartOfFile */',
                'stopAtTarget' => \T_WHITESPACE,
            ],
            'First namespace | Has first use statement: second import use statement, not yet tracked' => [
                'marker'       => '/* testGroupImportUse */',
                'target'       => \T_OPEN_USE_GROUP,
                'expected'     => self::$statementsFirstNSFirstUse,
                'stopAt'       => '/* testNamespaceDeclarationA */',
                'stopAtTarget' => \T_OPEN_CURLY_BRACKET,
            ],
            'First namespace | Full set use statements: class name in declaration, not yet tracked' => [
                'marker'       => '/* testInFirst */',
                'target'       => \T_STRING,
                'expected'     => self::$statementsFirstNSFull,
                'stopAt'       => '/* testNamespaceDeclarationA */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'First namespace | Full set use statements: trait import, partially tracked' => [
                'marker'       => '/* testInFirst */',
                'target'       => \T_USE,
                'expected'     => self::$statementsFirstNSFull,
                'stopAt'       => '/* testSingleImportUse */',
                'stopAtTarget' => \T_STRING,
            ],
            'Second namespace | No use statements: closure, partially tracked' => [
                'marker'       => '/* testInSecond */',
                'target'       => \T_CLOSURE,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testGroupImportUse */',
                'stopAtTarget' => \T_USE,
            ],
            'No namespace | No use statements: third namespace declaration, partially tracked' => [
                'marker'       => '/* testNamespaceDeclarationC */',
                'target'       => \T_STRING,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testInFirst */',
                'stopAtTarget' => \T_CLASS,
            ],
            'Third namespace | Full set use statements: variable in function; partially tracked' => [
                'marker'       => '/* testInThird */',
                'target'       => \T_VARIABLE,
                'expected'     => self::$statementsThirdNSFull,
                'stopAt'       => '/* testNamespaceDeclarationC */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'No namespace | No use statements: after scoped (parse error)' => [
                'marker'       => '/* testAfterScoped */',
                'target'       => \T_ECHO,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testNScloser */',
                'stopAtTarget' => \T_CLOSE_CURLY_BRACKET,
            ],
        ];
    }
}
