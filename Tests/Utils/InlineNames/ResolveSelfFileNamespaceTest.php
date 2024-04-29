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
class ResolveSelfFileNamespaceTest extends UtilityMethodTestCase
{

    /**
     * Test resolving a T_SELF token to the fully qualified name of the current class/interface/trait
     * in a file with a file-based namespace declaration.
     *
     * @dataProvider dataResolveSelf
     *
     * @param string $commentString     The comment which prefaces the T_NEW token in the test file.
     * @param string $expectedWithCall  The expected function return value when the namespace is determined
     *                                  via function call.
     * @param string $expectedWithParam The expected function return value when the namespace is passed in.
     *
     * @return void
     */
    public function testResolveSelf($commentString, $expectedWithCall, $expectedWithParam)
    {
        $stackPtr = $this->getTargetToken($commentString, \T_SELF);

        $result = InlineNames::resolveSelf(self::$phpcsFile, $stackPtr);
        $this->assertSame($expectedWithCall, $result, 'Failed with namespace determination via function call');

        $result = InlineNames::resolveSelf(self::$phpcsFile, $stackPtr, 'Fake\NS');
        $this->assertSame($expectedWithParam, $result, 'Failed with namespace passed as parameter');
    }

    /**
     * Data provider.
     *
     * @see testResolveSelf() For the array format.
     *
     * @return array
     */
    public static function dataResolveSelf()
    {
        return [
            'namespaced-class' => [
                '/* testNamespacedClass */',
                '\Some\NamespaceName\Sub\Foo',
                '\Fake\NS\Foo',
            ],
            'nested-anon-class' => [
                '/* testNestedAnonClass */',
                '',
                '',
            ],
        ];
    }
}
