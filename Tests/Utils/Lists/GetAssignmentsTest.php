<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Lists;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Lists;

/**
 * Tests for the \PHPCSUtils\Utils\Lists::getAssignments() method.
 *
 * @covers \PHPCSUtils\Utils\Lists::getAssignments
 *
 * @group lists
 *
 * @since 1.0.0
 */
class GetAssignmentsTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException('The Lists::getAssignments() method expects a long/short list token.');

        Lists::getAssignments(self::$phpcsFile, 100000);
    }

    /**
     * Test that false is returned when a non-(short) list token is passed.
     *
     * @dataProvider dataNotListToken
     *
     * @param string           $testMarker  The comment which prefaces the target token in the test file.
     * @param int|string|array $targetToken The token type(s) to look for.
     *
     * @return void
     */
    public function testNotListToken($testMarker, $targetToken)
    {
        $this->expectPhpcsException('The Lists::getAssignments() method expects a long/short list token.');

        $target = $this->getTargetToken($testMarker, $targetToken);
        Lists::getAssignments(self::$phpcsFile, $target);
    }

    /**
     * Data provider.
     *
     * @see testNotListToken() For the array format.
     *
     * @return array
     */
    public function dataNotListToken()
    {
        return [
            'not-a-list' => [
                '/* testNotAList */',
                \T_OPEN_SHORT_ARRAY,
            ],
            'live-coding' => [
                '/* testLiveCoding */',
                \T_LIST,
            ],
        ];
    }

    /**
     * Test retrieving the details of the variable assignments for a list.
     *
     * @dataProvider dataGetAssignments
     *
     * @param string           $testMarker  The comment which prefaces the target token in the test file.
     * @param int|string|array $targetToken The token type(s) to look for.
     * @param array|false      $expected    The expected function return value.
     *
     * @return void
     */
    public function testGetAssignments($testMarker, $targetToken, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, $targetToken);

        // Convert offsets to absolute positions.
        foreach ($expected as $index => $subset) {
            if (isset($subset['key_token'])) {
                $expected[$index]['key_token'] += $stackPtr;
            }
            if (isset($subset['key_end_token'])) {
                $expected[$index]['key_end_token'] += $stackPtr;
            }
            if (isset($subset['double_arrow_token'])) {
                $expected[$index]['double_arrow_token'] += $stackPtr;
            }
            if (isset($subset['reference_token']) && $subset['reference_token'] !== false) {
                $expected[$index]['reference_token'] += $stackPtr;
            }
            if (isset($subset['assignment_token'])) {
                $expected[$index]['assignment_token'] += $stackPtr;
            }
            if (isset($subset['assignment_end_token'])) {
                $expected[$index]['assignment_end_token'] += $stackPtr;
            }
        }

        $result = Lists::getAssignments(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * Token positions are provided as offsets from the target stackPtr.
     *
     * @see testGetAssignments() For the array format.
     *
     * @return array
     */
    public function dataGetAssignments()
    {
        return [
            'long-list-empty' => [
                '/* testEmptyLongList */',
                \T_LIST,
                [],
            ],
            'short-list-empty' => [
                '/* testEmptyShortList */',
                \T_OPEN_SHORT_ARRAY,
                [],
            ],
            'long-list-all-empty' => [
                '/* testLongListOnlyEmpties */',
                \T_LIST,
                [
                    0 => [
                        'raw'                  => '',
                        'is_empty'             => true,
                    ],
                    1 => [
                        'raw'                  => '/* comment */',
                        'is_empty'             => true,
                    ],
                    2 => [
                        'raw'                  => '',
                        'is_empty'             => true,
                    ],
                    3 => [
                        'raw'                  => '',
                        'is_empty'             => true,
                    ],
                ],
            ],
            'short-list-all-empty-with-comment' => [
                '/* testShortListOnlyEmpties */',
                \T_OPEN_SHORT_ARRAY,
                [],
            ],
            'long-list-basic' => [
                '/* testSimpleLongList */',
                \T_LIST,
                [
                    0 => [
                        'raw'                  => '$id',
                        'is_empty'             => false,
                        'assignment'           => '$id',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$id',
                        'assignment_token'     => 2,
                        'assignment_end_token' => 2,
                    ],
                    1 => [
                        'raw'                  => '$name',
                        'is_empty'             => false,
                        'assignment'           => '$name',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$name',
                        'assignment_token'     => 5,
                        'assignment_end_token' => 5,
                    ],
                ],
            ],
            'short-list-basic' => [
                '/* testSimpleShortList */',
                \T_OPEN_SHORT_ARRAY,
                [
                    0 => [
                        'raw'                  => '$this->propA',
                        'is_empty'             => false,
                        'assignment'           => '$this->propA',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$this',
                        'assignment_token'     => 1,
                        'assignment_end_token' => 3,
                    ],
                    1 => [
                        'raw'                  => '$this->propB',
                        'is_empty'             => false,
                        'assignment'           => '$this->propB',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$this',
                        'assignment_token'     => 6,
                        'assignment_end_token' => 8,
                    ],
                ],
            ],
            'short-list-in-foreach-keyed-with-ref' => [
                '/* testShortListInForeachKeyedWithRef */',
                \T_OPEN_SHORT_ARRAY,
                [
                    0 => [
                        'key'                  => "'id'",
                        'key_token'            => 1,
                        'key_end_token'        => 1,
                        'double_arrow_token'   => 3,
                        'raw'                  => '\'id\' => & $id',
                        'is_empty'             => false,
                        'assignment'           => '$id',
                        'nested_list'          => false,
                        'assign_by_reference'  => true,
                        'reference_token'      => 5,
                        'variable'             => '$id',
                        'assignment_token'     => 7,
                        'assignment_end_token' => 7,
                    ],
                    1 => [
                        'key'                  => "'name'",
                        'key_token'            => 10,
                        'key_end_token'        => 10,
                        'double_arrow_token'   => 12,
                        'raw'                  => '\'name\' => $name',
                        'is_empty'             => false,
                        'assignment'           => '$name',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$name',
                        'assignment_token'     => 14,
                        'assignment_end_token' => 14,
                    ],
                ],
            ],
            'long-list-nested' => [
                '/* testNestedLongList */',
                \T_LIST,
                [
                    0 => [
                        'raw'                  => '$a',
                        'is_empty'             => false,
                        'assignment'           => '$a',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$a',
                        'assignment_token'     => 2,
                        'assignment_end_token' => 2,
                    ],
                    1 => [
                        'raw'                  => 'list($b, $c)',
                        'is_empty'             => false,
                        'assignment'           => 'list($b, $c)',
                        'nested_list'          => true,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => false,
                        'assignment_token'     => 5,
                        'assignment_end_token' => 11,
                    ],
                ],
            ],
            'long-list-with-keys' => [
                '/* testLongListWithKeys */',
                \T_LIST,
                [
                    0 => [
                        'key'                  => "'name'",
                        'key_token'            => 2,
                        'key_end_token'        => 2,
                        'double_arrow_token'   => 4,
                        'raw'                  => '\'name\' => $a',
                        'is_empty'             => false,
                        'assignment'           => '$a',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$a',
                        'assignment_token'     => 6,
                        'assignment_end_token' => 6,
                    ],
                    1 => [
                        'key'                  => "'id'",
                        'key_token'            => 9,
                        'key_end_token'        => 9,
                        'double_arrow_token'   => 11,
                        'raw'                  => '\'id\' => $b',
                        'is_empty'             => false,
                        'assignment'           => '$b',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$b',
                        'assignment_token'     => 13,
                        'assignment_end_token' => 13,
                    ],
                    2 => [
                        'key'                  => "'field'",
                        'key_token'            => 16,
                        'key_end_token'        => 16,
                        'double_arrow_token'   => 18,
                        'raw'                  => '\'field\' => $c',
                        'is_empty'             => false,
                        'assignment'           => '$c',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$c',
                        'assignment_token'     => 20,
                        'assignment_end_token' => 20,
                    ],
                ],
            ],
            'long-list-with-empties' => [
                '/* testLongListWithEmptyEntries */',
                \T_LIST,
                [
                    0 => [
                        'raw'                  => '',
                        'is_empty'             => true,
                    ],
                    1 => [
                        'raw'                  => '$a',
                        'is_empty'             => false,
                        'assignment'           => '$a',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$a',
                        'assignment_token'     => 5,
                        'assignment_end_token' => 5,
                    ],
                    2 => [
                        'raw'                  => '',
                        'is_empty'             => true,
                    ],
                    3 => [
                        'raw'                  => '$b',
                        'is_empty'             => false,
                        'assignment'           => '$b',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$b',
                        'assignment_token'     => 10,
                        'assignment_end_token' => 10,
                    ],
                    4 => [
                        'raw'                  => '',
                        'is_empty'             => true,
                    ],
                    5 => [
                        'raw'                  => '$c',
                        'is_empty'             => false,
                        'assignment'           => '$c',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$c',
                        'assignment_token'     => 14,
                        'assignment_end_token' => 14,
                    ],
                    6 => [
                        'raw'                  => '',
                        'is_empty'             => true,
                    ],
                    7 => [
                        'raw'                  => '',
                        'is_empty'             => true,
                    ],
                ],
            ],
            'long-list-multi-line-keyed' => [
                '/* testLongListMultiLineKeyedWithTrailingComma */',
                \T_LIST,
                [
                    0 => [
                        'key'                  => '"name"',
                        'key_token'            => 4,
                        'key_end_token'        => 4,
                        'double_arrow_token'   => 6,
                        'raw'                  => '"name" => $this->name',
                        'is_empty'             => false,
                        'assignment'           => '$this->name',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$this',
                        'assignment_token'     => 8,
                        'assignment_end_token' => 10,
                    ],
                    1 => [
                        'key'                  => '"colour"',
                        'key_token'            => 14,
                        'key_end_token'        => 14,
                        'double_arrow_token'   => 16,
                        'raw'                  => '"colour" => $this->colour',
                        'is_empty'             => false,
                        'assignment'           => '$this->colour',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$this',
                        'assignment_token'     => 18,
                        'assignment_end_token' => 20,
                    ],
                    2 => [
                        'key'                  => '"age"',
                        'key_token'            => 24,
                        'key_end_token'        => 24,
                        'double_arrow_token'   => 26,
                        'raw'                  => '"age" => $this->age',
                        'is_empty'             => false,
                        'assignment'           => '$this->age',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$this',
                        'assignment_token'     => 28,
                        'assignment_end_token' => 30,
                    ],
                    3 => [
                        'key'                  => '"cuteness"',
                        'key_token'            => 34,
                        'key_end_token'        => 34,
                        'double_arrow_token'   => 36,
                        'raw'                  => '"cuteness" => $this->cuteness',
                        'is_empty'             => false,
                        'assignment'           => '$this->cuteness',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$this',
                        'assignment_token'     => 38,
                        'assignment_end_token' => 40,
                    ],
                    4 => [
                        'raw'                  => '',
                        'is_empty'             => true,
                    ],
                ],
            ],
            'short-list-with-keys-nested-lists' => [
                '/* testShortListWithKeysNestedLists */',
                [\T_OPEN_SHORT_ARRAY, \T_OPEN_SQUARE_BRACKET],
                [
                    0 => [
                        'key'                  => "'a'",
                        'key_token'            => 1,
                        'key_end_token'        => 1,
                        'double_arrow_token'   => 3,
                        'raw'                  => '\'a\' => [&$a, $b]',
                        'is_empty'             => false,
                        'assignment'           => '[&$a, $b]',
                        'nested_list'          => true,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => false,
                        'assignment_token'     => 5,
                        'assignment_end_token' => 11,
                    ],
                    1 => [
                        'key'                  => "'b'",
                        'key_token'            => 14,
                        'key_end_token'        => 14,
                        'double_arrow_token'   => 16,
                        'raw'                  => '\'b\' => [$c, &$d]',
                        'is_empty'             => false,
                        'assignment'           => '[$c, &$d]',
                        'nested_list'          => true,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => false,
                        'assignment_token'     => 18,
                        'assignment_end_token' => 24,
                    ],
                ],
            ],
            'long-list-with-array-vars' => [
                '/* testLongListWithArrayVars */',
                \T_LIST,
                [
                    0 => [
                        'raw'                  => '$a[]',
                        'is_empty'             => false,
                        'assignment'           => '$a[]',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$a',
                        'assignment_token'     => 2,
                        'assignment_end_token' => 4,
                    ],
                    1 => [
                        'raw'                  => '$a[0]',
                        'is_empty'             => false,
                        'assignment'           => '$a[0]',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$a',
                        'assignment_token'     => 7,
                        'assignment_end_token' => 10,
                    ],
                    2 => [
                        'raw'                  => '$a[]',
                        'is_empty'             => false,
                        'assignment'           => '$a[]',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$a',
                        'assignment_token'     => 13,
                        'assignment_end_token' => 15,
                    ],
                ],
            ],
            'short-list-multi-line-with-variable-keys' => [
                '/* testShortListMultiLineWithVariableKeys */',
                \T_OPEN_SHORT_ARRAY,
                [
                    0 => [
                        'key'                  => "'a' . 'b'",
                        'key_token'            => 3,
                        'key_end_token'        => 7,
                        'double_arrow_token'   => 9,
                        'raw'                  => '\'a\' . \'b\'        => $a',
                        'is_empty'             => false,
                        'assignment'           => '$a',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$a',
                        'assignment_token'     => 11,
                        'assignment_end_token' => 11,
                    ],
                    1 => [
                        'key'                  => '($a * 2)',
                        'key_token'            => 15,
                        'key_end_token'        => 21,
                        'double_arrow_token'   => 24,
                        'raw'                  => '($a * 2)
        => $b->prop->prop /* comment */ [\'index\']',
                        'is_empty'             => false,
                        'assignment'           => '$b->prop->prop [\'index\']',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$b',
                        'assignment_token'     => 26,
                        'assignment_end_token' => 36,
                    ],
                    2 => [
                        'key'                  => 'CONSTANT & OTHER',
                        'key_token'            => 40,
                        'key_end_token'        => 46,
                        'double_arrow_token'   => 48,
                        'raw'                  => 'CONSTANT & /*comment*/ OTHER => $c',
                        'is_empty'             => false,
                        'assignment'           => '$c',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$c',
                        'assignment_token'     => 50,
                        'assignment_end_token' => 50,
                    ],
                    3 => [
                        'key'                  => '(string) &$c',
                        'key_token'            => 54,
                        'key_end_token'        => 57,
                        'double_arrow_token'   => 59,
                        'raw'                  => '(string) &$c      => &$d["D"]',
                        'is_empty'             => false,
                        'assignment'           => '$d["D"]',
                        'nested_list'          => false,
                        'assign_by_reference'  => true,
                        'reference_token'      => 61,
                        'variable'             => '$d',
                        'assignment_token'     => 62,
                        'assignment_end_token' => 65,
                    ],
                    4 => [
                        'key'                  => 'get_key()[1]',
                        'key_token'            => 69,
                        'key_end_token'        => 74,
                        'double_arrow_token'   => 76,
                        'raw'                  => 'get_key()[1]     => $e',
                        'is_empty'             => false,
                        'assignment'           => '$e',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$e',
                        'assignment_token'     => 78,
                        'assignment_end_token' => 78,
                    ],
                    5 => [
                        'key'                  => '$prop[\'index\']',
                        'key_token'            => 82,
                        'key_end_token'        => 85,
                        'double_arrow_token'   => 87,
                        'raw'                  => '$prop[\'index\']   => $f->prop[\'index\']',
                        'is_empty'             => false,
                        'assignment'           => '$f->prop[\'index\']',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$f',
                        'assignment_token'     => 89,
                        'assignment_end_token' => 94,
                    ],
                    6 => [
                        'key'                  => '$obj->fieldname',
                        'key_token'            => 98,
                        'key_end_token'        => 100,
                        'double_arrow_token'   => 102,
                        'raw'                  => '$obj->fieldname  => ${$g}',
                        'is_empty'             => false,
                        'assignment'           => '${$g}',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => false,
                        'assignment_token'     => 104,
                        'assignment_end_token' => 107,
                    ],
                    7 => [
                        'key'                  => '$simple',
                        'key_token'            => 111,
                        'key_end_token'        => 111,
                        'double_arrow_token'   => 113,
                        'raw'                  => '$simple          => &$h',
                        'is_empty'             => false,
                        'assignment'           => '$h',
                        'nested_list'          => false,
                        'assign_by_reference'  => true,
                        'reference_token'      => 115,
                        'variable'             => '$h',
                        'assignment_token'     => 116,
                        'assignment_end_token' => 116,
                    ],
                    8 => [
                        'raw'                  => '',
                        'is_empty'             => true,
                    ],
                ],
            ],
            'long-list-with-close-parens-in-key' => [
                '/* testLongListWithCloseParensInKey */',
                \T_LIST,
                [
                    0 => [
                        'key'                  => 'get_key()[1]',
                        'key_token'            => 2,
                        'key_end_token'        => 7,
                        'double_arrow_token'   => 9,
                        'raw'                  => 'get_key()[1] => &$e',
                        'is_empty'             => false,
                        'assignment'           => '$e',
                        'nested_list'          => false,
                        'assign_by_reference'  => true,
                        'reference_token'      => 11,
                        'variable'             => '$e',
                        'assignment_token'     => 12,
                        'assignment_end_token' => 12,
                    ],
                ],
            ],
            'long-list-variable-vars' => [
                '/* testLongListVariableVar */',
                \T_LIST,
                [
                    0 => [
                        'raw'                  => '${$drink}',
                        'is_empty'             => false,
                        'assignment'           => '${$drink}',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => false,
                        'assignment_token'     => 3,
                        'assignment_end_token' => 6,
                    ],
                    1 => [
                        'raw'                  => '$foo->{$bar[\'baz\']}',
                        'is_empty'             => false,
                        'assignment'           => '$foo->{$bar[\'baz\']}',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$foo',
                        'assignment_token'     => 9,
                        'assignment_end_token' => 16,
                    ],
                ],
            ],
            'long-list-keyed-with-nested-lists' => [
                '/* testLongListKeyedNestedLists */',
                \T_LIST,
                [
                    0 => [
                        'key'                  => "'a'",
                        'key_token'            => 4,
                        'key_end_token'        => 4,
                        'double_arrow_token'   => 6,
                        'raw'                  => '\'a\' =>
        list("x" => $x1, "y" => $y1)',
                        'is_empty'             => false,
                        'assignment'           => 'list("x" => $x1, "y" => $y1)',
                        'nested_list'          => true,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => false,
                        'assignment_token'     => 9,
                        'assignment_end_token' => 23,
                    ],
                    1 => [
                        'key'                  => "'b'",
                        'key_token'            => 27,
                        'key_end_token'        => 27,
                        'double_arrow_token'   => 29,
                        'raw'                  => '\'b\' =>
        list("x" => $x2, "y" => $y2)',
                        'is_empty'             => false,
                        'assignment'           => 'list("x" => $x2, "y" => $y2)',
                        'nested_list'          => true,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => false,
                        'assignment_token'     => 32,
                        'assignment_end_token' => 46,
                    ],
                ],
            ],
            'parse-error-long-list-mixed-keyed-unkeyed' => [
                '/* testLongListMixedKeyedUnkeyed */',
                \T_LIST,
                [
                    0 => [
                        'raw'                  => '$unkeyed',
                        'is_empty'             => false,
                        'assignment'           => '$unkeyed',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$unkeyed',
                        'assignment_token'     => 2,
                        'assignment_end_token' => 2,
                    ],
                    1 => [
                        'key'                  => '"key"',
                        'key_token'            => 5,
                        'key_end_token'        => 5,
                        'double_arrow_token'   => 7,
                        'raw'                  => '"key" => $keyed',
                        'is_empty'             => false,
                        'assignment'           => '$keyed',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$keyed',
                        'assignment_token'     => 9,
                        'assignment_end_token' => 9,
                    ],
                ],
            ],
            'parse-error-short-list-empties-and-key' => [
                '/* testShortListWithEmptiesAndKey */',
                \T_OPEN_SHORT_ARRAY,
                [
                    0 => [
                        'raw'                  => '',
                        'is_empty'             => true,
                    ],
                    1 => [
                        'raw'                  => '',
                        'is_empty'             => true,
                    ],
                    2 => [
                        'raw'                  => '',
                        'is_empty'             => true,
                    ],
                    3 => [
                        'raw'                  => '',
                        'is_empty'             => true,
                    ],
                    4 => [
                        'key'                  => '"key"',
                        'key_token'            => 6,
                        'key_end_token'        => 6,
                        'double_arrow_token'   => 8,
                        'raw'                  => '"key" => $keyed',
                        'is_empty'             => false,
                        'assignment'           => '$keyed',
                        'nested_list'          => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'variable'             => '$keyed',
                        'assignment_token'     => 10,
                        'assignment_end_token' => 10,
                    ],
                ],
            ],
        ];
    }
}
