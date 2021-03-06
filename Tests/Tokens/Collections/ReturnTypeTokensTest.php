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
 * @covers \PHPCSUtils\Tokens\Collections::returnTypeTokens
 *
 * @group collections
 *
 * @since 1.0.0
 */
class ReturnTypeTokensTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testReturnTypeTokens()
    {
        $version  = Helper::getVersion();
        $expected = [
            \T_CALLABLE     => \T_CALLABLE,
            \T_SELF         => \T_SELF,
            \T_PARENT       => \T_PARENT,
            \T_STATIC       => \T_STATIC,
            \T_FALSE        => \T_FALSE,
            \T_NULL         => \T_NULL,
            \T_ARRAY        => \T_ARRAY,
            \T_BITWISE_OR   => \T_BITWISE_OR,
            \T_NS_SEPARATOR => \T_NS_SEPARATOR,
            \T_NAMESPACE    => \T_NAMESPACE,
            \T_STRING       => \T_STRING,
        ];

        if (\version_compare(\PHP_VERSION_ID, '80000', '>=') === true
            || \version_compare($version, '3.5.7', '>=') === true
        ) {
            $expected[\T_NAME_QUALIFIED]       = \T_NAME_QUALIFIED;
            $expected[\T_NAME_FULLY_QUALIFIED] = \T_NAME_FULLY_QUALIFIED;
            $expected[\T_NAME_RELATIVE]        = \T_NAME_RELATIVE;
        }

        if (\version_compare($version, '3.6.0', '>=') === true) {
            $expected[\T_TYPE_UNION] = \T_TYPE_UNION;
        }

        $this->assertSame($expected, Collections::returnTypeTokens());
    }
}
