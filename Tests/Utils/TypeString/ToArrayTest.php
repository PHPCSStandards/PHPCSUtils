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
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Tests for the \PHPCSUtils\Utils\TypeString::toArray() and
 * the \PHPCSUtils\Utils\TypeString::toArrayUnique method.
 *
 * @covers \PHPCSUtils\Utils\TypeString::toArray
 * @covers \PHPCSUtils\Utils\TypeString::toArrayUnique
 *
 * @since 1.1.0
 */
final class ToArrayTest extends TestCase
{

    /**
     * Test toArray() returns an empty array when non-string data is passed.
     *
     * @dataProvider dataToArrayReturnsEmptyArrayOnNonStringInput
     *
     * @param mixed $input The invalid input.
     *
     * @return void
     */
    public function testToArrayThrowsExceptionOnNonStringInput($input)
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #1 ($typeString) must be of type string,');

        TypeString::toArray($input);
    }

    /**
     * Test toArrayUnique() returns an empty array when non-string data is passed.
     *
     * @dataProvider dataToArrayReturnsEmptyArrayOnNonStringInput
     *
     * @param mixed $input The invalid input.
     *
     * @return void
     */
    public function testToArrayUniqueThrowsExceptionOnNonStringInput($input)
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #1 ($typeString) must be of type string,');

        TypeString::toArrayUnique($input);
    }

    /**
     * Data provider.
     *
     * @see testToArrayReturnsEmptyArrayOnNonStringInput() For the array format.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dataToArrayReturnsEmptyArrayOnNonStringInput()
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
     * Test type string to array conversion.
     *
     * @dataProvider dataToArray
     * @dataProvider dataToArrayNormalized
     *
     * @param string                $type     The type string.
     * @param array<string, string> $expected The expected function output.
     *
     * @return void
     */
    public function testToArrayAndNormalize($type, $expected)
    {
        $this->assertSame(\array_values($expected), TypeString::toArray($type, true));
    }

    /**
     * Test type string to array conversion.
     *
     * @dataProvider dataToArray
     * @dataProvider dataToArrayNotNormalized
     *
     * @param string                $type     The type string.
     * @param array<string, string> $expected The expected function output.
     *
     * @return void
     */
    public function testToArrayDontNormalize($type, $expected)
    {
        $this->assertSame(\array_values($expected), TypeString::toArray($type, false));
    }

    /**
     * Test type string to array conversion with de-duplication.
     *
     * @dataProvider dataToArray
     * @dataProvider dataToArrayNormalized
     * @dataProvider dataToArrayUniqueNormalized
     *
     * @param string                $type     The type string.
     * @param array<string, string> $expected The expected function output.
     *
     * @return void
     */
    public function testToArrayUniqueAndNormalize($type, $expected)
    {
        $this->assertSame($expected, TypeString::toArrayUnique($type, true));
    }

    /**
     * Test type string to array conversion with de-duplication.
     *
     * @dataProvider dataToArray
     * @dataProvider dataToArrayNotNormalized
     * @dataProvider dataToArrayUniqueNotNormalized
     *
     * @param string                $type     The type string.
     * @param array<string, string> $expected The expected function output.
     *
     * @return void
     */
    public function testToArrayUniqueDontNormalize($type, $expected)
    {
        $this->assertSame($expected, TypeString::toArrayUnique($type, false));
    }

    /**
     * Data provider: input for which normalization is irrelevant.
     *
     * @see testToArray() For the array format.
     *
     * @return array<string, array<string, string|array<string, string>>>
     */
    public static function dataToArray()
    {
        return [
            'empty string' => [
                'type'     => '',
                'expected' => [],
            ],
            'string containing only whitespace' => [
                'type'     => '      ',
                'expected' => [],
            ],

            'simple singular type: callable' => [
                'type'     => 'callable',
                'expected' => [
                    'callable' => 'callable',
                ],
            ],
            'nullable type: ?string' => [
                'type'     => '?string',
                'expected' => [
                    'null'   => 'null',
                    'string' => 'string',
                ],
            ],
            'nullable type with whitespace: ?   ClassName' => [
                'type'     => '?   ClassName',
                'expected' => [
                    'null'      => 'null',
                    'ClassName' => 'ClassName',
                ],
            ],
            'nullable type: ?boolean (invalid/interpreted as classname)' => [
                'type'     => '?boolean',
                'expected' => [
                    'null'    => 'null',
                    'boolean' => 'boolean',
                ],
            ],
            'nullable type: ?void (invalid)' => [
                'type'     => '?void',
                'expected' => [
                    'null' => 'null',
                    'void' => 'void',
                ],
            ],

            'union type: all types' => [
                'type'     => 'array|bool|callable|false|float|int|iterable|mixed|never|null|object|parent|'
                    . 'self|static|string|true|void',
                'expected' => [
                    'array'     => 'array',
                    'bool'      => 'bool',
                    'callable'  => 'callable',
                    'false'     => 'false',
                    'float'     => 'float',
                    'int'       => 'int',
                    'iterable'  => 'iterable',
                    'mixed'     => 'mixed',
                    'never'     => 'never',
                    'null'      => 'null',
                    'object'    => 'object',
                    'parent'    => 'parent',
                    'self'      => 'self',
                    'static'    => 'static',
                    'string'    => 'string',
                    'true'      => 'true',
                    'void'      => 'void',
                ],
            ],
            'union type: UnqualifiedName|Package\Partially|\Vendor\FullyQualified|namespace\Relative\Name' => [
                'type'     => 'UnqualifiedName|Package\Partially|\Vendor\FullyQualified|namespace\Relative\Name',
                'expected' => [
                    'UnqualifiedName'         => 'UnqualifiedName',
                    'Package\Partially'       => 'Package\Partially',
                    '\Vendor\FullyQualified'  => '\Vendor\FullyQualified',
                    'namespace\Relative\Name' => 'namespace\Relative\Name',
                ],
            ],
            'union type: true | null | void (invalid + whitespace)' => [
                'type'     => 'true | null | void',
                'expected' => [
                    'true' => 'true',
                    'null' => 'null',
                    'void' => 'void',
                ],
            ],

            'intersection type: UnqualifiedName&Package\Partially&\Vendor \FullyQualified & namespace\Relative\ Name' => [
                'type'     => 'UnqualifiedName&Package\Partially&\Vendor \FullyQualified & namespace\Relative\ Name',
                'expected' => [
                    'UnqualifiedName'         => 'UnqualifiedName',
                    'Package\Partially'       => 'Package\Partially',
                    '\Vendor\FullyQualified'  => '\Vendor\FullyQualified',
                    'namespace\Relative\Name' => 'namespace\Relative\Name',
                ],
            ],
            'intersection type: Foo & Bar (whitespace)' => [
                'type'     => 'Foo & Bar',
                'expected' => [
                    'Foo' => 'Foo',
                    'Bar' => 'Bar',
                ],
            ],
            'intersection type: bool&never (invalid)' => [
                'type'     => 'bool&never',
                'expected' => [
                    'bool'  => 'bool',
                    'never' => 'never',
                ],
            ],

            'DNF type: (A&B)|D' => [
                'type'     => '(A&B)|D',
                'expected' => [
                    'A' => 'A',
                    'B' => 'B',
                    'D' => 'D',
                ],
            ],
            'DNF type: C | ( \Fully\Qualified & Partially\Qualified ) | null (whitespace)' => [
                'type'     => 'C | ( \Fully\Qualified & Partially\Qualified ) | null',
                'expected' => [
                    'C'                   => 'C',
                    '\Fully\Qualified'    => '\Fully\Qualified',
                    'Partially\Qualified' => 'Partially\Qualified',
                    'null'                => 'null',
                ],
            ],
            'DNF type: int|null|(A&B&D)' => [
                'type'     => 'int|null|(A&B&D)',
                'expected' => [
                    'int'  => 'int',
                    'null' => 'null',
                    'A'    => 'A',
                    'B'    => 'B',
                    'D'    => 'D',
                ],
            ],
            'DNF type: (B&A)|null|(namespace\Relative&Unqualified)|false|(C&D)' => [
                'type'     => '(B&A)|null|(namespace\Relative&Unqualified)|false|(C&D)',
                'expected' => [
                    'B'                  => 'B',
                    'A'                  => 'A',
                    'null'               => 'null',
                    'namespace\Relative' => 'namespace\Relative',
                    'Unqualified'        => 'Unqualified',
                    'false'              => 'false',
                    'C'                  => 'C',
                    'D'                  => 'D',
                ],
            ],
            'DNF type: (A&B) (invalid, parens not needed)' => [
                'type'     => '(A&B)',
                'expected' => [
                    'A' => 'A',
                    'B' => 'B',
                ],
            ],
            'DNF type: A&(B|D) (invalid, parse error)' => [
                'type'     => 'A&(B|D)',
                'expected' => [
                    'A' => 'A',
                    'B' => 'B',
                    'D' => 'D',
                ],
            ],
            'DNF type: A|(B&(D|W)|null) (invalid, parse error)' => [
                'type'     => 'A|(B&(D|W)|null)',
                'expected' => [
                    'A'    => 'A',
                    'B'    => 'B',
                    'D'    => 'D',
                    'W'    => 'W',
                    'null' => 'null',
                ],
            ],
        ];
    }

    /**
     * Data provider: input for which normalization makes a difference.
     *
     * @see testToArray() For the array format.
     *
     * @return array<string, array<string, string|array<string, string>>>
     */
    public static function dataToArrayNormalized()
    {
        return [
            'simple singular type, mixed case' => [
                'type'     => 'FlOaT',
                'expected' => [
                    'float' => 'float',
                ],
            ],
            'union type: all types, some using non-standard case' => [
                'type'     => 'array|bool|callable|false|FLOAT|Int|iterable|miXed|never|null|object|parent|'
                    . 'Self|static|string|TRUE|void',
                'expected' => [
                    'array'     => 'array',
                    'bool'      => 'bool',
                    'callable'  => 'callable',
                    'false'     => 'false',
                    'float'     => 'float',
                    'int'       => 'int',
                    'iterable'  => 'iterable',
                    'mixed'     => 'mixed',
                    'never'     => 'never',
                    'null'      => 'null',
                    'object'    => 'object',
                    'parent'    => 'parent',
                    'self'      => 'self',
                    'static'    => 'static',
                    'string'    => 'string',
                    'true'      => 'true',
                    'void'      => 'void',
                ],
            ],
            'union type: \true|\false|\null (FQN)' => [
                'type'     => '\true|\false|\null',
                'expected' => [
                    'true'  => 'true',
                    'false' => 'false',
                    'null'  => 'null',
                ],
            ],
            'union type: \TRUE|\FALSE|\NULL (FQN + uppercase)' => [
                'type'     => '\TRUE|\FALSE|\NULL',
                'expected' => [
                    'true'  => 'true',
                    'false' => 'false',
                    'null'  => 'null',
                ],
            ],

            'DNF type: keywords in mixed case' => [
                'type'     => 'FALSE|(B&A)|Null',
                'expected' => [
                    'false' => 'false',
                    'B'     => 'B',
                    'A'     => 'A',
                    'null'  => 'null',
                ],
            ],
        ];
    }

    /**
     * Data provider: input for which normalization makes a difference.
     *
     * @see testToArray() For the array format.
     *
     * @return array<string, array<string, string|array<string, string>>>
     */
    public static function dataToArrayNotNormalized()
    {
        return [
            'simple singular type, mixed case' => [
                'type'     => 'FlOaT',
                'expected' => [
                    'FlOaT' => 'FlOaT',
                ],
            ],
            'union type: all types, some using non-standard case' => [
                'type'     => 'array|bool|callable|false|FLOAT|Int|iterable|miXed|never|null|object|parent|'
                    . 'Self|static|string|TRUE|void',
                'expected' => [
                    'array'     => 'array',
                    'bool'      => 'bool',
                    'callable'  => 'callable',
                    'false'     => 'false',
                    'FLOAT'     => 'FLOAT',
                    'Int'       => 'Int',
                    'iterable'  => 'iterable',
                    'miXed'     => 'miXed',
                    'never'     => 'never',
                    'null'      => 'null',
                    'object'    => 'object',
                    'parent'    => 'parent',
                    'Self'      => 'Self',
                    'static'    => 'static',
                    'string'    => 'string',
                    'TRUE'      => 'TRUE',
                    'void'      => 'void',
                ],
            ],
            'union type: \true|\false|\null (FQN)' => [
                'type'     => '\true|\false|\null',
                'expected' => [
                    '\true'  => '\true',
                    '\false' => '\false',
                    '\null'  => '\null',
                ],
            ],
            'union type: \TRUE|\FALSE|\NULL (FQN + uppercase)' => [
                'type'     => '\TRUE|\FALSE|\NULL',
                'expected' => [
                    '\TRUE'  => '\TRUE',
                    '\FALSE' => '\FALSE',
                    '\NULL'  => '\NULL',
                ],
            ],

            'DNF type: keywords in mixed case' => [
                'type'     => 'FALSE|(B&A)|Null',
                'expected' => [
                    'FALSE' => 'FALSE',
                    'B'     => 'B',
                    'A'     => 'A',
                    'Null'  => 'Null',
                ],
            ],
        ];
    }

    /**
     * Data provider: input for which filtering unique types makes a difference when the data is normalized.
     *
     * @see testToArray() For the array format.
     *
     * @return array<string, array<string, string|array<string, string>>>
     */
    public static function dataToArrayUniqueNormalized()
    {
        return [
            'union type with duplicates: different case' => [
                'type'     => 'FlOaT|null|float|NULL',
                'expected' => [
                    'float' => 'float',
                    'null'  => 'null',
                ],
            ],
            'union type with duplicates: same case' => [
                'type'     => 'float|null|float|null',
                'expected' => [
                    'float' => 'float',
                    'null'  => 'null',
                ],
            ],

            // Normalization makes no difference for OO types, even though the classes are effectively
            // the same due to the OO case handling in PHP, but that's not the concern of these methods.
            'intersection type with duplicates: different case' => [
                'type'     => 'FooBar&\Baz&foobar',
                'expected' => [
                    'FooBar' => 'FooBar',
                    '\Baz'   => '\Baz',
                    'foobar' => 'foobar',
                ],
            ],
            'intersection type with duplicates: same case' => [
                'type'     => 'FooBar&\Baz&FooBar',
                'expected' => [
                    'FooBar' => 'FooBar',
                    '\Baz'   => '\Baz',
                ],
            ],

            'DNF type with duplicates: different case' => [
                'type'     => '(A&B)|FALSE|(C&a)|false',
                'expected' => [
                    'A'     => 'A',
                    'B'     => 'B',
                    'false' => 'false',
                    'C'     => 'C',
                    'a'     => 'a',
                ],
            ],
            'DNF type with duplicates: same case' => [
                'type'     => '(A&B)|false|(C&A)|false',
                'expected' => [
                    'A'     => 'A',
                    'B'     => 'B',
                    'false' => 'false',
                    'C'     => 'C',
                ],
            ],
        ];
    }

    /**
     * Data provider: input for which filtering unique types makes a difference when the data is not normalized
     *
     * @see testToArray() For the array format.
     *
     * @return array<string, array<string, string|array<string, string>>>
     */
    public static function dataToArrayUniqueNotNormalized()
    {
        return [
            'union type with duplicates: different case' => [
                'type'     => 'FlOaT|null|float|NULL',
                'expected' => [
                    'FlOaT' => 'FlOaT',
                    'null'  => 'null',
                    'float' => 'float',
                    'NULL'  => 'NULL',
                ],
            ],
            'union type with duplicates: same case' => [
                'type'     => 'float|null|float|null',
                'expected' => [
                    'float' => 'float',
                    'null'  => 'null',
                ],
            ],

            // Normalization makes no difference for OO types, even though the classes are effectively
            // the same due to the OO case handling in PHP, but that's not the concern of these methods.
            'intersection type with duplicates: different case' => [
                'type'     => 'FooBar&\Baz&foobar',
                'expected' => [
                    'FooBar' => 'FooBar',
                    '\Baz'   => '\Baz',
                    'foobar' => 'foobar',
                ],
            ],
            'intersection type with duplicates: same case' => [
                'type'     => 'FooBar&\Baz&FooBar',
                'expected' => [
                    'FooBar' => 'FooBar',
                    '\Baz'   => '\Baz',
                ],
            ],

            'DNF type with duplicates: different case' => [
                'type'     => '(A&B)|FALSE|(C&a)|false',
                'expected' => [
                    'A'     => 'A',
                    'B'     => 'B',
                    'FALSE' => 'FALSE',
                    'C'     => 'C',
                    'a'     => 'a',
                    'false' => 'false',
                ],
            ],
            'DNF type with duplicates: same case' => [
                'type'     => '(A&B)|false|(C&A)|false',
                'expected' => [
                    'A'     => 'A',
                    'B'     => 'B',
                    'false' => 'false',
                    'C'     => 'C',
                ],
            ],
        ];
    }
}
