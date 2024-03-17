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
 * Tests for the \PHPCSUtils\Utils\Context::inEmpty() method.
 *
 * @covers \PHPCSUtils\Utils\Context::inEmpty
 *
 * @since 1.0.0
 */
final class InEmptyTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(Context::inEmpty(self::$phpcsFile, 10000));
    }

    /**
     * Test correctly identifying that an arbitrary token is within an empty().
     *
     * @dataProvider dataInEmpty
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   The expected function return value.
     *
     * @return void
     */
    public function testInEmpty($testMarker, $expected)
    {
        $target = $this->getTargetToken($testMarker, \T_VARIABLE, '$target');
        $this->assertSame($expected, Context::inEmpty(self::$phpcsFile, $target));
    }

    /**
     * Data provider.
     *
     * @see testInEmpty()
     *
     * @return array<string, array<string, string|bool>>
     */
    public static function dataInEmpty()
    {
        return [
            'method-called-empty' => [
                'testMarker' => '/* testNotEmptyMethodCall */',
                'expected'   => false,
            ],
            'owner-not-empty' => [
                'testMarker' => '/* testOwnerNotEmpty */',
                'expected'   => false,
            ],
            'in-empty' => [
                'testMarker' => '/* testInEmpty */',
                'expected'   => true,
            ],
            'in-empty-nested' => [
                'testMarker' => '/* testInEmptynested */',
                'expected'   => true,
            ],
            'parse-error' => [
                'testMarker' => '/* testParseError */',
                'expected'   => false,
            ],
        ];
    }
}
