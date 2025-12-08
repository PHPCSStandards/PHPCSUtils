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

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\BackCompat\BCTokens;
use PHPCSUtils\BackCompat\Helper;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\BackCompat\BCTokens::castTokens
 *
 * @group tokens
 *
 * @since 1.2.1
 */
final class CastTokensTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testCastTokens()
    {
        $expected = [
            \T_INT_CAST    => \T_INT_CAST,
            \T_STRING_CAST => \T_STRING_CAST,
            \T_DOUBLE_CAST => \T_DOUBLE_CAST,
            \T_ARRAY_CAST  => \T_ARRAY_CAST,
            \T_BOOL_CAST   => \T_BOOL_CAST,
            \T_OBJECT_CAST => \T_OBJECT_CAST,
            \T_UNSET_CAST  => \T_UNSET_CAST,
            \T_BINARY_CAST => \T_BINARY_CAST,
        ];

        if (\version_compare(Helper::getVersion(), '4.0.2', '>=') === true
            || \PHP_VERSION_ID >= 80500
        ) {
            $expected[\T_VOID_CAST] = \T_VOID_CAST;
        }

        $this->assertSame($expected, BCTokens::castTokens());
    }

    /**
     * Test whether the method in BCTokens is still in sync with the latest version of PHPCS.
     *
     * This group is not run by default and has to be specifically requested to be run.
     *
     * @group compareWithPHPCS
     *
     * @return void
     */
    public function testPHPCSCastTokens()
    {
        $this->assertSame(Tokens::$castTokens, BCTokens::castTokens());
    }
}
