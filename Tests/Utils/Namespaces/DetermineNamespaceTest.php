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

use PHPCSUtils\Internal\Cache;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Namespaces;

/**
 * Tests for the \PHPCSUtils\Utils\Namespaces::findNamespacePtr() and
 * \PHPCSUtils\Utils\Namespaces::determineNamespace() methods.
 *
 * @covers \PHPCSUtils\Utils\Namespaces::findNamespacePtr
 * @covers \PHPCSUtils\Utils\Namespaces::determineNamespace
 *
 * @since 1.0.0
 */
final class DetermineNamespaceTest extends UtilityMethodTestCase
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
     * @param string                      $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, string|false> $expected   The expected output for the functions.
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
     * @param string                      $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, string|false> $expected   The expected output for the functions.
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
     * @return array<string, array<string, string|array<string, string|false>>>
     */
    public static function dataDetermineNamespace()
    {
        return [
            'no-namespace' => [
                'testMarker' => '/* testNoNamespace */',
                'expected'   => [
                    'ptr'  => false,
                    'name' => '',
                ],
            ],
            'no-namespace-nested' => [
                'testMarker' => '/* testNoNamespaceNested */',
                'expected'   => [
                    'ptr'  => false,
                    'name' => '',
                ],
            ],
            'non-scoped-namespace-1' => [
                'testMarker' => '/* testNonScopedNamedNamespace1 */',
                'expected'   => [
                    'ptr'  => '/* Non-scoped named namespace 1 */',
                    'name' => 'Vendor\Package\Baz',
                ],
            ],
            'non-scoped-namespace-1-nested' => [
                'testMarker' => '/* testNonScopedNamedNamespace1Nested */',
                'expected'   => [
                    'ptr'  => '/* Non-scoped named namespace 1 */',
                    'name' => 'Vendor\Package\Baz',
                ],
            ],
            'global-namespace-scoped' => [
                'testMarker' => '/* testGlobalNamespaceScoped */',
                'expected'   => [
                    'ptr'  => '/* Scoped global namespace */',
                    'name' => '',
                ],
            ],
            'global-namespace-scoped-nested' => [
                'testMarker' => '/* testGlobalNamespaceScopedNested */',
                'expected'   => [
                    'ptr'  => '/* Scoped global namespace */',
                    'name' => '',
                ],
            ],
            'no-namespace-after-unnamed-scoped' => [
                'testMarker' => '/* testNoNamespaceAfterUnnamedScoped */',
                'expected'   => [
                    'ptr'  => false,
                    'name' => '',
                ],
            ],
            'no-namespace-nested-after-unnamed-scoped' => [
                'testMarker' => '/* testNoNamespaceNestedAfterUnnamedScoped */',
                'expected'   => [
                    'ptr'  => false,
                    'name' => '',
                ],
            ],
            'named-namespace-scoped' => [
                'testMarker' => '/* testNamedNamespaceScoped */',
                'expected'   => [
                    'ptr'  => '/* Scoped named namespace */',
                    'name' => 'Vendor\Package\Foo',
                ],
            ],
            'named-namespace-scoped-nested' => [
                'testMarker' => '/* testNamedNamespaceScopedNested */',
                'expected'   => [
                    'ptr'  => '/* Scoped named namespace */',
                    'name' => 'Vendor\Package\Foo',
                ],
            ],
            'no-namespace-after-named-scoped' => [
                'testMarker' => '/* testNoNamespaceAfterNamedScoped */',
                'expected'   => [
                    'ptr'  => false,
                    'name' => '',
                ],
            ],
            'no-namespace-nested-after-named-scoped' => [
                'testMarker' => '/* testNoNamespaceNestedAfterNamedScoped */',
                'expected'   => [
                    'ptr'  => false,
                    'name' => '',
                ],
            ],
            'non-scoped-namespace-2' => [
                'testMarker' => '/* testNonScopedNamedNamespace2 */',
                'expected'   => [
                    'ptr'  => '/* Non-scoped named namespace 2 */',
                    'name' => 'Vendor\Package\Foz',
                ],
            ],
            'non-scoped-namespace-2-nested' => [
                'testMarker' => '/* testNonScopedNamedNamespace2Nested */',
                'expected'   => [
                    'ptr'  => '/* Non-scoped named namespace 2 */',
                    'name' => 'Vendor\Package\Foz',
                ],
            ],
        ];
    }

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @return void
     */
    public function testFindNamespacePtrResultIsCached()
    {
        // The test case used is specifically selected to be one which will always reach the cache check.
        $methodName = 'PHPCSUtils\\Utils\\Namespaces::findNamespacePtr';
        $cases      = $this->dataDetermineNamespace();
        $testMarker = $cases['non-scoped-namespace-2']['testMarker'];
        $expected   = $cases['non-scoped-namespace-2']['expected']['ptr'];

        $stackPtr = $this->getTargetToken($testMarker, \T_ECHO);
        $expected = $this->getTargetToken($expected, \T_NAMESPACE);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = Namespaces::findNamespacePtr(self::$phpcsFile, $stackPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, $stackPtr);
        $resultSecondRun = Namespaces::findNamespacePtr(self::$phpcsFile, $stackPtr);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
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

        $targetType    = \T_STRING;
        $targetContent = 'Package';
        if (parent::usesPhp8NameTokens() === true) {
            $targetType    = \T_NAME_QUALIFIED;
            $targetContent = 'Vendor\Package\Foz';
        }

        $stackPtr = $this->getTargetToken('/* Non-scoped named namespace 2 */', $targetType, $targetContent);
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

        $result = Namespaces::determineNamespace(self::$phpcsFile, $stackPtr);
        $this->assertSame('', $result, 'Failed test with determineNamespace');
    }
}
