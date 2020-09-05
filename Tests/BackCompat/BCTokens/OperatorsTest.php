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
 * @covers \PHPCSUtils\BackCompat\BCTokens::operators
 *
 * @group tokens
 *
 * @since 1.0.0
 */
class OperatorsTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testOperators()
    {
        $version  = Helper::getVersion();
        $expected = [
            \T_MINUS       => \T_MINUS,
            \T_PLUS        => \T_PLUS,
            \T_MULTIPLY    => \T_MULTIPLY,
            \T_DIVIDE      => \T_DIVIDE,
            \T_MODULUS     => \T_MODULUS,
            \T_POW         => \T_POW,
            \T_SPACESHIP   => \T_SPACESHIP,
            \T_BITWISE_AND => \T_BITWISE_AND,
            \T_BITWISE_OR  => \T_BITWISE_OR,
            \T_BITWISE_XOR => \T_BITWISE_XOR,
            \T_SL          => \T_SL,
            \T_SR          => \T_SR,
        ];

        if (\version_compare($version, '2.6.1', '>=') === true
            || \version_compare(\PHP_VERSION_ID, '60999', '>=') === true
        ) {
            $expected[\T_COALESCE] = \T_COALESCE;
        }

        \asort($expected);

        $result = BCTokens::operators();
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
    public function testPHPCSOperators()
    {
        $this->assertSame(Tokens::$operators, BCTokens::operators());
    }
}
