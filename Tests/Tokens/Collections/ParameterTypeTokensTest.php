<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Tokens\Collections;

use PHPCSUtils\Tokens\Collections;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Tokens\Collections::parameterTypeTokens
 *
 * @group collections
 *
 * @since 1.0.0
 */
class ParameterTypeTokensTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testParameterTypeTokens()
    {
        $expected = [
            \T_CALLABLE     => \T_CALLABLE,
            \T_SELF         => \T_SELF,
            \T_PARENT       => \T_PARENT,
            \T_FALSE        => \T_FALSE,
            \T_NULL         => \T_NULL,
            \T_STRING       => \T_STRING,
            \T_NAMESPACE    => \T_NAMESPACE,
            \T_NS_SEPARATOR => \T_NS_SEPARATOR,
            \T_BITWISE_OR   => \T_BITWISE_OR,
        ];

        $this->assertSame($expected, Collections::parameterTypeTokens());
    }
}
