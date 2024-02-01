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
 * Tests for the \PHPCSUtils\Utils\FunctionDeclarations::isMagicMethodName() method.
 *
 * @coversDefaultClass \PHPCSUtils\Utils\FunctionDeclarations
 *
 * @group functiondeclarations
 *
 * @since 1.0.0
 */
final class IsMagicMethodNameTest extends TestCase
{

    /**
     * Test valid PHP magic method names.
     *
     * @dataProvider dataIsMagicMethodName
     * @covers       ::isMagicMethodName
     *
     * @param string $name The function name to test.
     *
     * @return void
     */
    public function testIsMagicMethodName($name)
    {
        $this->assertTrue(FunctionDeclarations::isMagicMethodName($name));
    }

    /**
     * Test valid PHP magic method names.
     *
     * @dataProvider dataIsMagicMethodName
     * @covers       ::isSpecialMethodName
     *
     * @param string $name The function name to test.
     *
     * @return void
     */
    public function testIsSpecialMethodName($name)
    {
        $this->assertTrue(FunctionDeclarations::isSpecialMethodName($name));
    }

    /**
     * Data provider.
     *
     * @see testIsMagicMethodName()   For the array format.
     * @see testIsSpecialMethodName() For the array format.
     *
     * @return array<string, array<string>>
     */
    public static function dataIsMagicMethodName()
    {
        return [
            // Normal case.
            'construct-defined-case'   => ['__construct'],
            'destruct-defined-case'    => ['__destruct'],
            'call-defined-case'        => ['__call'],
            'callStatic-defined-case'  => ['__callStatic'],
            'get-defined-case'         => ['__get'],
            'set-defined-case'         => ['__set'],
            'isset-defined-case'       => ['__isset'],
            'unset-defined-case'       => ['__unset'],
            'sleep-defined-case'       => ['__sleep'],
            'wakeup-defined-case'      => ['__wakeup'],
            'toString-defined-case'    => ['__toString'],
            'set_state-defined-case'   => ['__set_state'],
            'clone-defined-case'       => ['__clone'],
            'invoke-defined-case'      => ['__invoke'],
            'debugInfo-defined-case'   => ['__debugInfo'],
            'serialize-defined-case'   => ['__serialize'],
            'unserialize-defined-case' => ['__unserialize'],

            // Uppercase et al.
            'construct-changed-case'   => ['__CONSTRUCT'],
            'destruct-changed-case'    => ['__Destruct'],
            'call-changed-case'        => ['__Call'],
            'callStatic-changed-case'  => ['__callstatic'],
            'get-changed-case'         => ['__GET'],
            'set-changed-case'         => ['__SeT'],
            'isset-changed-case'       => ['__isSet'],
            'unset-changed-case'       => ['__unSet'],
            'sleep-changed-case'       => ['__SleeP'],
            'wakeup-changed-case'      => ['__wakeUp'],
            'toString-changed-case'    => ['__TOString'],
            'set_state-changed-case'   => ['__Set_State'],
            'clone-changed-case'       => ['__CLONE'],
            'invoke-changed-case'      => ['__Invoke'],
            'debugInfo-changed-case'   => ['__Debuginfo'],
            'serialize-changed-case'   => ['__SERIALIZE'],
            'unserialize-changed-case' => ['__unSerialize'],
        ];
    }

    /**
     * Test non-magic method names.
     *
     * @dataProvider dataIsNotMagicMethodName
     * @covers       ::isMagicMethodName
     *
     * @param string $name The function name to test.
     *
     * @return void
     */
    public function testIsNotMagicMethodName($name)
    {
        $this->assertFalse(FunctionDeclarations::isMagicMethodName($name));
    }

    /**
     * Test non-magic method names.
     *
     * @dataProvider dataIsNotMagicMethodName
     * @covers       ::isSpecialMethodName
     *
     * @param string $name The function name to test.
     *
     * @return void
     */
    public function testIsNotSpecialMethodName($name)
    {
        $this->assertFalse(FunctionDeclarations::isSpecialMethodName($name));
    }

    /**
     * Data provider.
     *
     * @see testIsNotMagicMethodName()   For the array format.
     * @see testIsNotSpecialMethodName() For the array format.
     *
     * @return array<string, array<string>>
     */
    public static function dataIsNotMagicMethodName()
    {
        return [
            'no_underscore'         => ['construct'],
            'single_underscore'     => ['_destruct'],
            'triple_underscore'     => ['___call'],
            'not_magic_method_name' => ['__myFunction'],
        ];
    }
}
