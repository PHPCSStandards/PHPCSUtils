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
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\BackCompat\BCTokens::magicConstants
 *
 * @group tokens
 *
 * @since 1.0.0
 */
class MagicConstantsTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testMagicConstants()
    {
        $expected = [
            \T_CLASS_C  => \T_CLASS_C,
            \T_DIR      => \T_DIR,
            \T_FILE     => \T_FILE,
            \T_FUNC_C   => \T_FUNC_C,
            \T_LINE     => \T_LINE,
            \T_METHOD_C => \T_METHOD_C,
            \T_NS_C     => \T_NS_C,
            \T_TRAIT_C  => \T_TRAIT_C,
        ];

        $this->assertSame($expected, BCTokens::magicConstants());
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
    public function testPHPCSMagicConstants()
    {
        $this->assertSame(Tokens::$magicConstants, BCTokens::magicConstants());
    }
}
