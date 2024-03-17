<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Tokens\TokenHelper;

use PHPCSUtils\Tokens\TokenHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Tokens\TokenHelper::tokenExists
 *
 * @since 1.0.0
 */
final class TokenExistsTest extends TestCase
{

    /**
     * Add select constants to allow testing this method.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpConstants()
    {
        if (\defined('T_FAKETOKEN') === false) {
            \define('T_FAKETOKEN', -5);
        }
    }

    /**
     * Test the method.
     *
     * @dataProvider dataTokenExists
     *
     * @param string $name     Token name.
     * @param bool   $expected Expected function return value.
     *
     * @return void
     */
    public function testTokenExists($name, $expected)
    {
        $this->assertSame($expected, TokenHelper::tokenExists($name));
    }

    /**
     * Data provider.
     *
     * {@internal This dataprovider does not currently contain any tests for
     *            PHP native tokens which may not exist (depending on the PHPCS version).
     *            These tests are not relevant at this time with the current minimum
     *            PHPCS version, but this may change again in the future.}
     *
     * @return array<string, array<string, string|bool>>
     */
    public static function dataTokenExists()
    {
        return [
            'Token which doesn\'t exist either way' => [
                'name'     => 'T_DOESNOTEXIST',
                'expected' => false,
            ],
            'PHP native token which always exists' => [
                'name'     => 'T_FUNCTION',
                'expected' => true,
            ],
            'PHPCS native token which always exists (in the PHPCS versions supported)' => [
                'name'     => 'T_CLOSURE',
                'expected' => true,
            ],
            'Mocked polyfilled token' => [
                'name'     => 'T_FAKETOKEN',
                'expected' => false,
            ],
        ];
    }
}
