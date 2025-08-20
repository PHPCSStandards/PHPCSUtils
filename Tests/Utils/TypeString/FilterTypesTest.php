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
use stdClass;
use Yoast\PHPUnitPolyfills\TestCases\TestCase;

/**
 * Tests for the \PHPCSUtils\Utils\TypeString::filterKeywordTypes() and the
 * \PHPCSUtils\Utils\TypeString::filterOOTypes() methods.
 *
 * @covers \PHPCSUtils\Utils\TypeString::filterKeywordTypes
 * @covers \PHPCSUtils\Utils\TypeString::filterOOTypes
 *
 * @since 1.1.0
 */
final class FilterTypesTest extends TestCase
{

    /**
     * Test filterKeywordTypes() throws an exception when non-array data is passed.
     *
     * @dataProvider dataFilterNonArrayInput
     *
     * @param mixed $input The invalid input.
     *
     * @return void
     */
    public function testFilterKeywordTypesNonArrayInput($input)
    {
        if (\PHP_VERSION_ID >= 70000) {
            // PHP 7.0+
            $this->expectException('\TypeError');
        } elseif (\method_exists($this, 'expectError')) {
            // PHP 5.4 + 5.5 with PHPUnit Polyfills 1.x.
            $this->expectError();
        } else {
            // PHP 5.6 with PHPUnit 5.2+ and PHPUnit Polyfills 2.x.
            $this->expectException('\PHPUnit_Framework_Error');
        }

        TypeString::filterKeywordTypes($input);
    }

