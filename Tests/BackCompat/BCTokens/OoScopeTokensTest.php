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
 * @covers \PHPCSUtils\BackCompat\BCTokens::ooScopeTokens
 *
 * @group tokens
 *
 * @since 1.0.0
 */
class OoScopeTokensTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testOoScopeTokens()
    {
        $expected = [
            \T_CLASS      => \T_CLASS,
            \T_ANON_CLASS => \T_ANON_CLASS,
            \T_INTERFACE  => \T_INTERFACE,
            \T_TRAIT      => \T_TRAIT,
        ];

        $this->assertSame($expected, BCTokens::ooScopeTokens());
    }
}
