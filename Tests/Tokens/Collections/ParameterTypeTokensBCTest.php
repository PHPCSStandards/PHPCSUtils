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
 * @covers \PHPCSUtils\Tokens\Collections::parameterTypeTokensBC
 * @covers \PHPCSUtils\Tokens\Collections::triggerDeprecation
 *
 * @group collections
 *
 * @since 1.0.0
 */
final class ParameterTypeTokensBCTest extends TestCase
{

    /**
     * Test the method.
     *
     * @return void
     */
    public function testParameterTypeTokensBC()
    {
        $this->expectDeprecation();
        $this->expectDeprecationMessage(
            'Collections::parameterTypeTokensBC() method is deprecated since PHPCSUtils 1.0.0-alpha4.'
            . ' Use the PHPCSUtils\Tokens\Collections::parameterTypeTokens() method instead.'
        );

        $this->assertSame(Collections::parameterTypeTokens(), Collections::parameterTypeTokensBC());
    }
}
