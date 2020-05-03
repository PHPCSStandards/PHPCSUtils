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
 * @covers \PHPCSUtils\Tokens\Collections::functionDeclarationTokens
 *
 * @group collections
 *
 * @since 1.0.0
 */
class FunctionDeclarationTokensTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testFunctionDeclarationTokens()
    {
        $version  = Helper::getVersion();
        $expected = [
            \T_FUNCTION => \T_FUNCTION,
            \T_CLOSURE  => \T_CLOSURE,
        ];

        if (\version_compare($version, '3.5.3', '>=') === true
            || \version_compare(\PHP_VERSION_ID, '70399', '>=') === true
        ) {
            $expected[\T_FN] = \T_FN;
        }

        $this->assertSame($expected, Collections::functionDeclarationTokens());
    }
}
