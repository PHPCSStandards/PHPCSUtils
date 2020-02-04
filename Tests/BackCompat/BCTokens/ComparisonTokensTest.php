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
use PHPCSUtils\BackCompat\Helper;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\BackCompat\BCTokens::comparisonTokens
 *
 * @group tokens
 *
 * @since 1.0.0
 */
class ComparisonTokensTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testComparisonTokens()
    {
        $version  = Helper::getVersion();
        $expected = [
            \T_IS_EQUAL            => \T_IS_EQUAL,
            \T_IS_IDENTICAL        => \T_IS_IDENTICAL,
            \T_IS_NOT_EQUAL        => \T_IS_NOT_EQUAL,
            \T_IS_NOT_IDENTICAL    => \T_IS_NOT_IDENTICAL,
            \T_LESS_THAN           => \T_LESS_THAN,
            \T_GREATER_THAN        => \T_GREATER_THAN,
            \T_IS_SMALLER_OR_EQUAL => \T_IS_SMALLER_OR_EQUAL,
            \T_IS_GREATER_OR_EQUAL => \T_IS_GREATER_OR_EQUAL,
            \T_SPACESHIP           => \T_SPACESHIP,
        ];

        if (\version_compare($version, '2.6.1', '>=') === true
            || \version_compare(\PHP_VERSION_ID, '60999', '>=') === true
        ) {
            $expected[\T_COALESCE] = \T_COALESCE;
        }

        $this->assertSame($expected, BCTokens::comparisonTokens());
    }
}
