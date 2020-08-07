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
 * @covers \PHPCSUtils\Tokens\Collections::nullsafeObjectOperatorBC
 *
 * @group collections
 *
 * @since 1.0.0
 */
class NullsafeObjectOperatorBCTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testNullsafeObjectOperatorBC()
    {
        $expected = [];
        if (\version_compare(\PHP_VERSION_ID, '80000', '>=') === true
        ) {
            $expected = [
                \T_NULLSAFE_OBJECT_OPERATOR => \T_NULLSAFE_OBJECT_OPERATOR,
            ];
        } else {
            $expected = [
                \T_INLINE_THEN     => \T_INLINE_THEN,
                \T_OBJECT_OPERATOR => \T_OBJECT_OPERATOR,
            ];
        }

        $this->assertSame($expected, Collections::nullsafeObjectOperatorBC());
    }
}
