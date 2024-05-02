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
final class LiveCodingScopedTest extends NamespaceTrackerTestCase
{

    /**
     * Helper function defining the "seenInFile" array.
     *
     * @return array<int, array<string, int|string|null>>
     */
    protected static function getSeenInFile()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            0 => [
                'start' => 0,
                'end'   => ($php8Names === true) ? 11 : 15,
                'name'  => '',
            ],
            1 => [
                'start' => ($php8Names === true) ? 12 : 16,
                'end'   => null,
                'name'  => 'Vendor\Package\FirstNamespace',
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
        $fileName = \str_replace('.php', '.inc', __FILE__);

        return [
            'Live Coding: namespace declaration' => [
                'marker'   => '/* testNamespaceDeclaration */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => 7,
                    'currentNSPtr' => 1,
                    'seenInFile'   => self::getSeenInFile(),
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

        $startGlobalClosed  = self::getSeenInFile()[0];
        $firstNamespaceOpen = self::getSeenInFile()[1];

        return [
            'Live Coding | No namespace: namespace declaration name' => [
                'marker'   => '/* testNamespaceDeclaration */',
                'target'   => ($php8Names === true) ? \T_NAME_QUALIFIED : \T_NS_SEPARATOR,
                'expected' => $startGlobalClosed,
            ],
            'Live Coding | First namespace: function keyword' => [
                'marker'   => '/* testInClassBeforeFirstTrackingToken */',
                'target'   => \T_FUNCTION,
                'expected' => $firstNamespaceOpen,
            ],
            'Live Coding | First namespace: namespace operator' => [
                'marker'   => '/* testNamespaceOperatorInClass */',
                'target'   => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected' => $firstNamespaceOpen,
            ],
            'Live Coding | First namespace: end of file' => [
                'marker'   => '/* testEndOfFile */',
                'target'   => \T_WHITESPACE,
                'expected' => $firstNamespaceOpen,
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
            'Live Coding | No namespace: namespace open curly; untracked' => [
                'marker'       => '/* testNamespaceDeclaration */',
                'target'       => \T_OPEN_CURLY_BRACKET,
                'expected'     => '',
                'stopAt'       => '/* testNamespaceDeclaration */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'Live Coding | First namespace: in class before first tracking token; class tracked' => [
                'marker'       => '/* testInClassBeforeFirstTrackingToken */',
                'target'       => \T_PUBLIC,
                'expected'     => 'Vendor\Package\FirstNamespace',
                'stopAt'       => '/* testNamespaceDeclaration */',
                'stopAtTarget' => \T_CLASS,
            ],
            'Live Coding | First namespace: in class after first tracking token in class; class untracked' => [
                'marker'       => '/* testNamespaceOperatorInClass */',
                'target'       => \T_OPEN_PARENTHESIS,
                'expected'     => 'Vendor\Package\FirstNamespace',
                'stopAt'       => '/* testNamespaceOperatorInClass */',
                'stopAtTarget' => \T_NEW,
            ],
            'Live Coding | First namespace: end of file; untracked' => [
                'marker'       => '/* testEndOfFile */',
                'target'       => \T_WHITESPACE,
                'expected'     => 'Vendor\Package\FirstNamespace',
                'stopAt'       => '/* testNamespaceOperatorInClass */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
        ];
    }
}
