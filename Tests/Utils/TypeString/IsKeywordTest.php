<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\TypeString;

use PHPCSUtils\Tests\TypeProviderHelper;
use PHPCSUtils\Utils\TypeString;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHPCSUtils\Utils\TypeString::isKeyword() method.
 *
 * @covers \PHPCSUtils\Utils\TypeString::isKeyword
 *
 * @since 1.1.0
 */
final class IsKeywordTest extends TestCase
{

    /**
     * Test isKeyword() returns false when non-string data is passed.
     *
     * @dataProvider dataIsKeywordNonStringInput
     *
     * @param mixed $input The invalid input.
     *
     * @return void
     */
    public function testIsKeywordNonStringInput($input)
    {
        $this->assertFalse(TypeString::isKeyword($input));
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dataIsKeywordNonStringInput()
    {
        $data = TypeProviderHelper::getAll();
        unset(
            $data['empty string'],
            $data['numeric string'],
            $data['textual string'],
            $data['textual string starting with numbers']
        );

        return $data;
    }

    /**
     * Test isKeyword() returns "true" for PHP native keyword based types.
     *
     * @dataProvider dataIsKeywordValid
     *
     * @param string $type The type.
     *
     * @return void
     */
    public function testIsKeywordValid($type)
    {
        $this->assertTrue(TypeString::isKeyword($type));
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, string|bool>>
     */
    public static function dataIsKeywordValid()
    {
        $data  = [];
        $types = [
            // Valid keyword types.
            'array'    => 'array',
            'bool'     => 'bool',
            'callable' => 'callable',
            'false'    => 'false',
            'float'    => 'float',
            'int'      => 'int',
            'iterable' => 'iterable',
            'mixed'    => 'mixed',
            'never'    => 'never',
            'null'     => 'null',
            'object'   => 'object',
            'parent'   => 'parent',
            'self'     => 'self',
            'static'   => 'static',
            'string'   => 'string',
            'true'     => 'true',
            'void'     => 'void',
        ];

        foreach ($types as $type => $expected) {
            $data[$type . ': lowercase'] = [
                'type' => $type,
            ];

            $data[$type . ': uppercase'] = [
                'type' => \strtoupper($type),
            ];

            $data[$type . ': mixed case'] = [
                'type' => \ucfirst($type),
            ];

            $data[$type . ': surrounding whitespace'] = [
                'type' => "  $type  ",
            ];
        }

        $data['true: fully qualified']             = ['type' => '\true'];
        $data['false: fully qualified']            = ['type' => '\false'];
        $data['null: fully qualified']             = ['type' => '\null'];
        $data['true: fully qualified, uppercase']  = ['type' => '\TRUE'];
        $data['false: fully qualified, uppercase'] = ['type' => '\FALSE'];
        $data['null: fully qualified, uppercase']  = ['type' => '\NULL'];

        return $data;
    }

    /**
     * Test isKeyword() returns "false" for non-keyword based types.
     *
     * @dataProvider dataIsKeywordInvalid
     *
     * @param string $type The type.
     *
     * @return void
     */
    public function testIsKeywordInvalid($type)
    {
        $this->assertFalse(TypeString::isKeyword($type));
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, string|bool>>
     */
    public static function dataIsKeywordInvalid()
    {
        return [
            'empty string' => [
                'type' => '',
            ],
            'string containing only whitespace' => [
                'type' => '     ',
            ],
            'string which isn\'t a type string' => [
                'type' => 'Roll, roll, roll your boat',
            ],
            'typestring which hasn\'t been split yet (union)' => [
                'type' => 'true|string|float',
            ],
            'typestring which hasn\'t been split yet (intersection)' => [
                'type' => 'A&B',
            ],
            'typestring which hasn\'t been split yet (DNF)' => [
                'type' => '(A&B)|false',
            ],
            'Classname: Boolean' => [
                'type' => 'Boolean',
            ],
            'Classname: Integer' => [
                'type' => 'Integer',
            ],
            'Classname: Traversable with surrounding whitespace' => [
                'type' => '  Traversable   ' . "\n\t\n",
            ],
            'Classname: Package\Int' => [
                'type' => 'Package\Int',
            ],
            'Classname: namespace\Relative\Name' => [
                'type' => 'namespace\Relative\Name',
            ],
            'Classname: \Fully\Qualified\Name' => [
                'type' => '\Fully\Qualified\Name',
            ],
            'Classname: Пасха (non-ascii chars)' => [
                'type' => 'Пасха',
            ],
            'Classname: 😎 (non-ascii chars/emoji name)' => [
                'type' => '😎',
            ],
        ];
    }
}
