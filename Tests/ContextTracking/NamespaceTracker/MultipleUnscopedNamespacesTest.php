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
final class MultipleUnscopedNamespacesTest extends NamespaceTrackerTestCase
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
                'end'   => ($php8Names === true) ? 57 : 63,
                'name'  => 'Vendor\Package\FirstNamespace',
            ],
            2 => [
                'start' => ($php8Names === true) ? 58 : 64,
                'end'   => ($php8Names === true) ? 61 : 67,
                'name'  => '',
            ],
            3 => [
                'start' => ($php8Names === true) ? 62 : 68,
                'end'   => ($php8Names === true) ? 110 : 118,
                'name'  => 'SecondNamespace',
            ],
            4 => [
                'start' => ($php8Names === true) ? 111 : 119,
                'end'   => ($php8Names === true) ? 116 : 126,
                'name'  => '',
            ],
            5 => [
                'start' => ($php8Names === true) ? 117 : 127,
                'end'   => null,
                'name'  => 'Package\ThirdNamespace',
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
            'Not namespaced: namespace declaration 1' => [
                'marker'   => '/* testNamespaceDeclarationA */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => 6,
                    'currentNSPtr' => 1,
                    'seenInFile'   => parent::getSeenInFileSubset(0, 2, true),
                ],
            ],
            'First namespace: in class' => [
                'marker'   => '/* testNamespaceOperatorInClass */',
                'target'   => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? 6 : 45,
                    'currentNSPtr' => 1,
                    'seenInFile'   => parent::getSeenInFileSubset(0, 2, true),
                ],
            ],
            'Not namespaced: namespace declaration 2' => [
                'marker'   => '/* testNamespaceDeclarationB */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? 58 : 64,
                    'currentNSPtr' => 3,
                    'seenInFile'   => parent::getSeenInFileSubset(0, 4, true),
                ],
            ],
            'Second namespace: in function' => [
                'marker'   => '/* testInFunctionBeforeFirstTrackingToken */',
                'target'   => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? 58 : 97,
                    'currentNSPtr' => 3,
                    'seenInFile'   => parent::getSeenInFileSubset(0, 4, true),
                ],
            ],
            'Not namespaced: namespace declaration 3' => [
                'marker'   => '/* testNamespaceDeclarationC */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? 111 : 119,
                    'currentNSPtr' => 5,
                    'seenInFile'   => $allSeenInFile,
                ],
            ],
            'Third namespace: after scoped' => [
                'marker'   => '/* testAfterScoped */',
                'target'   => \T_ECHO,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? 111 : 119,
                    'currentNSPtr' => 5,
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

        $firstNamespaceOpen        = self::getSeenInFile()[1];
        $firstNamespaceOpen['end'] = null;

        $secondNamespaceDeclaration = self::getSeenInFile()[2];
        $secondNamespaceOpen        = self::getSeenInFile()[3];
        $secondNamespaceOpen['end'] = null;

        $thirdNamespaceDeclaration = self::getSeenInFile()[4];
        $thirdNamespaceOpen        = self::getSeenInFile()[5];

        return [
            'No namespace: start of file' => [
                'marker'   => '/* testStartOfFile */',
                'target'   => \T_WHITESPACE,
                'expected' => $startGlobalOpen,
            ],
            'Not namespaced: namespace declaration 1' => [
                'marker'   => '/* testNamespaceDeclarationA */',
                'target'   => \T_NAMESPACE,
                'expected' => $startGlobalClosed,
            ],
            'Not namespaced: namespace declaration 1 name' => [
                'marker'   => '/* testNamespaceDeclarationA */',
                'target'   => ($php8Names === true) ? \T_NAME_QUALIFIED : \T_NS_SEPARATOR,
                'expected' => $startGlobalClosed,
            ],
            'Not namespaced: namespace 1 end of statement' => [
                'marker'   => '/* testNamespaceDeclarationA */',
                'target'   => \T_SEMICOLON,
                'expected' => $startGlobalClosed,
            ],
            'First namespace: in class before first tracking token in class' => [
                'marker'   => '/* testInClassBeforeFirstTrackingToken */',
                'target'   => \T_WHITESPACE,
                'expected' => $firstNamespaceOpen,
            ],
            'First namespace: in class namespace operator' => [
                'marker'   => '/* testNamespaceOperatorInClass */',
                'target'   => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected' => $firstNamespaceOpen,
            ],
            'First namespace: in class after first tracking token in class' => [
                'marker'   => '/* testInClassAfterFirstTrackingToken */',
                'target'   => \T_WHITESPACE,
                'expected' => $firstNamespaceOpen,
            ],
            'Not namespaced: namespace declaration 2' => [
                'marker'   => '/* testNamespaceDeclarationB */',
                'target'   => \T_NAMESPACE,
                'expected' => $secondNamespaceDeclaration,
            ],
            'Not namespaced: namespace declaration 2 name' => [
                'marker'   => '/* testNamespaceDeclarationB */',
                'target'   => \T_STRING,
                'expected' => $secondNamespaceDeclaration,
            ],
            'Not namespaced: namespace 2 end of statement' => [
                'marker'   => '/* testNamespaceDeclarationB */',
                'target'   => \T_SEMICOLON,
                'expected' => $secondNamespaceDeclaration,
            ],
            'Second namespace: in function before first tracking token in function' => [
                'marker'   => '/* testInFunctionBeforeFirstTrackingToken */',
                'target'   => \T_VARIABLE,
                'expected' => $secondNamespaceOpen,
            ],
            'Second namespace: in function namespace operator' => [
                'marker'   => '/* testInFunctionBeforeFirstTrackingToken */',
                'target'   => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected' => $secondNamespaceOpen,
            ],
            'Second namespace: in function after first tracking token in function' => [
                'marker'   => '/* testInFunctionAfterFirstTrackingToken */',
                'target'   => \T_RETURN,
                'expected' => $secondNamespaceOpen,
            ],
            'Not namespaced: namespace declaration 3' => [
                'marker'   => '/* testNamespaceDeclarationC */',
                'target'   => \T_NAMESPACE,
                'expected' => $thirdNamespaceDeclaration,
            ],
            'Not namespaced: namespace declaration 3 name' => [
                'marker'   => '/* testNamespaceDeclarationC */',
                'target'   => ($php8Names === true) ? \T_NAME_QUALIFIED : \T_NS_SEPARATOR,
                'expected' => $thirdNamespaceDeclaration,
            ],
            'Third namespace: namespace 3 effective start' => [
                'marker'   => '/* testNamespaceDeclarationCEndOfStatement */',
                'target'   => \T_WHITESPACE,
                'expected' => $thirdNamespaceOpen,
            ],
            'Third namespace: after scoped' => [
                'marker'   => '/* testAfterScoped */',
                'target'   => \T_ECHO,
                'expected' => $thirdNamespaceOpen,
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
            'Not namespaced: namespace declaration 1 name' => [
                'marker'       => '/* testNamespaceDeclarationA */',
                'target'       => ($php8Names === true) ? \T_NAME_QUALIFIED : \T_NS_SEPARATOR,
                'expected'     => '',
                'stopAt'       => '/* testNamespaceDeclarationA */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'First namespace: in class before first tracking token in class; class not yet tracked' => [
                'marker'       => '/* testInClassBeforeFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => 'Vendor\Package\FirstNamespace',
                'stopAt'       => '/* testNamespaceDeclarationA */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'First namespace: in class before first tracking token in class; class tracked' => [
                'marker'       => '/* testInClassBeforeFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => 'Vendor\Package\FirstNamespace',
                'stopAt'       => '/* testNamespaceOperatorInClass */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'First namespace: in class after first tracking token in class; class not yet tracked' => [
                'marker'       => '/* testInClassAfterFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => 'Vendor\Package\FirstNamespace',
                'stopAt'       => '/* testNamespaceDeclarationA */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'First namespace: in class after first tracking token in class; class tracked' => [
                'marker'       => '/* testInClassAfterFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => 'Vendor\Package\FirstNamespace',
                'stopAt'       => '/* testNamespaceOperatorInClass */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'Not namespaced: namespace declaration 2 name' => [
                'marker'       => '/* testNamespaceDeclarationB */',
                'target'       => \T_STRING,
                'expected'     => '',
                'stopAt'       => '/* testNamespaceDeclarationB */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'Second namespace: in function before first tracking token in function; function not yet tracked 1' => [
                'marker'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'target'       => \T_VARIABLE,
                'expected'     => 'SecondNamespace',
                'stopAt'       => '/* testNamespaceDeclarationB */',
                'stopAtTarget' => \T_DOC_COMMENT_OPEN_TAG,
            ],
            'Second namespace: in function before first tracking token in function; function not yet tracked 2' => [
                'marker'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'target'       => \T_VARIABLE,
                'expected'     => 'SecondNamespace',
                'stopAt'       => '/* testNamespaceDeclarationB */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'Second namespace: in function before first tracking token in function; function tracked' => [
                'marker'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'target'       => \T_VARIABLE,
                'expected'     => 'SecondNamespace',
                'stopAt'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'Second namespace: in function namespace operator; function tracked' => [
                'marker'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'target'       => \T_OPEN_PARENTHESIS,
                'expected'     => 'SecondNamespace',
                'stopAt'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'Second namespace: in function after first tracking token in function; function not yet tracked' => [
                'marker'       => '/* testInFunctionAfterFirstTrackingToken */',
                'target'       => \T_RETURN,
                'expected'     => 'SecondNamespace',
                'stopAt'       => '/* testNamespaceDeclarationB */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'Second namespace: in function after first tracking token in function; function tracked' => [
                'marker'       => '/* testInFunctionAfterFirstTrackingToken */',
                'target'       => \T_RETURN,
                'expected'     => 'SecondNamespace',
                'stopAt'       => '/* testInFunctionBeforeFirstTrackingToken */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'Not namespaced: namespace declaration 3 name' => [
                'marker'       => '/* testNamespaceDeclarationC */',
                'target'       => ($php8Names === true) ? \T_NAME_QUALIFIED : \T_NS_SEPARATOR,
                'expected'     => '',
                'stopAt'       => '/* testNamespaceDeclarationC */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'Third namespace: after scoped' => [
                'marker'       => '/* testAfterScoped */',
                'target'       => \T_ECHO,
                'expected'     => 'Package\ThirdNamespace',
                'stopAt'       => '/* testNamespaceDeclarationC */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
        ];
    }
}
