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
 * @covers \PHPCSUtils\BackCompat\BCTokens::functionNameTokens
 *
 * @group tokens
 *
 * @since 1.0.0
 */
final class FunctionNameTokensTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testFunctionNameTokens()
    {
        $expected = [
            \T_STRING               => \T_STRING,
            \T_EVAL                 => \T_EVAL,
            \T_EXIT                 => \T_EXIT,
            \T_INCLUDE              => \T_INCLUDE,
            \T_INCLUDE_ONCE         => \T_INCLUDE_ONCE,
            \T_REQUIRE              => \T_REQUIRE,
            \T_REQUIRE_ONCE         => \T_REQUIRE_ONCE,
            \T_ISSET                => \T_ISSET,
            \T_UNSET                => \T_UNSET,
            \T_EMPTY                => \T_EMPTY,
            \T_SELF                 => \T_SELF,
            \T_STATIC               => \T_STATIC,
            \T_PARENT               => \T_PARENT,
            \T_NAME_QUALIFIED       => \T_NAME_QUALIFIED,
            \T_NAME_FULLY_QUALIFIED => \T_NAME_FULLY_QUALIFIED,
            \T_NAME_RELATIVE        => \T_NAME_RELATIVE,
            \T_ANON_CLASS           => \T_ANON_CLASS,
        ];

        \asort($expected);

        $result = BCTokens::functionNameTokens();
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
    public function testPHPCSFunctionNameTokens()
    {
        $version = Helper::getVersion();

        if (\version_compare($version, '3.99.99', '>') === true) {
            $this->assertSame(Tokens::$functionNameTokens, BCTokens::functionNameTokens());
        } else {
            /*
             * Don't fail this test on the difference between PHPCS 4.x and 3.x.
             * This test is only run against `dev-master` and `dev-master` is still PHPCS 3.x.
             */
            $expected                    = Tokens::$functionNameTokens;
            $expected[\T_NAME_QUALIFIED] = \T_NAME_QUALIFIED;
            $expected[\T_NAME_FULLY_QUALIFIED] = \T_NAME_FULLY_QUALIFIED;
            $expected[\T_NAME_RELATIVE]        = \T_NAME_RELATIVE;
            $expected[\T_ANON_CLASS]           = \T_ANON_CLASS;

            \asort($expected);

            $result = BCTokens::functionNameTokens();
            \asort($result);

            $this->assertSame($expected, $result);
        }
    }
}
