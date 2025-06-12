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
 * @covers \PHPCSUtils\Tokens\Collections::parameterPassingTokens
 *
 * @since 1.0.0
 */
final class ParameterPassingTokensTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testParameterPassingTokens()
    {
        $expected = [
            \T_STRING               => \T_STRING,
            \T_NAME_QUALIFIED       => \T_NAME_QUALIFIED,
            \T_NAME_FULLY_QUALIFIED => \T_NAME_FULLY_QUALIFIED,
            \T_NAME_RELATIVE        => \T_NAME_RELATIVE,
            \T_VARIABLE             => \T_VARIABLE,
            \T_ANON_CLASS           => \T_ANON_CLASS,
            \T_PARENT               => \T_PARENT,
            \T_SELF                 => \T_SELF,
            \T_STATIC               => \T_STATIC,
            \T_EXIT                 => \T_EXIT,
            \T_ISSET                => \T_ISSET,
            \T_UNSET                => \T_UNSET,
            \T_ARRAY                => \T_ARRAY,
            \T_OPEN_SHORT_ARRAY     => \T_OPEN_SHORT_ARRAY,
        ];

        $this->assertSame($expected, Collections::parameterPassingTokens());
    }
}
