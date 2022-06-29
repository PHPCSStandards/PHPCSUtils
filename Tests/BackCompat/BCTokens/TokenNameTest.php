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

use PHPCSUtils\BackCompat\BCTokens;
use PHPUnit\Framework\TestCase;

/**
 * Test class.
 *
 * @covers \PHPCSUtils\BackCompat\BCTokens::tokenName
 *
 * @group tokens
 *
 * @since 1.0.0
 */
class TokenNameTest extends TestCase
{

    /**
     * Test the method.
     *
     * @dataProvider dataTokenName
     *
     * @param int|string $tokenCode The PHP/PHPCS token code to get the name for.
     * @param string     $expected  The expected token name.
     *
     * @return void
     */
    public function testTokenName($tokenCode, $expected)
    {
        $this->assertSame($expected, BCTokens::tokenName($tokenCode));
    }

    /**
     * Data provider.
     *
     * @see testTokenName() For the array format.
     *
     * @return array
     */
    public function dataTokenName()
    {
        return [
            'PHP native token: T_ECHO' => [
                'tokenCode' => \T_ECHO,
                'expected'  => 'T_ECHO',
            ],
            'PHP native token: T_FUNCTION' => [
                'tokenCode' => \T_FUNCTION,
                'expected'  => 'T_FUNCTION',
            ],
            'PHPCS native token: T_CLOSURE' => [
                'tokenCode' => \T_CLOSURE,
                'expected'  => 'T_CLOSURE',
            ],
            'PHPCS native token: T_STRING_CONCAT' => [
                'tokenCode' => \T_STRING_CONCAT,
                'expected'  => 'T_STRING_CONCAT',
            ],
        ];
    }
}
