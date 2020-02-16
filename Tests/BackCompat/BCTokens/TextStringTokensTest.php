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

use PHPCSUtils\BackCompat\BCTokens;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\BackCompat\BCTokens::textStringTokens
 *
 * @group tokens
 *
 * @since 1.0.0
 */
class TextStringTokensTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testTextStringTokens()
    {
        $expected = [
            \T_CONSTANT_ENCAPSED_STRING => \T_CONSTANT_ENCAPSED_STRING,
            \T_DOUBLE_QUOTED_STRING     => \T_DOUBLE_QUOTED_STRING,
            \T_INLINE_HTML              => \T_INLINE_HTML,
            \T_HEREDOC                  => \T_HEREDOC,
            \T_NOWDOC                   => \T_NOWDOC,
        ];

        $this->assertSame($expected, BCTokens::textStringTokens());
    }
}
