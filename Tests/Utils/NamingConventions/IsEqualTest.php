<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\NamingConventions;

use PHPCSUtils\Utils\NamingConventions;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHPCSUtils\Utils\NamingConventions::isEqual() method.
 *
 * @covers \PHPCSUtils\Utils\NamingConventions::isEqual
 *
 * @since 1.0.0
 */
final class IsEqualTest extends TestCase
{

    /**
     * Test whether two arbitrary strings are considered equal for PHP identifier names.
     *
     * @dataProvider dataIsEqual
     *
     * @param string $inputA   The first name.
     * @param string $inputB   The second name.
     * @param bool   $expected The expected function output.
     *
     * @return void
     */
    public function testIsEqual($inputA, $inputB, $expected)
    {
        $this->assertSame($expected, NamingConventions::isEqual($inputA, $inputB));
    }

    /**
     * Data provider.
     *
     * @see testIsEqual() For the array format.
     *
     * @return array<string, array<string, string|bool>>
     */
    public static function dataIsEqual()
    {
        return [
            'a-z-0-9-only-same-case' => [
                'inputA'   => 'abcdefghijklmnopqrstuvwxyz_0123456789',
                'inputB'   => 'abcdefghijklmnopqrstuvwxyz_0123456789',
                'expected' => true,
            ],
            'a-z-0-9-only-different-case' => [
                'inputA'   => 'ABCDEFGHIJKLMNOPQRSTUVWXYZ_0123456789',
                'inputB'   => 'abcdefghijklmnopqrstuvwxyz_0123456789',
                'expected' => true,
            ],
            'extended-ascii-same-case' => [
                'inputA'   => 'ÇüéâäàåçêëèïîìÄÅÉæÆôöòûùÿÖÜ¢áíóúñÑ',
                'inputB'   => 'ÇüéâäàåçêëèïîìÄÅÉæÆôöòûùÿÖÜ¢áíóúñÑ',
                'expected' => true,
            ],
            'extended-ascii-different-case' => [
                'inputA'   => 'ÇüéâäàåçêëèïîìÄÅÉæÆôöòûùÿÖÜ¢áíóúñÑ',
                'inputB'   => 'çÜÉÂÄÀÅÇÊËÈÏÎÌäåéÆæÔÖÒÛÙŸöü¢ÁÍÓÚÑñ',
                'expected' => false,
            ],
            'mixed-ascii-extended-ascii-same-case' => [
                'inputA'   => 'Déjàvü',
                'inputB'   => 'Déjàvü',
                'expected' => true,
            ],
            'mixed-ascii-extended-ascii-different-case-only-for-ascii' => [
                'inputA'   => 'Déjàvü',
                'inputB'   => 'déJàVü',
                'expected' => true,
            ],
            'mixed-ascii-extended-ascii-different-case' => [
                'inputA'   => 'Déjàvü',
                'inputB'   => 'DÉJÀVÜ',
                'expected' => false,
            ],
            'emoji-name' => [
                'inputA'   => '💩💩💩',
                'inputB'   => '💩💩💩',
                'expected' => true,
            ],
            'invalid-input-but-not-relevant' => [
                'inputA'   => true,
                'inputB'   => true,
                'expected' => true,
            ],
        ];
    }
}
