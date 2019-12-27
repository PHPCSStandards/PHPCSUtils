<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\BackCompat\BCTokens;

use PHPCSUtils\BackCompat\BCTokens;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\BackCompat\BCTokens::parenthesisOpeners
 *
 * @group tokens
 *
 * @since 1.0.0
 */
class ParenthesisOpenersTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testParenthesisOpeners()
    {
        $expected = [
            \T_ARRAY      => \T_ARRAY,
            \T_LIST       => \T_LIST,
            \T_FUNCTION   => \T_FUNCTION,
            \T_CLOSURE    => \T_CLOSURE,
            \T_ANON_CLASS => \T_ANON_CLASS,
            \T_WHILE      => \T_WHILE,
            \T_FOR        => \T_FOR,
            \T_FOREACH    => \T_FOREACH,
            \T_SWITCH     => \T_SWITCH,
            \T_IF         => \T_IF,
            \T_ELSEIF     => \T_ELSEIF,
            \T_CATCH      => \T_CATCH,
            \T_DECLARE    => \T_DECLARE,
        ];

        \asort($expected);

        $result = BCTokens::parenthesisOpeners();
        \asort($result);

        $this->assertSame($expected, $result);
    }
}
