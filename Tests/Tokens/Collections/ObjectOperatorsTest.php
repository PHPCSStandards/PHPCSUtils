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

use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\Tokens\Collections;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Tokens\Collections::objectOperators
 *
 * @group collections
 *
 * @since 1.0.0
 */
class ObjectOperatorsTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testObjectOperators()
    {
        $version  = Helper::getVersion();
        $expected = [
            \T_OBJECT_OPERATOR => \T_OBJECT_OPERATOR,
            \T_DOUBLE_COLON    => \T_DOUBLE_COLON,
        ];

        if (\version_compare(\PHP_VERSION_ID, '80000', '>=') === true
            || \version_compare($version, '3.5.7', '>=') === true
        ) {
            $expected[\T_NULLSAFE_OBJECT_OPERATOR] = \T_NULLSAFE_OBJECT_OPERATOR;
        }

        $this->assertSame($expected, Collections::objectOperators());
    }
}
