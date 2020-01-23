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
 * @covers \PHPCSUtils\BackCompat\BCTokens::functionNameTokens
 *
 * @group tokens
 *
 * @since 1.0.0
 */
class FunctionNameTokensTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testFunctionNameTokens()
    {
        $expected = [
            \T_STRING       => \T_STRING,
            \T_EVAL         => \T_EVAL,
            \T_EXIT         => \T_EXIT,
            \T_INCLUDE      => \T_INCLUDE,
            \T_INCLUDE_ONCE => \T_INCLUDE_ONCE,
            \T_REQUIRE      => \T_REQUIRE,
            \T_REQUIRE_ONCE => \T_REQUIRE_ONCE,
            \T_ISSET        => \T_ISSET,
            \T_UNSET        => \T_UNSET,
            \T_EMPTY        => \T_EMPTY,
            \T_SELF         => \T_SELF,
            \T_STATIC       => \T_STATIC,
        ];

        $this->assertSame($expected, BCTokens::functionNameTokens());
    }
}
