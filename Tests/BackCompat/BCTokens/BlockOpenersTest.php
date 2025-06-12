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
 * @covers \PHPCSUtils\BackCompat\BCTokens::blockOpeners
 *
 * @group tokens
 *
 * @since 1.1.0
 */
final class BlockOpenersTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testBlockOpeners()
    {
        $expected = [
            \T_OPEN_CURLY_BRACKET  => \T_OPEN_CURLY_BRACKET,
            \T_OPEN_SQUARE_BRACKET => \T_OPEN_SQUARE_BRACKET,
            \T_OPEN_PARENTHESIS    => \T_OPEN_PARENTHESIS,
        ];

        $this->assertSame($expected, BCTokens::blockOpeners());
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
    public function testPHPCSBlockOpeners()
    {
        $version = Helper::getVersion();

        if (\version_compare($version, '3.99.99', '>') === true) {
            $this->assertSame(Tokens::$blockOpeners, BCTokens::blockOpeners());
        } else {
            /*
             * Don't fail this test on the difference between PHPCS 4.x and 3.x.
             * This test is only run against `dev-master` and `dev-master` is still PHPCS 3.x.
             */
            $expected = Tokens::$blockOpeners;
            unset($expected[\T_OBJECT]);

            $result = BCTokens::blockOpeners();

            $this->assertSame($expected, $result);
        }
    }
}
