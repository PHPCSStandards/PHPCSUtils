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
final class InvalidFirstNamespaceNotAtStartTest extends NamespaceTrackerTestCase
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
                'end'   => ($php8Names === true) ? 54 : 60,
                'name'  => '',
            ],
            1 => [
                'start' => ($php8Names === true) ? 55 : 61,
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
        $fileName  = \str_replace('.php', '.inc', __FILE__);
        $php8Names = parent::usesPhp8NameTokens();

        $data = [];

        if ($php8Names === false) {
            /*
             * This test will only work on PHPCS 3.x, as for PHPCS 4.x there are no tokens between the start of
             * the file and the target stackPtr which are tracked, so the `track()` function call is never
             * triggered by the test.
             */
            $data['Global namespace: namespace operator in class'] = [
                'marker'   => '/* testNamespaceOperatorInClass */',
                'target'   => \T_NAMESPACE,
                'expected' => [
                    'file'         => $fileName,
                    'lastSeenPtr'  => 33,
                    'currentNSPtr' => 0,
                    'seenInFile'   => parent::getSeenInFileSubset(0, 1, true),
                ],
            ];
        }

        $data['Invalid | Parse error: namespace declaration'] = [
            'marker'   => '/* testNamespaceDeclaration */',
            'target'   => \T_NAMESPACE,
            'expected' => [
                'file'         => $fileName,
                'lastSeenPtr'  => ($php8Names === true) ? 51 : 53,
                'currentNSPtr' => 1,
                'seenInFile'   => self::getSeenInFile(),
            ],
        ];

        return $data;
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

        $globalNamespaceOpen        = self::getSeenInFile()[0];
        $globalNamespaceOpen['end'] = null;
        $globalNamespaceClosed      = self::getSeenInFile()[0];
        $firstNamespaceOpen         = self::getSeenInFile()[1];

        return [
            'No namespace: class close curly' => [
                'marker'   => '/* testInClassAfterFirstTrackingToken */',
                'target'   => \T_CLOSE_CURLY_BRACKET,
                'expected' => $globalNamespaceOpen,
            ],
            'Invalid | No namespace: namespace declaration name' => [
                'marker'   => '/* testNamespaceDeclaration */',
                'target'   => ($php8Names === true) ? \T_NAME_QUALIFIED : \T_NS_SEPARATOR,
                'expected' => $globalNamespaceClosed,
            ],
            'Invalid | First namespace: end of file' => [
                'marker'   => '/* testEndOfFile */',
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
            'No namespace: in class after first tracking token in class; class tracked' => [
                'marker'       => '/* testInClassAfterFirstTrackingToken */',
                'target'       => \T_WHITESPACE,
                'expected'     => '',
                'stopAt'       => '/* testStartOfFile */',
                'stopAtTarget' => \T_CLASS,
            ],
            'Invalid | No namespace: namespace declaration, class tracked' => [
                'marker'       => '/* testNamespaceDeclaration */',
                'target'       => ($php8Names === true) ? \T_NAME_QUALIFIED : \T_NS_SEPARATOR,
                'expected'     => '',
                'stopAt'       => '/* testStartOfFile */',
                'stopAtTarget' => \T_CLASS,
            ],
            'Invalid | First namespace: end of file' => [
                'marker'       => '/* testEndOfFile */',
                'target'       => \T_WHITESPACE,
                'expected'     => 'Vendor\Package\Name',
                'stopAt'       => '/* testNamespaceDeclaration */',
                'stopAtTarget' => \T_NAMESPACE,
            ],
        ];
    }
}
