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
 * @covers \PHPCSUtils\Tokens\Collections::propertyTypeTokensBC
 *
 * @group collections
 *
 * @since 1.0.0
 */
class PropertyTypeTokensBCTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testPropertyTypeTokensBC()
    {
        $version  = Helper::getVersion();
        $expected = Collections::$propertyTypeTokens;

        if (\version_compare($version, '3.99.99', '<=') === true) {
            $expected[\T_ARRAY_HINT] = \T_ARRAY_HINT;
        }

        $this->assertSame($expected, Collections::propertyTypeTokensBC());
    }
}
