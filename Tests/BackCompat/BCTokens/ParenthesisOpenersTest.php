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
use PHPCSUtils\BackCompat\Helper;
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
final class ParenthesisOpenersTest extends TestCase
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
            \T_USE        => \T_USE,
            \T_ANON_CLASS => \T_ANON_CLASS,
            \T_WHILE      => \T_WHILE,
            \T_FOR        => \T_FOR,
            \T_FOREACH    => \T_FOREACH,
            \T_SWITCH     => \T_SWITCH,
            \T_IF         => \T_IF,
            \T_ELSEIF     => \T_ELSEIF,
            \T_CATCH      => \T_CATCH,
            \T_DECLARE    => \T_DECLARE,
            \T_MATCH      => \T_MATCH,
            \T_ISSET      => \T_ISSET,
            \T_EMPTY      => \T_EMPTY,
            \T_UNSET      => \T_UNSET,
            \T_EVAL       => \T_EVAL,
            \T_EXIT       => \T_EXIT,
        ];

        \asort($expected);

        $result = BCTokens::parenthesisOpeners();
        \asort($result);

        $this->assertSame($expected, $result);
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
    public function testPHPCSParenthesisOpeners()
    {
        $version = Helper::getVersion();

        if (\version_compare($version, '3.99.99', '>') === true) {
            $this->assertSame(Tokens::$parenthesisOpeners, BCTokens::parenthesisOpeners());
        } else {
            /*
             * Don't fail this test on the difference between PHPCS 4.x and 3.x.
             * This test is only run against `dev-master` and `dev-master` is still PHPCS 3.x.
             */
            $expected           = Tokens::$parenthesisOpeners;
            $expected[\T_USE]   = \T_USE;
            $expected[\T_ISSET] = \T_ISSET;
            $expected[\T_EMPTY] = \T_EMPTY;
            $expected[\T_UNSET] = \T_UNSET;
            $expected[\T_EVAL]  = \T_EVAL;
            $expected[\T_EXIT]  = \T_EXIT;

            \asort($expected);

            $result = BCTokens::parenthesisOpeners();
            \asort($result);

            $this->assertSame($expected, $result);
        }
    }
}
