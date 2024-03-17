<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\FunctionDeclarations;

use PHPCSUtils\Utils\FunctionDeclarations;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHPCSUtils\Utils\FunctionDeclarations::isMagicFunctionName() method.
 *
 * @covers \PHPCSUtils\Utils\FunctionDeclarations::isMagicFunctionName
 *
 * @group functiondeclarations
 *
 * @since 1.0.0
 */
final class IsMagicFunctionNameTest extends TestCase
{

    /**
     * Test valid PHP magic function names.
     *
     * @dataProvider dataIsMagicFunctionName
     *
     * @param string $name The function name to test.
     *
     * @return void
     */
    public function testIsMagicFunctionName($name)
    {
        $this->assertTrue(FunctionDeclarations::isMagicFunctionName($name));
    }

    /**
     * Data provider.
     *
     * @see testIsMagicFunctionName() For the array format.
     *
     * @return array<string, array<string>>
     */
    public static function dataIsMagicFunctionName()
    {
        return [
            'lowercase' => ['__autoload'],
            'uppercase' => ['__AUTOLOAD'],
            'mixedcase' => ['__AutoLoad'],
        ];
    }

    /**
     * Test non-magic function names.
     *
     * @dataProvider dataIsNotMagicFunctionName
     *
     * @param string $name The function name to test.
     *
     * @return void
     */
    public function testIsNotMagicFunctionName($name)
    {
        $this->assertFalse(FunctionDeclarations::isMagicFunctionName($name));
    }

    /**
     * Data provider.
     *
     * @see testIsNotMagicFunctionName() For the array format.
     *
     * @return array<string, array<string>>
     */
    public static function dataIsNotMagicFunctionName()
    {
        return [
            'no_underscore'           => ['noDoubleUnderscore'],
            'single_underscore'       => ['_autoload'],
            'triple_underscore'       => ['___autoload'],
            'not_magic_function_name' => ['__notAutoload'],
        ];
    }
}
