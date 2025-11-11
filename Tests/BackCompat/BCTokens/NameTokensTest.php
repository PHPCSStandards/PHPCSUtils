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
 * @covers \PHPCSUtils\BackCompat\BCTokens::nameTokens
 *
 * @group tokens
 *
 * @since 1.1.0
 */
final class NameTokensTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testNameTokens()
    {
        $expected = [
            \T_STRING               => \T_STRING,
            \T_NAME_QUALIFIED       => \T_NAME_QUALIFIED,
            \T_NAME_FULLY_QUALIFIED => \T_NAME_FULLY_QUALIFIED,
            \T_NAME_RELATIVE        => \T_NAME_RELATIVE,
        ];

        $this->assertSame($expected, BCTokens::nameTokens());
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
    public function testPHPCSNameTokens()
    {
        if (\version_compare(Helper::getVersion(), '3.99.99', '>') === false) {
            $this->markTestSkipped('Test only applicable to PHPCS >= 4.x');
        }

        $this->assertSame(Tokens::NAME_TOKENS, BCTokens::nameTokens());
    }
}
