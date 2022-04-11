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

use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\Tokens\TokenHelper;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\Tokens\TokenHelper::tokenExists
 *
 * @since 1.0.0
 */
class TokenExistsTest extends TestCase
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
        \define('T_FAKETOKEN', -5);
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
     * @return array
     */
    public function dataTokenExists()
    {
        $phpcsVersion = Helper::getVersion();

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
            'PHP native token which may not exist - PHP 7.4 T_FN' => [
                'name'     => 'T_FN',
                'expected' => (\version_compare($phpcsVersion, '3.5.3', '>=')
                                || \version_compare(\PHP_VERSION_ID, '70399', '>=')),
            ],
            'PHP native token which may not exist - PHP 8.0 T_NULLSAFE_OBJECT_OPERATOR' => [
                'name'     => 'T_NULLSAFE_OBJECT_OPERATOR',
                'expected' => (\version_compare($phpcsVersion, '3.5.7', '>=')
                                || \version_compare(\PHP_VERSION_ID, '79999', '>=')),
            ],
            'PHP native token which may not exist - PHP 8.1 T_ENUM' => [
                'name'     => 'T_ENUM',
                'expected' => (\version_compare($phpcsVersion, '3.7.0', '>=')
                                || \version_compare(\PHP_VERSION_ID, '80099', '>=')),
            ],
            'Mocked polyfilled token' => [
                'name'     => 'T_FAKETOKEN',
                'expected' => false,
            ],
        ];
    }
}
