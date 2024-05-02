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
final class LiveCodingUnscopedTest extends NamespaceTrackerTestCase
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
                'end'   => ($php8Names === true) ? 9 : 13,
                'name'  => '',
            ],
            1 => [
                'start' => ($php8Names === true) ? 10 : 14,
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
        $fileName  = \str_replace('.php', '.inc', __FILE__);
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'Live Coding: namespace declaration 2' => [
                'marker'   => '/* testNamespaceDeclarationB */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? 59 : 65,
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
        $firstNamespaceOpen = self::getSeenInFile()[1];

        return [
            'Live Coding | First namespace: unfinished namespace declaration 2' => [
                'marker'   => '/* testNamespaceDeclarationB */',
                'target'   => \T_NAMESPACE,
                'expected' => $firstNamespaceOpen,
            ],
            'Live Coding | First namespace: namespace declaration 2 name' => [
                'marker'   => '/* testNamespaceDeclarationB */',
                'target'   => \T_STRING,
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
            'First namespace: in class after first tracking token in class; class tracked' => [
                'marker'       => '/* testInClassAfterFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => 'Vendor\Package\FirstNamespace',
                'stopAt'       => '/* testNamespaceOperatorInClass */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'Live Coding | First namespace: namespace declaration 2 name' => [
                'marker'       => '/* testNamespaceDeclarationB */',
                'target'       => \T_STRING,
                'expected'     => 'Vendor\Package\FirstNamespace',
                'stopAt'       => '/* testNamespaceDeclarationB */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'Live Coding | First namespace: end of file' => [
                'marker'       => '/* testEndOfFile */',
                'target'       => \T_WHITESPACE,
                'expected'     => 'Vendor\Package\FirstNamespace',
                'stopAt'       => '/* testNamespaceDeclarationB */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
        ];
    }
}
