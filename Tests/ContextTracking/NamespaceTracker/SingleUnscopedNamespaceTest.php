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
final class SingleUnscopedNamespaceTest extends NamespaceTrackerTestCase
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
                'name'  => 'Vendor\Package\Name',
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
        $fileName      = \str_replace('.php', '.inc', __FILE__);
        $php8Names     = parent::usesPhp8NameTokens();
        $allSeenInFile = self::getSeenInFile();

        return [
            'No namespace: start of file' => [
                'marker'   => '/* testStartOfFile */',
                'target'   => \T_WHITESPACE,
                'expected' => [
                    'file'         => '',
                    'lastSeenPtr'  => -1,
                    'currentNSPtr' => 0,
                    'seenInFile'   => parent::getSeenInFileSubset(0, 1, true),
                ],
            ],
            'Not namespaced: namespace declaration' => [
                'marker'   => '/* testNamespaceDeclaration */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => 6,
                    'currentNSPtr' => 1,
                    'seenInFile'   => $allSeenInFile,
                ],
            ],
            'Namespaced: in class' => [
                'marker'   => '/* testInClassAfterFirstTrackingToken */',
                'target'   => \T_WHITESPACE,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? 6 : 45,
                    'currentNSPtr' => 1,
                    'seenInFile'   => $allSeenInFile,
                ],
            ],
            'Namespaced: after scoped' => [
                'marker'   => '/* testAfterScoped */',
                'target'   => \T_WHITESPACE,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? 6 : 96,
                    'currentNSPtr' => 1,
                    'seenInFile'   => $allSeenInFile,
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

        $startGlobalOpen        = self::getSeenInFile()[0];
        $startGlobalOpen['end'] = null;
        $startGlobalClosed      = self::getSeenInFile()[0];

        $firstNamespaceOpen = self::getSeenInFile()[1];

        return [
            'No namespace: start of file' => [
                'marker'   => '/* testStartOfFile */',
                'target'   => \T_WHITESPACE,
                'expected' => $startGlobalOpen,
            ],
            'Not namespaced: namespace declaration' => [
                'marker'   => '/* testNamespaceDeclaration */',
                'target'   => \T_NAMESPACE,
                'expected' => $startGlobalClosed,
            ],
            'Not namespaced: namespace declaration name' => [
                'marker'   => '/* testNamespaceDeclaration */',
                'target'   => ($php8Names === true) ? \T_NAME_QUALIFIED : \T_NS_SEPARATOR,
                'expected' => $startGlobalClosed,
            ],
            'Not namespaced: namespace end of statement' => [
                'marker'   => '/* testNamespaceDeclaration */',
                'target'   => \T_SEMICOLON,
                'expected' => $startGlobalClosed,
            ],
            'Namespaced: in class before first tracking token in class' => [
                'marker'   => '/* testInClassBeforeFirstTrackingToken */',
                'target'   => \T_OPEN_PARENTHESIS,
                'expected' => $firstNamespaceOpen,
            ],
            'Namespaced: in class namespace operator' => [
                'marker'   => '/* testNamespaceOperatorInClass */',
                'target'   => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected' => $firstNamespaceOpen,
            ],
            'Namespaced: in class after first tracking token in class' => [
                'marker'   => '/* testInClassAfterFirstTrackingToken */',
                'target'   => \T_WHITESPACE,
                'expected' => $firstNamespaceOpen,
            ],
            'Namespaced: in function before first tracking token in function' => [
                'marker'   => '/* testInFunctionBeforeFirstTrackingToken */',
                'target'   => \T_VARIABLE,
                'expected' => $firstNamespaceOpen,
            ],
            'Namespaced: in function namespace operator' => [
                'marker'   => '/* testInFunctionBeforeFirstTrackingToken */',
                'target'   => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected' => $firstNamespaceOpen,
            ],
            'Namespaced: in function after first tracking token in function' => [
                'marker'   => '/* testInFunctionAfterFirstTrackingToken */',
                'target'   => \T_RETURN,
                'expected' => $firstNamespaceOpen,
            ],
            'Namespaced: after scoped' => [
                'marker'   => '/* testAfterScoped */',
                'target'   => \T_ECHO,
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
            'No namespace: start of file' => [
                'marker'       => '/* testStartOfFile */',
                'target'       => \T_WHITESPACE,
                'expected'     => '',
                'stopAt'       => '/* testStartOfFile */',
                'stopAtTarget' => \T_WHITESPACE,
            ],
            'Not namespaced: namespace declaration name' => [
                'marker'       => '/* testNamespaceDeclaration */',
                'target'       => ($php8Names === true) ? \T_NAME_QUALIFIED : \T_NS_SEPARATOR,
                'expected'     => '',
                'stopAt'       => '/* testNamespaceDeclaration */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'Namespaced: in class before first tracking token in class; class not yet tracked' => [
                'marker'       => '/* testInClassBeforeFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => 'Vendor\Package\Name',
                'stopAt'       => '/* testNamespaceDeclaration */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'Namespaced: in class before first tracking token in class; class tracked' => [
                'marker'       => '/* testInClassBeforeFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => 'Vendor\Package\Name',
                'stopAt'       => '/* testNamespaceOperatorInClass */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'Namespaced: in class after first tracking token in class; class not yet tracked' => [
                'marker'       => '/* testInClassAfterFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => 'Vendor\Package\Name',
                'stopAt'       => '/* testNamespaceDeclaration */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'Namespaced: in class after first tracking token in class; class tracked' => [
                'marker'       => '/* testInClassAfterFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => 'Vendor\Package\Name',
                'stopAt'       => '/* testNamespaceOperatorInClass */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'Namespaced: in function before first tracking token in function; function not yet tracked' => [
                'marker'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'target'       => \T_VARIABLE,
                'expected'     => 'Vendor\Package\Name',
                'stopAt'       => '/* testAttribute */',
                'stopAtTarget' => \T_ATTRIBUTE,
            ],
            'Namespaced: in function before first tracking token in function; function tracked' => [
                'marker'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'target'       => \T_VARIABLE,
                'expected'     => 'Vendor\Package\Name',
                'stopAt'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'Namespaced: in function namespace operator; function tracked' => [
                'marker'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'target'       => \T_OPEN_PARENTHESIS,
                'expected'     => 'Vendor\Package\Name',
                'stopAt'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'Namespaced: in function after first tracking token in function; function not yet tracked' => [
                'marker'       => '/* testInFunctionAfterFirstTrackingToken */',
                'target'       => \T_RETURN,
                'expected'     => 'Vendor\Package\Name',
                'stopAt'       => '/* testNamespaceOperatorInClass */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'Namespaced: in function after first tracking token in function; function tracked' => [
                'marker'       => '/* testInFunctionAfterFirstTrackingToken */',
                'target'       => \T_RETURN,
                'expected'     => 'Vendor\Package\Name',
                'stopAt'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'Namespaced: after scoped; not yet tracked' => [
                'marker'       => '/* testAfterScoped */',
                'target'       => \T_ECHO,
                'expected'     => 'Vendor\Package\Name',
                'stopAt'       => '/* testNamespaceDeclaration */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
        ];
    }
}
