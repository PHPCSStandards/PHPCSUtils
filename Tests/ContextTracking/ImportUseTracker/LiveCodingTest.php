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
final class LiveCodingTest extends ImportUseTrackerTestCase
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
            'implode' => 'implode',
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
            'No namespace: namespace declaration' => [
                'marker'   => '/* testNamespaceDeclaration */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => 7,
                    'seenInFile'  => [],
                ],
            ],
            'Namespaced: first import use statement' => [
                'marker'   => '/* testSingleImportUse */',
                'target'   => \T_USE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 15 : 19,
                    'seenInFile'  => [
                        ($php8Names === true) ? 11 : 15 => ($php8Names === true) ? [15] : [19],
                    ],
                ],
            ],
            'Namespaced: second import use statement' => [
                'marker'   => '/* testGroupImportUse */',
                'target'   => \T_USE,
                'expected' => [
                    'file'        => $fileName,
                    'lastSeenPtr' => ($php8Names === true) ? 26 : 30,
                    'seenInFile'  => [
                        ($php8Names === true) ? 11 : 15 => ($php8Names === true) ? [15, 26] : [19, 30],
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
            'No use statements: start of file' => [
                'marker'   => '/* testStartOfFile */',
                'target'   => \T_WHITESPACE,
                'expected' => parent::$noStatementsInfoArray,
            ],
        ];

        // Start of namespace declaration seen.
        $expected = [
            ($php8Names === true) ? 11 : 15 => parent::$noStatementsInfoSubArray,
        ];

        $data['No use statements: in first import use statement'] = [
            'marker'   => '/* testSingleImportUse */',
            'target'   => \T_STRING,
            'expected' => $expected,
        ];

        // First import use statement seen.
        $expected[($php8Names === true) ? 11 : 15]['lastPtr']       = ($php8Names === true) ? 15 : 19;
        $expected[($php8Names === true) ? 11 : 15]['statements']    = self::$statementsFirst;
        $expected[($php8Names === true) ? 11 : 15]['effectiveFrom'] = ($php8Names === true) ? 21 : 25;

        $data['Has first use statement: in second import group open'] = [
            'marker'   => '/* testGroupImportUse */',
            'target'   => \T_STRING,
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
                'marker'       => '/* testNamespaceDeclaration */',
                'target'       => \T_NAMESPACE,
                'expected'     => parent::$noStatements,
                'stopAt'       => '/* testStartOfFile */',
                'stopAtTarget' => \T_WHITESPACE,
            ],
            'Has first use statement: in group use statement, not yet tracked' => [
                'marker'       => '/* testGroupImportUse */',
                'target'       => \T_COMMA,
                'expected'     => self::$statementsFirst,
                'stopAt'       => '/* testNamespaceDeclaration */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
        ];
    }
}
