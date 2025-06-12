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
 * @covers \PHPCSUtils\BackCompat\BCTokens::scopeOpeners
 *
 * @group tokens
 *
 * @since 1.1.0
 */
final class ScopeOpenersTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testScopeOpeners()
    {
        $expected = [
            \T_CLASS      => \T_CLASS,
            \T_ANON_CLASS => \T_ANON_CLASS,
            \T_INTERFACE  => \T_INTERFACE,
            \T_TRAIT      => \T_TRAIT,
            \T_ENUM       => \T_ENUM,
            \T_NAMESPACE  => \T_NAMESPACE,
            \T_FUNCTION   => \T_FUNCTION,
            \T_CLOSURE    => \T_CLOSURE,
            \T_IF         => \T_IF,
            \T_SWITCH     => \T_SWITCH,
            \T_CASE       => \T_CASE,
            \T_DECLARE    => \T_DECLARE,
            \T_DEFAULT    => \T_DEFAULT,
            \T_WHILE      => \T_WHILE,
            \T_ELSE       => \T_ELSE,
            \T_ELSEIF     => \T_ELSEIF,
            \T_FOR        => \T_FOR,
            \T_FOREACH    => \T_FOREACH,
            \T_DO         => \T_DO,
            \T_TRY        => \T_TRY,
            \T_CATCH      => \T_CATCH,
            \T_FINALLY    => \T_FINALLY,
            \T_USE        => \T_USE,
            \T_MATCH      => \T_MATCH,
        ];

        $this->assertSame($expected, BCTokens::scopeOpeners());
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
    public function testPHPCSScopeOpeners()
    {
        $version = Helper::getVersion();

        if (\version_compare($version, '3.99.99', '>') === true) {
            $this->assertSame(Tokens::$scopeOpeners, BCTokens::scopeOpeners());
        } else {
            /*
             * Don't fail this test on the difference between PHPCS 4.x and 3.x.
             * This test is only run against `dev-master` and `dev-master` is still PHPCS 3.x.
             */
            $expected = Tokens::$scopeOpeners;
            unset($expected[\T_PROPERTY], $expected[\T_OBJECT]);

            $result = BCTokens::scopeOpeners();

            $this->assertSame($expected, $result);
        }
    }
}
