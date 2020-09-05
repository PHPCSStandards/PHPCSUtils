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

use PHP_CodeSniffer\Util\Tokens;
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

    /**
     * Test whether the method in BCTokens is still in sync with the latest version of PHPCS.
     *
     * This group is not run by default and has to be specifically requested to be run.
     *
     * @group compareWithPHPCS
     *
     * @return void
     */
    public function testPHPCSArithmeticTokens()
    {
        $this->assertSame(Tokens::$arithmeticTokens, BCTokens::arithmeticTokens());
    }
}
