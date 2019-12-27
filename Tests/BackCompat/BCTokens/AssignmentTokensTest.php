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
use PHPCSUtils\BackCompat\Helper;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\BackCompat\BCTokens::assignmentTokens
 *
 * @group tokens
 *
 * @since 1.0.0
 */
class AssignmentTokensTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testAssignmentTokens()
    {
        $version  = Helper::getVersion();
        $expected = [
            \T_EQUAL        => \T_EQUAL,
            \T_AND_EQUAL    => \T_AND_EQUAL,
            \T_OR_EQUAL     => \T_OR_EQUAL,
            \T_CONCAT_EQUAL => \T_CONCAT_EQUAL,
            \T_DIV_EQUAL    => \T_DIV_EQUAL,
            \T_MINUS_EQUAL  => \T_MINUS_EQUAL,
            \T_POW_EQUAL    => \T_POW_EQUAL,
            \T_MOD_EQUAL    => \T_MOD_EQUAL,
            \T_MUL_EQUAL    => \T_MUL_EQUAL,
            \T_PLUS_EQUAL   => \T_PLUS_EQUAL,
            \T_XOR_EQUAL    => \T_XOR_EQUAL,
            \T_DOUBLE_ARROW => \T_DOUBLE_ARROW,
            \T_SL_EQUAL     => \T_SL_EQUAL,
            \T_SR_EQUAL     => \T_SR_EQUAL,
        ];

        if (\version_compare($version, '2.8.1', '>=') === true
            || \version_compare(\PHP_VERSION_ID, '70399', '>=') === true
        ) {
            $expected[\T_COALESCE_EQUAL] = \T_COALESCE_EQUAL;
        }

        if (\version_compare($version, '2.8.0', '>=') === true) {
            $expected[\T_ZSR_EQUAL] = \T_ZSR_EQUAL;
        }

        $this->assertSame($expected, BCTokens::assignmentTokens());
    }
}
