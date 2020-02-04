<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\BackCompat\BCTokens;

use PHPCSUtils\BackCompat\BCTokens;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\BackCompat\BCTokens::arithmeticTokens
 *
 * @group tokens
 *
 * @since 1.0.0
 */
class ArithmeticTokensTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testArithmeticTokens()
    {
        $expected = [
            \T_PLUS     => \T_PLUS,
            \T_MINUS    => \T_MINUS,
            \T_MULTIPLY => \T_MULTIPLY,
            \T_DIVIDE   => \T_DIVIDE,
            \T_MODULUS  => \T_MODULUS,
            \T_POW      => \T_POW,
        ];

        $this->assertSame($expected, BCTokens::arithmeticTokens());
    }
}
