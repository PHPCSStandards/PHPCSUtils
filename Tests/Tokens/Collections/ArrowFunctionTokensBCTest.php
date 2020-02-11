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
 * @covers \PHPCSUtils\Tokens\Collections::arrowFunctionTokensBC
 *
 * @group collections
 *
 * @since 1.0.0
 */
class ArrowFunctionTokensBCTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testArrowFunctionTokensBC()
    {
        $version  = Helper::getVersion();
        $expected = [
            \T_STRING => \T_STRING,
        ];

        if (\version_compare($version, '3.5.3', '>=') === true
            || \version_compare(\PHP_VERSION_ID, '70399', '>=') === true
        ) {
            $expected[\T_FN] = \T_FN;
        }

        $this->assertSame($expected, Collections::ArrowFunctionTokensBC());
    }
}
