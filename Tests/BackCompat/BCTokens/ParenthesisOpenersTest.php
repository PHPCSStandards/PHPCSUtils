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
class ParenthesisOpenersTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testParenthesisOpeners()
    {
        $version  = Helper::getVersion();
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

        if (\version_compare($version, '4.0.0', '>=') === true) {
            $expected[\T_USE] = \T_USE;
        }

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
        $this->assertSame(Tokens::$parenthesisOpeners, BCTokens::parenthesisOpeners());
    }
}
