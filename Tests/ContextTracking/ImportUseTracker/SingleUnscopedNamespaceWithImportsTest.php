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
final class SingleUnscopedNamespaceWithImportsTest extends ImportUseTrackerTestCase
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
            'str_replace' => 'str_replace',
        ],
        'const'    => [],
    ];

    /**
     * Use statements array as expected after the first + second import use statement in the test case file.
     *
     * {@internal Should be a class constant, but can't be until minimum PHP is 5.6+.}
     *
     * @var array<string, array<string, string>>
     */
    private static $statementsFirstSecond = [
        'name'     => [
            'Exception' => 'Supplier\Exception',
            'Foo'       => 'Supplier\Some\Foo',
        ],
        'function' => [
            'str_replace' => 'str_replace',
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
            'Exception' => 'Supplier\Exception',
            'Foo'       => 'Supplier\Some\Foo',
        ],
        'function' => [
            'str_replace' => 'str_replace',
        ],
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
            'No namespace: namespace declaration' => [
                'marker'   => '/* testUnscopedNamespace */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => 6,
                    'seenInFile'  => [],
                ],
            ],
            'Namespaced: first import use statement' => [
                'marker'   => '/* testSingleImportUse */',
                'target'   => \T_USE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 14 : 18,
                    'seenInFile'  => [
                        ($php8Names === true) ? 10 : 14 => ($php8Names === true) ? [14] : [18],
                    ],
                ],
            ],
            'Namespaced: second import use statement' => [
                'marker'   => '/* testGroupImportUse */',
                'target'   => \T_USE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 23 : 27,
                    'seenInFile'  => [
                        ($php8Names === true) ? 10 : 14 => ($php8Names === true) ? [14, 23] : [18, 27],
                    ],
                ],
            ],
            'Namespaced: in class after non-import T_USE token' => [
                'marker'   => '/* testInClassAfterFirstTrackingToken */',
                'target'   => \T_CLOSE_CURLY_BRACKET,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 67 : 73,
                    'seenInFile'  => [
                        ($php8Names === true) ? 10 : 14 => ($php8Names === true) ? [14, 23, 67] : [18, 27, 73],
                    ],
                ],
            ],
            'Namespaced: third import use statement' => [
                'marker'   => '/* testMultiImportUse */',
                'target'   => \T_USE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 81 : 87,
                    'seenInFile'  => [
                        ($php8Names === true) ? 10 : 14 => ($php8Names === true) ? [14, 23, 67, 81] : [18, 27, 73, 87],
                    ],
                ],
            ],
            'Namespaced: in closure after non-import T_USE token' => [
                'marker'   => '/* testInClosureAfterFirstTrackingToken */',
                'target'   => \T_RETURN,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 109 : 118,
                    'seenInFile'  => [
                        ($php8Names === true) ? 10 : 14 =>
                            ($php8Names === true) ? [14, 23, 67, 81, 109] : [18, 27, 73, 87, 118],
                    ],
                ],
            ],
            'Namespaced: after scoped' => [
                'marker'   => '/* testAfterScoped */',
                'target'   => \T_ECHO,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 109 : 118,
                    'seenInFile'  => [
                        ($php8Names === true) ? 10 : 14 =>
                            ($php8Names === true) ? [14, 23, 67, 81, 109] : [18, 27, 73, 87, 118],
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
            'No use statements: in first import use statement' => [
                'marker'   => '/* testSingleImportUse */',
                'target'   => \T_SEMICOLON,
                'expected' => $expected,
            ],
        ];

        // First import use statement seen.
        $expected[($php8Names === true) ? 10 : 14]['lastPtr']       = ($php8Names === true) ? 14 : 18;
        $expected[($php8Names === true) ? 10 : 14]['statements']    = self::$statementsFirst;
        $expected[($php8Names === true) ? 10 : 14]['effectiveFrom'] = ($php8Names === true) ? 20 : 24;

        $data['Has first use statement: in second import group open'] = [
            'marker'   => '/* testGroupImportUse */',
            'target'   => \T_OPEN_USE_GROUP,
            'expected' => $expected,
        ];

        $data['Has first use statement: end of second import close group curly'] = [
            'marker'   => '/* testEndOfGroupUse */',
            'target'   => \T_CLOSE_USE_GROUP,
            'expected' => $expected,
        ];

        $data['Has first use statement: end of second import semicolon'] = [
            'marker'   => '/* testEndOfGroupUse */',
            'target'   => \T_SEMICOLON,
            'expected' => $expected,
        ];

        // Second import use statement seen.
        $expected[($php8Names === true) ? 10 : 14]['lastPtr']       = ($php8Names === true) ? 23 : 27;
        $expected[($php8Names === true) ? 10 : 14]['statements']    = self::$statementsFirstSecond;
        $expected[($php8Names === true) ? 10 : 14]['effectiveFrom'] = ($php8Names === true) ? 40 : 46;

        $data['Has first + second use statement: after end of second import semicolon'] = [
            'marker'   => '/* testEndOfGroupUse */',
            'target'   => \T_WHITESPACE,
            'expected' => $expected,
        ];

        $data['Has first + second use statement: short array inside attribute'] = [
            'marker'   => '/* testEndOfGroupUse */',
            'target'   => \T_OPEN_SHORT_ARRAY,
            'expected' => $expected,
        ];

        $data['Has first + second use statement: class declaration'] = [
            'marker'   => '/* testEndOfGroupUse */',
            'target'   => \T_CLASS,
            'expected' => $expected,
        ];

        // Trait use statement seen.
        $expected[($php8Names === true) ? 10 : 14]['lastPtr'] = ($php8Names === true) ? 67 : 73;

        $data['Has first + second use statement: in third import comma'] = [
            'marker'   => '/* testMultiImportUse */',
            'target'   => \T_COMMA,
            'expected' => $expected,
        ];

        $data['Has first + second use statement: end of third import semicolon'] = [
            'marker'   => '/* testEndOfMultiUse */',
            'target'   => \T_SEMICOLON,
            'expected' => $expected,
        ];

        // Third import use statement seen.
        $expected[($php8Names === true) ? 10 : 14]['lastPtr']       = ($php8Names === true) ? 81 : 87;
        $expected[($php8Names === true) ? 10 : 14]['statements']    = self::$statementsFull;
        $expected[($php8Names === true) ? 10 : 14]['effectiveFrom'] = ($php8Names === true) ? 95 : 104;

        $data['Full set use statements: after end of third import'] = [
            'marker'   => '/* testEndOfMultiUse */',
            'target'   => \T_WHITESPACE,
            'expected' => $expected,
        ];

        $data['Full set use statements: at closure'] = [
            'marker'   => '/* testClosureBeforeFirstTrackingToken */',
            'target'   => \T_CLOSURE,
            'expected' => $expected,
        ];

        // Closure use statement seen.
        $expected[($php8Names === true) ? 10 : 14]['lastPtr'] = ($php8Names === true) ? 109 : 118;

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
                'marker'       => '/* testUnscopedNamespace */',
                'target'       => \T_NAMESPACE,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testStartOfFile */',
                'stopAtTarget' => \T_WHITESPACE,
            ],
            'Has first use statement: end of group use statement, not yet tracked' => [
                'marker'       => '/* testEndOfGroupUse */',
                'target'       => \T_SEMICOLON,
                'expected'     => self::$statementsFirst,
                'stopAt'       => '/* testUnscopedNamespace */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'Has first + second use statement: attribute start, not yet tracked' => [
                'marker'       => '/* testEndOfGroupUse */',
                'target'       => \T_ATTRIBUTE,
                'expected'     => self::$statementsFirstSecond,
                'stopAt'       => '/* testUnscopedNamespace */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'Has first + second use statement: short array inside attribute' => [
                'marker'       => '/* testEndOfGroupUse */',
                'target'       => \T_OPEN_SHORT_ARRAY,
                'expected'     => self::$statementsFirstSecond,
                'stopAt'       => '/* testSingleImportUse */',
                'stopAtTarget' => \T_STRING,
            ],
            'Has first + second use statement: trait import use name, attribute tracked' => [
                'marker'       => '/* testInClassBeforeFirstTrackingToken */',
                'target'       => \T_STRING,
                'expected'     => self::$statementsFirstSecond,
                'stopAt'       => '/* testEndOfGroupUse */',
                'stopAtTarget' => \T_ATTRIBUTE,
            ],
            'Has first + second use statement: third use import statement, partially tracked' => [
                'marker'       => '/* testMultiImportUse */',
                'target'       => \T_STRING, // `const` keyword.
                'expected'     => self::$statementsFirstSecond,
                'stopAt'       => '/* testEndOfGroupUse */',
                'stopAtTarget' => \T_SEMICOLON,
            ],
            'Full set use statements: in closure; closure not yet tracked' => [
                'marker'       => '/* testInClosureAfterFirstTrackingToken */',
                'target'       => \T_RETURN,
                'expected'     => self::$statementsFull,
                'stopAt'       => '/* testClosureBeforeFirstTrackingToken */',
                'stopAtTarget' => \T_OPEN_PARENTHESIS,
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
