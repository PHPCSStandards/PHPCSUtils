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
 * @group namingconventions
 *
 * @since 1.0.0
 */
class IsEqualTest extends TestCase
{

    /**
     * Test whether two arbitrary strings are considered equal for PHP identifier names.
     *
     * @dataProvider dataIsEqual
     *
     * @param string $inputA   The first name.
     * @param string $inputB   The second name.
     * @param array  $expected The expected function output.
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
     * @return array
     */
    public function dataIsEqual()
    {
        return [
            'a-z-0-9-only-same-case' => [
                'abcdefghijklmnopqrstuvwxyz_0123456789',
                'abcdefghijklmnopqrstuvwxyz_0123456789',
                true,
            ],
            'a-z-0-9-only-different-case' => [
                'ABCDEFGHIJKLMNOPQRSTUVWXYZ_0123456789',
                'abcdefghijklmnopqrstuvwxyz_0123456789',
                true,
            ],
            'extended-ascii-same-case' => [
                'ÇüéâäàåçêëèïîìÄÅÉæÆôöòûùÿÖÜ¢áíóúñÑ',
                'ÇüéâäàåçêëèïîìÄÅÉæÆôöòûùÿÖÜ¢áíóúñÑ',
                true,
            ],
            'extended-ascii-different-case' => [
                'ÇüéâäàåçêëèïîìÄÅÉæÆôöòûùÿÖÜ¢áíóúñÑ',
                'çÜÉÂÄÀÅÇÊËÈÏÎÌäåéÆæÔÖÒÛÙŸöü¢ÁÍÓÚÑñ',
                false,
            ],
            'mixed-ascii-extended-ascii-same-case' => [
                'Déjàvü',
                'Déjàvü',
                true,
            ],
            'mixed-ascii-extended-ascii-different-case-only-for-ascii' => [
                'Déjàvü',
                'déJàVü',
                true,
            ],
            'mixed-ascii-extended-ascii-different-case' => [
                'Déjàvü',
                'DÉJÀVÜ',
                false,
            ],
            'emoji-name' => [
                '💩💩💩',
                '💩💩💩',
                true,
            ],
            'invalid-input-but-not-relevant' => [
                true,
                true,
                true,
            ],
        ];
    }
}
