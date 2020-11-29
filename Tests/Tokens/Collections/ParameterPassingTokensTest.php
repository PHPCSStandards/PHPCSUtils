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
 * @covers \PHPCSUtils\Tokens\Collections::parameterPassingTokens
 *
 * @group collections
 *
 * @since 1.0.0
 */
class ParameterPassingTokensTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testParameterPassingTokens()
    {
        $version  = Helper::getVersion();
        $expected = [
            \T_STRING => \T_STRING,
        ];

        if (\version_compare(\PHP_VERSION_ID, '80000', '>=') === true
            || (\version_compare($version, '3.5.7', '>=') === true
                && \version_compare($version, '4.0.0', '<') === true)
        ) {
            $expected[\T_NAME_QUALIFIED]       = \T_NAME_QUALIFIED;
            $expected[\T_NAME_FULLY_QUALIFIED] = \T_NAME_FULLY_QUALIFIED;
            $expected[\T_NAME_RELATIVE]        = \T_NAME_RELATIVE;
        }

        $expected += [
            \T_VARIABLE            => \T_VARIABLE,
            \T_SELF                => \T_SELF,
            \T_STATIC              => \T_STATIC,
            \T_ISSET               => \T_ISSET,
            \T_UNSET               => \T_UNSET,
            \T_ARRAY               => \T_ARRAY,
            \T_OPEN_SHORT_ARRAY    => \T_OPEN_SHORT_ARRAY,
            \T_OPEN_SQUARE_BRACKET => \T_OPEN_SQUARE_BRACKET,
        ];

        $this->assertSame($expected, Collections::parameterPassingTokens());
    }
}
