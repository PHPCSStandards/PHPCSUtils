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
 * Tests for the \PHPCSUtils\Utils\Context::inIsset() method.
 *
 * @covers \PHPCSUtils\Utils\Context::inIsset
 *
 * @since 1.0.0
 */
final class InIssetTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(Context::inIsset(self::$phpcsFile, 10000));
    }

    /**
     * Test correctly identifying that an arbitrary token is within an isset().
     *
     * @dataProvider dataInIsset
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   The expected function return value.
     *
     * @return void
     */
    public function testInIsset($testMarker, $expected)
    {
        $target = $this->getTargetToken($testMarker, \T_VARIABLE, '$target');
        $this->assertSame($expected, Context::inIsset(self::$phpcsFile, $target));
    }

    /**
     * Data provider.
     *
     * @see testInIsset()
     *
     * @return array<string, array<string, string|bool>>
     */
    public static function dataInIsset()
    {
        return [
            'method-called-isset' => [
                'testMarker' => '/* testNotIssetMethodCall */',
                'expected'   => false,
            ],
            'owner-not-isset' => [
                'testMarker' => '/* testOwnerNotIsset */',
                'expected'   => false,
            ],
            'in-isset' => [
                'testMarker' => '/* testInIsset */',
                'expected'   => true,
            ],
            'in-isset-nested' => [
                'testMarker' => '/* testInIssetnested */',
                'expected'   => true,
            ],
            'parse-error' => [
                'testMarker' => '/* testParseError */',
                'expected'   => false,
            ],
        ];
    }
}
