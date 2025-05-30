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
 * @covers \PHPCSUtils\BackCompat\BCTokens::assignmentTokens
 *
 * @group tokens
 *
 * @since 1.1.0
 */
final class AssignmentTokensTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testAssignmentTokens()
    {
        $expected = [
            \T_EQUAL          => \T_EQUAL,
            \T_AND_EQUAL      => \T_AND_EQUAL,
            \T_OR_EQUAL       => \T_OR_EQUAL,
            \T_CONCAT_EQUAL   => \T_CONCAT_EQUAL,
            \T_DIV_EQUAL      => \T_DIV_EQUAL,
            \T_MINUS_EQUAL    => \T_MINUS_EQUAL,
            \T_POW_EQUAL      => \T_POW_EQUAL,
            \T_MOD_EQUAL      => \T_MOD_EQUAL,
            \T_MUL_EQUAL      => \T_MUL_EQUAL,
            \T_PLUS_EQUAL     => \T_PLUS_EQUAL,
            \T_XOR_EQUAL      => \T_XOR_EQUAL,
            \T_DOUBLE_ARROW   => \T_DOUBLE_ARROW,
            \T_SL_EQUAL       => \T_SL_EQUAL,
            \T_SR_EQUAL       => \T_SR_EQUAL,
            \T_COALESCE_EQUAL => \T_COALESCE_EQUAL,
        ];

        $this->assertSame($expected, BCTokens::assignmentTokens());
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
    public function testPHPCSAssignmentTokens()
    {
        $version = Helper::getVersion();

        if (\version_compare($version, '3.99.99', '>') === true) {
            $this->assertSame(Tokens::$assignmentTokens, BCTokens::assignmentTokens());
        } else {
            /*
             * Don't fail this test on the difference between PHPCS 4.x and 3.x.
             * This test is only run against `dev-master` and `dev-master` is still PHPCS 3.x.
             */
            $expected = Tokens::$assignmentTokens;
            unset($expected[\T_ZSR_EQUAL]);

            $result = BCTokens::assignmentTokens();

            $this->assertSame($expected, $result);
        }
    }
}
