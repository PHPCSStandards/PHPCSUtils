<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\InlineNames;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\InlineNames;

/**
 * Tests for the \PHPCSUtils\Utils\InlineNames::resolveSelf() method.
 *
 * @covers \PHPCSUtils\Utils\InlineNames::resolveSelf
 *
 * @group inlinenames
 *
 * @since 1.0.0
 */
class ResolveSelfMultiNamespaceTest extends UtilityMethodTestCase
{

    /**
     * Test resolving a T_SELF token to the fully qualified name of the current class/interface/trait
     * in a file with multiple namespace declarations.
     *
     * @dataProvider dataResolveSelf
     *
     * @param string $commentString The comment which prefaces the T_SELF token in the test file.
     * @param string $expected      The expected function return value.
     *
     * @return void
     */
    public function testResolveSelf($commentString, $expected)
    {
        $stackPtr = $this->getTargetToken($commentString, \T_SELF);
        $result   = InlineNames::resolveSelf(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testResolveSelf() For the array format.
     *
     * @return array
     */
    public function dataResolveSelf()
    {
        return [
            'scoped-named-namespace-interface' => [
                '/* testNamespacedInterface */',
                '\Some\NamespaceName\Sub\Foo',
            ],
            'scoped-global-namespace-trait-property-type' => [
                '/* testGlobalTraitPropertyType */',
                '\Foo',
            ],
            'scoped-global-namespace-trait' => [
                '/* testGlobalTrait */',
                '\Foo',
            ],
        ];
    }
}
