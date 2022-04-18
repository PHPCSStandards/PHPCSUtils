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

use PHPCSUtils\Tokens\Collections;
use PHPUnit\Framework\TestCase;
use Yoast\PHPUnitPolyfills\Polyfills\AssertIsType;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Tokens\Collections::arrayOpenTokensBC
 *
 * @group collections
 *
 * @since 1.0.0
 */
class ArrayOpenTokensBCTest extends TestCase
{
    use AssertIsType;

    /**
     * Test the method.
     *
     * @return void
     */
    public function testArrayOpenTokensBC()
    {
        $arrayOpenTokens = Collections::arrayOpenTokensBC();
        $this->assertIsArray($arrayOpenTokens);
        $this->assertCount(3, $arrayOpenTokens);
    }
}
