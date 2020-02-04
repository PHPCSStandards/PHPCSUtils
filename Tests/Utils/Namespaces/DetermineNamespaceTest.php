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
 * Tests for the \PHPCSUtils\Utils\Namespaces::findNamespacePtr() and
 * \PHPCSUtils\Utils\Namespaces::determineNamespace() methods.
 *
 * @covers \PHPCSUtils\Utils\Namespaces::findNamespacePtr
 * @covers \PHPCSUtils\Utils\Namespaces::determineNamespace
 *
 * @group namespaces
 *
 * @since 1.0.0
 */
class DetermineNamespaceTest extends UtilityMethodTestCase
{

    /**
     * Test that false is returned when an invalid token is passed.
     *
     * @return void
     */
    public function testInvalidTokenPassed()
    {
        $this->assertFalse(Namespaces::findNamespacePtr(self::$phpcsFile, 100000));
    }

    /**
     * Test finding the correct namespace token for an arbitrary token in a file.
     *
     * @dataProvider dataDetermineNamespace
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected output for the functions.
     *
     * @return void
     */
    public function testFindNamespacePtr($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_ECHO);

        if ($expected['ptr'] !== false) {
            $expected['ptr'] = $this->getTargetToken($expected['ptr'], \T_NAMESPACE);
        }

        $result = Namespaces::findNamespacePtr(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected['ptr'], $result);
    }

    /**
     * Test retrieving the applicable namespace name for an arbitrary token in a file.
     *
     * @dataProvider dataDetermineNamespace
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected output for the functions.
     *
     * @return void
     */
    public function testDetermineNamespace($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_ECHO);
        $result   = Namespaces::determineNamespace(self::$phpcsFile, $stackPtr);

        $this->assertSame($expected['name'], $result);
    }

    /**
     * Data provider.
     *
     * @see testDetermineNamespace() For the array format.
     *
     * @return array
     */
    public function dataDetermineNamespace()
    {
        return [
            'no-namespace' => [
                '/* testNoNamespace */',
                [
                    'ptr'  => false,
                    'name' => '',
                ],
            ],
            'no-namespace-nested' => [
                '/* testNoNamespaceNested */',
                [
                    'ptr'  => false,
                    'name' => '',
                ],
            ],
            'non-scoped-namespace-1' => [
                '/* testNonScopedNamedNamespace1 */',
                [
                    'ptr'  => '/* Non-scoped named namespace 1 */',
                    'name' => 'Vendor\Package\Baz',
                ],
            ],
            'non-scoped-namespace-1-nested' => [
                '/* testNonScopedNamedNamespace1Nested */',
                [
                    'ptr'  => '/* Non-scoped named namespace 1 */',
                    'name' => 'Vendor\Package\Baz',
                ],
            ],
            'global-namespace-scoped' => [
                '/* testGlobalNamespaceScoped */',
                [
                    'ptr'  => '/* Scoped global namespace */',
                    'name' => '',
                ],
            ],
            'global-namespace-scoped-nested' => [
                '/* testGlobalNamespaceScopedNested */',
                [
                    'ptr'  => '/* Scoped global namespace */',
                    'name' => '',
                ],
            ],
            'no-namespace-after-unnamed-scoped' => [
                '/* testNoNamespaceAfterUnnamedScoped */',
                [
                    'ptr'  => false,
                    'name' => '',
                ],
            ],
            'no-namespace-nested-after-unnamed-scoped' => [
                '/* testNoNamespaceNestedAfterUnnamedScoped */',
                [
                    'ptr'  => false,
                    'name' => '',
                ],
            ],
            'named-namespace-scoped' => [
                '/* testNamedNamespaceScoped */',
                [
                    'ptr'  => '/* Scoped named namespace */',
                    'name' => 'Vendor\Package\Foo',
                ],
            ],
            'named-namespace-scoped-nested' => [
                '/* testNamedNamespaceScopedNested */',
                [
                    'ptr'  => '/* Scoped named namespace */',
                    'name' => 'Vendor\Package\Foo',
                ],
            ],
            'no-namespace-after-named-scoped' => [
                '/* testNoNamespaceAfterNamedScoped */',
                [
                    'ptr'  => false,
                    'name' => '',
                ],
            ],
            'no-namespace-nested-after-named-scoped' => [
                '/* testNoNamespaceNestedAfterNamedScoped */',
                [
                    'ptr'  => false,
                    'name' => '',
                ],
            ],
            'non-scoped-namespace-2' => [
                '/* testNonScopedNamedNamespace2 */',
                [
                    'ptr'  => '/* Non-scoped named namespace 2 */',
                    'name' => 'Vendor\Package\Foz',
                ],
            ],
            'non-scoped-namespace-2-nested' => [
                '/* testNonScopedNamedNamespace2Nested */',
                [
                    'ptr'  => '/* Non-scoped named namespace 2 */',
                    'name' => 'Vendor\Package\Foz',
                ],
            ],
        ];
    }

    /**
     * Test that the namespace declaration itself is not regarded as being namespaced.
     *
     * @return void
     */
    public function testNamespaceDeclarationIsNotNamespaced()
    {
        $stackPtr = $this->getTargetToken('/* Non-scoped named namespace 2 */', \T_NAMESPACE);
        $result   = Namespaces::findNamespacePtr(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result, 'Failed checking that namespace declaration token is not regarded as namespaced');

        $stackPtr = $this->getTargetToken('/* Non-scoped named namespace 2 */', \T_STRING, 'Package');
        $result   = Namespaces::findNamespacePtr(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result, 'Failed checking that a token in the namespace name is not regarded as namespaced');
    }

    /**
     * Test returning an empty string if the namespace could not be determined (parse error).
     *
     * @return void
     */
    public function testFallbackToEmptyString()
    {
        $stackPtr = $this->getTargetToken('/* testParseError */', \T_COMMENT, '/* comment */');

        $expected = $this->getTargetToken('/* testParseError */', \T_NAMESPACE);
        $result   = Namespaces::findNamespacePtr(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result, 'Failed test with findNamespacePtr');

        $result = Namespaces::determineNamespace(self::$phpcsFile, $stackPtr, false);
        $this->assertSame('', $result, 'Failed test with determineNamespace');
    }
}
