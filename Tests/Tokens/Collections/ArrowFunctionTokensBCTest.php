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
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Tokens\Collections::arrowFunctionTokensBC
 * @covers \PHPCSUtils\Tokens\Collections::triggerDeprecation
 *
 * @group collections
 *
 * @since 1.0.0
 */
final class ArrowFunctionTokensBCTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testArrowFunctionTokensBC()
    {
        $this->expectDeprecation();
        $this->expectDeprecationMessage(
            'Collections::arrowFunctionTokensBC() method is deprecated since PHPCSUtils 1.0.0-alpha4.'
            . ' Use the `T_FN` token instead.'
        );

        $expected = [
            \T_FN => \T_FN,
        ];

        $this->assertSame($expected, Collections::arrowFunctionTokensBC());
    }
}
