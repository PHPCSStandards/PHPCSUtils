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
 * @covers \PHPCSUtils\Tokens\Collections::returnTypeTokensBC
 *
 * @group collections
 *
 * @since 1.0.0
 */
class ReturnTypeTokensBCTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testReturnTypeTokensBC()
    {
        $version  = Helper::getVersion();
        $expected = Collections::returnTypeTokens();

        if (\version_compare($version, '3.99.99', '<=') === true) {
            $expected[\T_RETURN_TYPE] = \T_RETURN_TYPE;
            $expected[\T_ARRAY_HINT]  = \T_ARRAY_HINT;
        }

        $this->assertSame($expected, Collections::returnTypeTokensBC());
    }
}
