<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Namespaces;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Namespaces;

/**
 * Tests for the \PHPCSUtils\Utils\Namespaces::getDeclaredName() method.
 *
 * @covers \PHPCSUtils\Utils\Namespaces::getDeclaredName
 *
 * @since 1.0.0
 */
final class GetDeclaredNameTest extends UtilityMethodTestCase
{

    /**
     * Test that false is returned when an invalid token is passed.
     *
     * @return void
     */
    public function testInvalidTokenPassed()
    {
        // Non-existent token.
        $this->assertFalse(Namespaces::getDeclaredName(self::$phpcsFile, 100000), 'Failed with non-existent token');

        // Non namespace token.
        $this->assertFalse(Namespaces::getDeclaredName(self::$phpcsFile, 0), 'Failed with non-namespace token');
    }

    /**
     * Test retrieving the cleaned up namespace name based on a T_NAMESPACE token.
     *
     * @dataProvider dataGetDeclaredName
     *
     * @param string                      $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, string|false> $expected   The expected output for the function.
     * @param bool                        $skipOnPHP8 Optional. Whether the test should be skipped when the
     *                                                PHP 8 identifier name tokenization is used (as the target token
     *                                                won't exist). Defaults to `false`.
     *
     * @return void
     */
    public function testGetDeclaredNameClean($testMarker, $expected, $skipOnPHP8 = false)
    {
        if ($skipOnPHP8 === true && parent::usesPhp8NameTokens() === true) {
            $this->markTestSkipped("PHP 8.0 identifier name tokenization used. Target token won't exist.");
        }

        $stackPtr = $this->getTargetToken($testMarker, \T_NAMESPACE);
        $result   = Namespaces::getDeclaredName(self::$phpcsFile, $stackPtr, true);

        $this->assertSame($expected['clean'], $result);
    }

    /**
     * Test retrieving the "dirty" namespace name based on a T_NAMESPACE token.
     *
     * @dataProvider dataGetDeclaredName
     *
     * @param string                      $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, string|false> $expected   The expected output for the function.
     * @param bool                        $skipOnPHP8 Optional. Whether the test should be skipped when the
     *                                                PHP 8 identifier name tokenization is used (as the target token
     *                                                won't exist). Defaults to `false`.
     *
     * @return void
     */
    public function testGetDeclaredNameDirty($testMarker, $expected, $skipOnPHP8 = false)
    {
        if ($skipOnPHP8 === true && parent::usesPhp8NameTokens() === true) {
            $this->markTestSkipped("PHP 8.0 identifier name tokenization used. Target token won't exist.");
        }

        $stackPtr = $this->getTargetToken($testMarker, \T_NAMESPACE);
        $result   = Namespaces::getDeclaredName(self::$phpcsFile, $stackPtr, false);

        $this->assertSame($expected['dirty'], $result);
    }

    /**
     * Data provider.
     *
     * @see testGetDeclaredName() For the array format.
     *
     * @return array<string, array<string, string|array<string, string|false>|bool>>
     */
    public static function dataGetDeclaredName()
    {
        return [
            'global-namespace-curlies' => [
                'testMarker' => '/* testGlobalNamespaceCurlies */',
                'expected'   => [
                    'clean' => '',
                    'dirty' => '',
                ],
            ],
            'namespace-semicolon' => [
                'testMarker' => '/* testNamespaceSemiColon */',
                'expected'   => [
                    'clean' => 'Vendor',
                    'dirty' => 'Vendor',
                ],
            ],
            'namespace-curlies' => [
                'testMarker' => '/* testNamespaceCurlies */',
                'expected'   => [
                    'clean' => 'Vendor\Package\Sublevel\End',
                    'dirty' => 'Vendor\Package\Sublevel\End',
                ],
            ],
            'namespace-curlies-no-space-at-end' => [
                'testMarker' => '/* testNamespaceCurliesNoSpaceAtEnd */',
                'expected'   => [
                    'clean' => 'Vendor\Package\Sublevel\Deeperlevel\End',
                    'dirty' => 'Vendor\Package\Sublevel\Deeperlevel\End',
                ],
            ],
            'namespace-close-tag' => [
                'testMarker' => '/* testNamespaceCloseTag */',
                'expected'   => [
                    'clean' => 'My\Name',
                    'dirty' => 'My\Name',
                ],
            ],
            'namespace-close-tag-no-space-at-end' => [
                'testMarker' => '/* testNamespaceCloseTagNoSpaceAtEnd */',
                'expected'   => [
                    'clean' => 'My\Other\Name',
                    'dirty' => 'My\Other\Name',
                ],
            ],
            'namespace-whitespace-tolerance' => [
                'testMarker' => '/* testNamespaceLotsOfWhitespace */',
                'expected'   => [
                    'clean' => 'Vendor\Package\Sub\Deeperlevel\End',
                    'dirty' => 'Vendor \
    Package\
        Sub		\
            Deeperlevel\
                End',
                ],
            ],
            'namespace-with-comments-and-annotations' => [
                'testMarker' => '/* testNamespaceWithCommentsWhitespaceAndAnnotations */',
                'expected'   => [
                    'clean' => 'Vendor\Package\Sublevel\Deeper\End',
                    'dirty' => 'Vendor\/*comment*/
    Package\Sublevel  \ //phpcs:ignore Standard.Category.Sniff -- for reasons.
            Deeper\ // Another comment
                End',
                ],
            ],
            'namespace-operator' => [
                'testMarker' => '/* testNamespaceOperator */',
                'expected'   => [
                    'clean' => false,
                    'dirty' => false,
                ],
                'skipOnPHP8' => true,
            ],
            'parse-error-reserved-keywords' => [
                'testMarker' => '/* testParseErrorReservedKeywords */',
                'expected'   => [
                    'clean' => 'Vendor\while\Package\protected\name\try\this',
                    'dirty' => 'Vendor\while\Package\protected\name\try\this',
                ],
            ],
            'parse-error-semicolon' => [
                'testMarker' => '/* testParseErrorSemiColon */',
                'expected'   => [
                    'clean' => false,
                    'dirty' => false,
                ],
            ],
            'live-coding' => [
                'testMarker' => '/* testLiveCoding */',
                'expected'   => [
                    'clean' => false,
                    'dirty' => false,
                ],
            ],
        ];
    }
}
