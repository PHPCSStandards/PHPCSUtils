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
 * @covers \PHPCSUtils\Tokens\Collections::arrayOpenTokensBC
 *
 * @since 1.0.2
 */
final class ArrayOpenTokensBCTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testArrayOpenTokensBC()
    {
        $expected = [
            \T_ARRAY            => \T_ARRAY,
            \T_OPEN_SHORT_ARRAY => \T_OPEN_SHORT_ARRAY,
        ];

        $this->assertSame($expected, Collections::arrayOpenTokensBC());
    }
}