    /**
     * Test filterOOTypes() throws an exception when non-array data is passed.
     *
     * @dataProvider dataFilterNonArrayInput
     *
     * @param mixed $input The invalid input.
     *
     * @return void
     */
    public function testFilterOOTypesNonArrayInput($input)
    {
        if (\PHP_VERSION_ID >= 70000) {
            // PHP 7.0+
            $this->expectException('\TypeError');
        } elseif (\method_exists($this, 'expectError')) {
            // PHP 5.4 + 5.5 with PHPUnit Polyfills 1.x.
            $this->expectError();
        } else {
            // PHP 5.6 with PHPUnit 5.2+ and PHPUnit Polyfills 2.x.
            $this->expectException('\PHPUnit_Framework_Error');
        }

        TypeString::filterOOTypes($input);
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function dataFilterNonArrayInput()
    {
        $data = TypeProviderHelper::getAll();
        unset(
            $data['empty array'],
            $data['array with values, no keys'],
            $data['array with values, string keys']
        );

        return $data;
    }

    /**
     * Test filterKeywordTypes() ignores non-string array entries completely.
     *
     * @return void
     */
    public function testFilterKeywordTypesDisregardsNonStringEntries()
    {
        $types = [
            'bool'      => false,
            'float'     => 1.5,
            'keyword'   => 'string',
            'int'       => 1.0,
            'iterable'  => [1, 2, 3],
            'object'    => new stdClass(),
            'classname' => '\Traversable',
        ];

        $expected = [
            'keyword' => 'string',
        ];

        $this->assertSame($expected, TypeString::filterKeywordTypes($types));
    }

    /**
     * Test filterOOTypes() ignores non-string array entries completely.
     *
     * @return void
     */
    public function testFilterOOTypesDisregardsNonStringEntries()
    {
        $types = [
            'bool'      => false,
            'float'     => 1.5,
            'int'       => 1.0,
            'iterable'  => [1, 2, 3],
            'classname' => '\Traversable',
            'object'    => new stdClass(),
            'keyword'   => 'string',
        ];

        $expected = [
            'classname' => '\Traversable',
        ];

        $this->assertSame($expected, TypeString::filterOOTypes($types));
    }

    /**
     * Test filterKeywordTypes() correctly filters out non-keyword types and maintains key association.
     *
     * @dataProvider dataFilterKeywordTypes
     *
     * @param array<string, string> $types    A types array.
     * @param array<string, string> $expected The expected function return value.
     *
     * @return void
     */
    public function testFilterKeywordTypes($types, $expected)
    {
        $this->assertEqualsCanonicalizing($expected, TypeString::filterKeywordTypes($types));
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, array<string, string>>>
     */
    public static function dataFilterKeywordTypes()
    {
        $baseData = self::dataFilterTypes();
        $data     = [];

        foreach ($baseData as $key => $dataSet) {
            $types = \array_merge($dataSet['keywords'], $dataSet['oonames']);
            $types = self::shuffle($types);

            $data[$key] = [
                'types'    => $types,
                'expected' => $dataSet['keywords'],
            ];
        }

        return $data;
    }

    /**
     * Test filterOOTypes() correctly filters out keyword types and maintains key association.
     *
     * @dataProvider dataFilterOOTypes
     *
     * @param array<string, string> $types    A types array.
     * @param array<string, string> $expected The expected function return value.
     *
     * @return void
     */
    public function testFilterOOTypes($types, $expected)
    {
        $this->assertEqualsCanonicalizing($expected, TypeString::filterOOTypes($types));
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, array<string, string>>>
     */
    public static function dataFilterOOTypes()
    {
        $baseData = self::dataFilterTypes();
        $data     = [];

        foreach ($baseData as $key => $dataSet) {
            $types = \array_merge($dataSet['keywords'], $dataSet['oonames']);
            $types = self::shuffle($types);

            $data[$key] = [
                'types'    => $types,
                'expected' => $dataSet['oonames'],
            ];
        }

        return $data;
    }

    /**
     * Data provider.
     *
     * @return array<string, array<string, array<string, string>>>
     */
    public static function dataFilterTypes()
    {
        return [
            'empty array' => [
                'keywords' => [],
                'oonames'  => [],
            ],
            'keyed array containing only keywords' => [
                'keywords' => [
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
                ],
                'oonames'  => [],
            ],
            'keyed array containing only oo names' => [
                'keywords' => [],
                'oonames'  => [
                    'Foo'                 => 'Foo',
                    '\Bar'                => '\Bar',
                    'Partially\Qualified' => 'Partially\Qualified',
                    'namespace\Relative'  => 'namespace\Relative',
                    '\Fully\Qualified'    => '\Fully\Qualified',
                ],
            ],
            'keyed array containing both keywords and oo names' => [
                'keywords' => [
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
                ],
                'oonames' => [
                    'Foo'                 => 'Foo',
                    '\Bar'                => '\Bar',
                    'Partially\Qualified' => 'Partially\Qualified',
                    'namespace\Relative'  => 'namespace\Relative',
                    '\Fully\Qualified'    => '\Fully\Qualified',
                ],
            ],
            'keyed array containing FQN and uppercase true/false/null keywords' => [
                'keywords' => [
                    '\FALSE' => '\FALSE',
                    '\NULL'  => '\NULL',
                    '\TRUE'  => '\TRUE',
                ],
                'oonames'  => [],
            ],
            'keyed array containing both keywords and oo names, keys not the same as values' => [
                'keywords' => [
                    'float'  => 'callable',
                    'string' => 'int',
                    'void'   => 'never',
                    'never'  => 'null',
                    'static' => 'self',
                    'parent' => 'static',
                    'true'   => 'string',
                ],
                'oonames' => [
                    'one'   => 'Foo',
                    'two'   => '\Bar',
                    'three' => 'Partially\Qualified',
                    'four'  => 'namespace\Relative',
                    'five'  => '\Fully\Qualified',
                ],
            ],
            'keyed array containing both keywords and oo names, keywords not case-normalized are returned as-is' => [
                'keywords' => [
                    'ARRAY'    => 'ARRAY',
                    'False'    => 'False',
                    'iterable' => 'iterable',
                    'MiXeD'    => 'MiXeD',
                    'parent'   => 'Parent',
                    'TRUE'     => 'TRUE',
                ],
                'oonames' => [
                    'Foo'              => 'Foo',
                    '\Bar'             => '\Bar',
                    '\Fully\Qualified' => '\Fully\Qualified',
                ],
            ],
            'keyed array containing both keywords and oo names, untrimmed values are returned as-is' => [
                'keywords' => [
                    'iterable' => 'iterable           ',
                    'parent'   => '  Parent  ',
                    'TRUE'     => "\t\tTRUE",
                ],
                'oonames' => [
                    'Foo'              => 'Foo         ',
                    '\Bar'             => '  \Bar  ',
                    '\Fully\Qualified' => '      \Fully\Qualified',
                ],
            ],
            'keyed array containing both keywords and oo names with duplicates, duplicates are included in return' => [
                'keywords' => [
                    'float'  => 'callable',
                    'string' => 'int',
                    'void'   => 'never',
                    'never'  => 'int',
                    'static' => 'self',
                    'parent' => 'callable',
                    'true'   => 'never',
                ],
                'oonames' => [
                    'one'   => 'Foo',
                    'two'   => '\Bar',
                    'three' => 'Partially\Qualified',
                    'four'  => '\Bar',
                    'five'  => 'Foo',
                ],
            ],
        ];
    }

    /**
     * Test filterKeywordTypes() correctly filters out non-keyword types and maintains
     * key association even when the keys are numeric.
     *
     * @return void
     */
    public function testFilterKeywordTypesKeyAssociationIsMaintainedEvenWhenNumeric()
    {
        $types = [
            'array',
            'int',
            '\Bar',
            'mixed',
            'Partially\Qualified',
            'parent',
            'true',
            'namespace\Relative',
        ];

        $expected = [
            0 => 'array',
            1 => 'int',
            3 => 'mixed',
            5 => 'parent',
            6 => 'true',
        ];

        $this->assertSame($expected, TypeString::filterKeywordTypes($types));
    }

    /**
     * Test filterOOTypes() correctly filters out keyword types and maintains
     * key association even when the keys are numeric.
     *
     * @return void
     */
    public function testFilterOOTypesKeyAssociationIsMaintainedEvenWhenNumeric()
    {
        $types = [
            'array',
            'int',
            '\Bar',
            'mixed',
            'Partially\Qualified',
            'parent',
            'true',
            'namespace\Relative',
        ];

        $expected = [
            2 => '\Bar',
            4 => 'Partially\Qualified',
            7 => 'namespace\Relative',
        ];

        $this->assertSame($expected, TypeString::filterOOTypes($types));
    }

    /**
     * Test filterKeywordTypes() correctly filters out non-keyword types from a numerically
     * keyed input and doesn't remove duplicates.
     *
     * @return void
     */
    public function testFilterKeywordTypesDuplicateHandlingWithNumericKeys()
    {
        $types = [
            'int',
            '\Bar',
            'mixed',
            'Partially\Qualified',
            'int',
            'Partially\Qualified',
        ];

        $expected = [
            0 => 'int',
            2 => 'mixed',
            4 => 'int',
        ];

        $this->assertSame($expected, TypeString::filterKeywordTypes($types));
    }

    /**
     * Test filterOOTypes() correctly filters out keyword types from a numerically
     * keyed input and doesn't remove duplicates.
     *
     * @return void
     */
    public function testFilterOOTypesDuplicateHandlingWithNumericKeys()
    {
        $types = [
            'int',
            '\Bar',
            'mixed',
            'Partially\Qualified',
            'int',
            'Partially\Qualified',
        ];

        $expected = [
            1 => '\Bar',
            3 => 'Partially\Qualified',
            5 => 'Partially\Qualified',
        ];

        $this->assertSame($expected, TypeString::filterOOTypes($types));
    }

    /**
     * Helper function: shuffle array while preserving key association.
     *
     * @param array<string, string> $types The array to shuffle.
     *
     * @return array<string, string>
     */
    private static function shuffle(array $types)
    {
        $keys = \array_keys($types);

        \shuffle($keys);

        $shuffled = [];
        foreach ($keys as $key) {
            $shuffled[$key] = $types[$key];
        }

        return $shuffled;
    }
}
