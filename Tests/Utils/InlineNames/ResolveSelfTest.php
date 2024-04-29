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
class ResolveSelfTest extends UtilityMethodTestCase
{

    /**
     * Test receiving an expected exception when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_SELF');
        InlineNames::resolveSelf(self::$phpcsFile, 10000);
    }

    /**
     * Test receiving an expected exception when a non-T_SELF token is passed.
     *
     * @return void
     */
    public function testUnexpectedTokenException()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_SELF');

        $target = $this->getTargetToken('/* testNotSelf */', \T_ECHO);
        InlineNames::resolveSelf(self::$phpcsFile, $target);
    }

    /**
     * Test resolving a T_SELF token to the fully qualified name of the current class/interface/trait.
     *
     * @dataProvider dataResolveSelf
     *
     * @param string $commentString The comment which prefaces the T_NEW token in the test file.
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
    public static function dataResolveSelf()
    {
        return [
            'self-outside-class-context' => [
                '/* testSelfOutsideClassContext */',
                false,
            ],
            'anon-class' => [
                '/* testAnonClass */',
                '',
            ],
        ];
    }
}
