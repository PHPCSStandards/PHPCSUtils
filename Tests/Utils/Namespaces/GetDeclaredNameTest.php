<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
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
 * @group namespaces
 *
 * @since 1.0.0
 */
class GetDeclaredNameTest extends UtilityMethodTestCase
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
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected output for the function.
     *
     * @return void
     */
    public function testGetDeclaredNameClean($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_NAMESPACE);
        $result   = Namespaces::getDeclaredName(self::$phpcsFile, $stackPtr, true);

        $this->assertSame($expected['clean'], $result);
    }

    /**
     * Test retrieving the "dirty" namespace name based on a T_NAMESPACE token.
     *
     * @dataProvider dataGetDeclaredName
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected output for the function.
     *
     * @return void
     */
    public function testGetDeclaredNameDirty($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_NAMESPACE);
        $result   = Namespaces::getDeclaredName(self::$phpcsFile, $stackPtr, false);

        $this->assertSame($expected['dirty'], $result);
    }

    /**
     * Data provider.
     *
     * @see testGetDeclaredName() For the array format.
     *
     * @return array
     */
    public function dataGetDeclaredName()
    {
        return [
            'global-namespace-curlies' => [
                '/* testGlobalNamespaceCurlies */',
                [
                    'clean' => '',
                    'dirty' => '',
                ],
            ],
            'namespace-semicolon' => [
                '/* testNamespaceSemiColon */',
                [
                    'clean' => 'Vendor',
                    'dirty' => 'Vendor',
                ],
            ],
            'namespace-curlies' => [
                '/* testNamespaceCurlies */',
                [
                    'clean' => 'Vendor\Package\Sublevel\End',
                    'dirty' => 'Vendor\Package\Sublevel\End',
                ],
            ],
            'namespace-curlies-no-space-at-end' => [
                '/* testNamespaceCurliesNoSpaceAtEnd */',
                [
                    'clean' => 'Vendor\Package\Sublevel\Deeperlevel\End',
                    'dirty' => 'Vendor\Package\Sublevel\Deeperlevel\End',
                ],
            ],
            'namespace-close-tag' => [
                '/* testNamespaceCloseTag */',
                [
                    'clean' => 'My\Name',
                    'dirty' => 'My\Name',
                ],
            ],
            'namespace-close-tag-no-space-at-end' => [
                '/* testNamespaceCloseTagNoSpaceAtEnd */',
                [
                    'clean' => 'My\Other\Name',
                    'dirty' => 'My\Other\Name',
                ],
            ],
            'namespace-whitespace-tolerance' => [
                '/* testNamespaceLotsOfWhitespace */',
                [
                    'clean' => 'Vendor\Package\Sub\Deeperlevel\End',
                    'dirty' => 'Vendor \
    Package\
        Sub		\
            Deeperlevel\
                End',
                ],
            ],
            'namespace-with-comments-and-annotations' => [
                '/* testNamespaceWithCommentsWhitespaceAndAnnotations */',
                [
                    'clean' => 'Vendor\Package\Sublevel\Deeper\End',
                    'dirty' => 'Vendor\/*comment*/
    Package\Sublevel  \ //phpcs:ignore Standard.Category.Sniff -- for reasons.
            Deeper\ // Another comment
                End',
                ],
            ],
            'namespace-operator' => [
                '/* testNamespaceOperator */',
                [
                    'clean' => false,
                    'dirty' => false,
                ],
            ],
            'parse-error-reserved-keywords' => [
                '/* testParseErrorReservedKeywords */',
                [
                    'clean' => 'Vendor\while\Package\protected\name\try\this',
                    'dirty' => 'Vendor\while\Package\protected\name\try\this',
                ],
            ],
            'parse-error-semicolon' => [
                '/* testParseErrorSemiColon */',
                [
                    'clean' => false,
                    'dirty' => false,
                ],
            ],
            'live-coding' => [
                '/* testLiveCoding */',
                [
                    'clean' => false,
                    'dirty' => false,
                ],
            ],
        ];
    }
}
