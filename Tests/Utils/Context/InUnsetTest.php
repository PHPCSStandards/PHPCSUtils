<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Context;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Context;

/**
 * Tests for the \PHPCSUtils\Utils\Context::inUnset() method.
 *
 * @covers \PHPCSUtils\Utils\Context::inUnset
 *
 * @since 1.0.0
 */
final class InUnsetTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(Context::inUnset(self::$phpcsFile, 10000));
    }

    /**
     * Test correctly identifying that an arbitrary token is within an unset().
     *
     * @dataProvider dataInUnset
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   The expected function return value.
     *
     * @return void
     */
    public function testInUnset($testMarker, $expected)
    {
        $target = $this->getTargetToken($testMarker, \T_VARIABLE, '$target');
        $this->assertSame($expected, Context::inUnset(self::$phpcsFile, $target));
    }

    /**
     * Data provider.
     *
     * @see testInUnset()
     *
     * @return array<string, array<string, string|bool>>
     */
    public static function dataInUnset()
    {
        return [
            'method-called-unset' => [
                'testMarker' => '/* testNotUnsetMethodCall */',
                'expected'   => false,
            ],
            'owner-not-unset' => [
                'testMarker' => '/* testOwnerNotUnset */',
                'expected'   => false,
            ],
            'in-unset' => [
                'testMarker' => '/* testInUnset */',
                'expected'   => true,
            ],
            'parse-error' => [
                'testMarker' => '/* testParseError */',
                'expected'   => false,
            ],
        ];
    }
}
