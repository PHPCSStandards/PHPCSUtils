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
final class MultipleScopedNamespacesTest extends NamespaceTrackerTestCase
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
                'end'   => ($php8Names === true) ? 32 : 38,
                'name'  => 'Vendor\Package\FirstNamespace',
            ],
            2 => [
                'start' => ($php8Names === true) ? 33 : 39,
                'end'   => ($php8Names === true) ? 41 : 47,
                'name'  => '',
            ],
            3 => [
                'start' => ($php8Names === true) ? 42 : 48,
                'end'   => ($php8Names === true) ? 53 : 61,
                'name'  => '',
            ],
            4 => [
                'start' => ($php8Names === true) ? 54 : 62,
                'end'   => ($php8Names === true) ? 63 : 71,
                'name'  => '',
            ],
            5 => [
                'start' => ($php8Names === true) ? 64 : 72,
                'end'   => ($php8Names === true) ? 91 : 101,
                'name'  => 'ThirdNamespace',
            ],
            6 => [
                'start' => ($php8Names === true) ? 92 : 102,
                'end'   => null,
                'name'  => '',
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
                    'lastSeenPtr'  => ($php8Names === true) ? 32 : 38,
                    'currentNSPtr' => 2,
                    'seenInFile'   => parent::getSeenInFileSubset(0, 3, true),
                ],
            ],
            'First namespace: namespace operator' => [
                'marker'   => '/* testInFirst */',
                'target'   => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? 32 : 38,
                    'currentNSPtr' => 2,
                    'seenInFile'   => parent::getSeenInFileSubset(0, 3, true),
                ],
            ],
            'First namespace: namespace closer' => [
                'marker'   => '/* testNScloser */',
                'target'   => \T_CLOSE_CURLY_BRACKET,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? 32 : 38,
                    'currentNSPtr' => 2,
                    'seenInFile'   => parent::getSeenInFileSubset(0, 3, true),
                ],
            ],
            'Not namespaced: namespace declaration 2' => [
                'marker'   => '/* testNamespaceDeclarationB */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? 53 : 61,
                    'currentNSPtr' => 4,
                    'seenInFile'   => parent::getSeenInFileSubset(0, 5, true),
                ],
            ],
            'Second namespace: namespace operator' => [
                'marker'   => '/* testInSecond */',
                'target'   => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? 53 : 61,
                    'currentNSPtr' => 4,
                    'seenInFile'   => parent::getSeenInFileSubset(0, 5, true),
                ],
            ],
            'Not namespaced: namespace declaration 3' => [
                'marker'   => '/* testNamespaceDeclarationC */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? 91 : 101,
                    'currentNSPtr' => 6,
                    'seenInFile'   => $allSeenInFile,
                ],
            ],
            'Third namespace: namespace operator' => [
                'marker'   => '/* testInThird */',
                'target'   => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? 91 : 101,
                    'currentNSPtr' => 6,
                    'seenInFile'   => $allSeenInFile,
                ],
            ],
            'No namespace: after scoped (invalid) - echo' => [
                'marker'   => '/* testAfterScoped */',
                'target'   => \T_ECHO,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? 91 : 101,
                    'currentNSPtr' => 6,
                    'seenInFile'   => $allSeenInFile,
                ],
            ],
            'No namespace: after scoped (invalid) - namespace operator' => [
                'marker'   => '/* testAfterScoped */',
                'target'   => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => ($php8Names === true) ? 91 : 109,
                    'currentNSPtr' => 6,
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

        $firstNamespaceClosed       = self::getSeenInFile()[1];
        $secondNamespaceDeclaration = self::getSeenInFile()[2];
        $secondNamespaceClosed      = self::getSeenInFile()[3];
        $thirdNamespaceDeclaration  = self::getSeenInFile()[4];
        $thirdNamespaceClosed       = self::getSeenInFile()[5];
        $afterThirdNamespace        = self::getSeenInFile()[6];

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
            'Not namespaced: namespace 1 open curly' => [
                'marker'   => '/* testNamespaceDeclarationA */',
                'target'   => \T_OPEN_CURLY_BRACKET,
                'expected' => $startGlobalClosed,
            ],
            'First namespace: before first tracking token' => [
                'marker'   => '/* testInFirst */',
                'target'   => \T_CLASS,
                'expected' => $firstNamespaceClosed,
            ],
            'First namespace: namespace operator' => [
                'marker'   => '/* testInFirst */',
                'target'   => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected' => $firstNamespaceClosed,
            ],
            'First namespace: after first tracking token' => [
                'marker'   => '/* testInFirst */',
                'target'   => \T_OPEN_CURLY_BRACKET,
                'expected' => $firstNamespaceClosed,
            ],
            'First namespace: namespace 1 effective end' => [
                'marker'   => '/* testNScloser */',
                'target'   => \T_CLOSE_CURLY_BRACKET,
                'expected' => $firstNamespaceClosed,
            ],
            'Not namespaced: namespace declaration 2' => [
                'marker'   => '/* testNamespaceDeclarationB */',
                'target'   => \T_NAMESPACE,
                'expected' => $secondNamespaceDeclaration,
            ],
            'Not namespaced: namespace declaration 2 comment' => [
                'marker'   => '/* testNamespaceDeclarationB */',
                'target'   => \T_COMMENT,
                'expected' => $secondNamespaceDeclaration,
            ],
            'Second namespace: before first tracking token' => [
                'marker'   => '/* testInSecond */',
                'target'   => \T_ECHO,
                'expected' => $secondNamespaceClosed,
            ],
            'Second namespace: namespace operator' => [
                'marker'   => '/* testInSecond */',
                'target'   => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected' => $secondNamespaceClosed,
            ],
            'Second namespace: after first tracking token' => [
                'marker'   => '/* testInSecond */',
                'target'   => \T_SEMICOLON,
                'expected' => $secondNamespaceClosed,
            ],
            'Not namespaced: namespace declaration 3' => [
                'marker'   => '/* testNamespaceDeclarationC */',
                'target'   => \T_NAMESPACE,
                'expected' => $thirdNamespaceDeclaration,
            ],
            'Not namespaced: namespace declaration 3 name' => [
                'marker'   => '/* testNamespaceDeclarationC */',
                'target'   => \T_STRING,
                'expected' => $thirdNamespaceDeclaration,
            ],
            'Third namespace: namespace 3 open curly' => [
                'marker'   => '/* testNamespaceDeclarationCOpener */',
                'target'   => \T_OPEN_CURLY_BRACKET,
                'expected' => $thirdNamespaceDeclaration,
            ],
            'Third namespace: namespace 3 effective start' => [
                'marker'   => '/* testNamespaceDeclarationCOpener */',
                'target'   => \T_WHITESPACE,
                'expected' => $thirdNamespaceClosed,
            ],
            'Third namespace: before first tracking token' => [
                'marker'   => '/* testInThird */',
                'target'   => \T_FUNCTION,
                'expected' => $thirdNamespaceClosed,
            ],
            'Third namespace: namespace operator' => [
                'marker'   => '/* testInThird */',
                'target'   => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected' => $thirdNamespaceClosed,
            ],
            'Third namespace: after first tracking token' => [
                'marker'   => '/* testInThird */',
                'target'   => \T_SEMICOLON,
                'expected' => $thirdNamespaceClosed,
            ],
            'No namespace: after scoped (invalid) - echo' => [
                'marker'   => '/* testAfterScoped */',
                'target'   => \T_ECHO,
                'expected' => $afterThirdNamespace,
            ],
            'No namespace: after scoped (invalid) - namespace operator' => [
                'marker'   => '/* testAfterScoped */',
                'target'   => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected' => $afterThirdNamespace,
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
            'First namespace: namespace 1 open curly' => [
                'marker'       => '/* testNamespaceDeclarationA */',
                'target'       => \T_OPEN_CURLY_BRACKET,
                'expected'     => '',
                'stopAt'       => '/* testNamespaceDeclarationA */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'First namespace: class token' => [
                'marker'       => '/* testInFirst */',
                'target'       => \T_CLASS,
                'expected'     => 'Vendor\Package\FirstNamespace',
                'stopAt'       => '/* testNamespaceDeclarationA */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'First namespace: namespace operator' => [
                'marker'       => '/* testInFirst */',
                'target'       => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
                'expected'     => 'Vendor\Package\FirstNamespace',
                'stopAt'       => '/* testInFirst */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
            'First namespace: namespace 1 effective end' => [
                'marker'       => '/* testNScloser */',
                'target'       => \T_CLOSE_CURLY_BRACKET,
                'expected'     => 'Vendor\Package\FirstNamespace',
                'stopAt'       => '/* testNamespaceDeclarationA */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'Not namespaced: namespace declaration 2 comment' => [
                'marker'       => '/* testNamespaceDeclarationB */',
                'target'       => \T_COMMENT,
                'expected'     => '',
                'stopAt'       => '/* testNamespaceDeclarationB */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'Second namespace: echo' => [
                'marker'       => '/* testInSecond */',
                'target'       => \T_ECHO,
                'expected'     => '',
                'stopAt'       => '/* testNamespaceDeclarationB */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'Not namespaced: namespace declaration 3 name' => [
                'marker'       => '/* testNamespaceDeclarationC */',
                'target'       => \T_STRING,
                'expected'     => '',
                'stopAt'       => '/* testNamespaceDeclarationC */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'Third namespace: namespace 3 effective start' => [
                'marker'       => '/* testNamespaceDeclarationCOpener */',
                'target'       => \T_WHITESPACE,
                'expected'     => 'ThirdNamespace',
                'stopAt'       => '/* testNamespaceDeclarationB */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'Third namespace: function token' => [
                'marker'       => '/* testInThird */',
                'target'       => \T_FUNCTION,
                'expected'     => 'ThirdNamespace',
                'stopAt'       => '/* testNamespaceDeclarationC */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'Third namespace: variable token' => [
                'marker'       => '/* testInThird */',
                'target'       => \T_VARIABLE,
                'expected'     => 'ThirdNamespace',
                'stopAt'       => '/* testNamespaceDeclarationC */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
            'No namespace: after scoped (invalid) - echo' => [
                'marker'       => '/* testAfterScoped */',
                'target'       => \T_ECHO,
                'expected'     => '',
                'stopAt'       => '/* testInThird */',
                'stopAtTarget' => ($php8Names === true) ? \T_NAME_RELATIVE : \T_NAMESPACE,
            ],
        ];
    }
}
