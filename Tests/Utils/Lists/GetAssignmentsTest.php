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

use PHPCSUtils\Internal\Cache;
use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\Lists;

/**
 * Tests for the \PHPCSUtils\Utils\Lists::getAssignments() method.
 *
 * @covers \PHPCSUtils\Utils\Lists::getAssignments
 *
 * @since 1.0.0
 */
final class GetAssignmentsTest extends PolyfilledTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type long/short list; 100000');

        Lists::getAssignments(self::$phpcsFile, 100000);
    }

    /**
     * Test that false is returned when a non-(short) list token is passed.
     *
     * @dataProvider dataNotListToken
     *
     * @param string     $testMarker  The comment which prefaces the target token in the test file.
     * @param int|string $targetToken The token type(s) to look for.
     *
     * @return void
     */
    public function testNotListToken($testMarker, $targetToken)
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type long/short list; T_');

        $target = $this->getTargetToken($testMarker, $targetToken);
        Lists::getAssignments(self::$phpcsFile, $target);
    }

    /**
     * Data provider.
     *
     * @see testNotListToken() For the array format.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataNotListToken()
    {
        return [
            'not-a-list' => [
                'testMarker'  => '/* testNotAList */',
                'targetToken' => \T_OPEN_SHORT_ARRAY,
            ],
            'live-coding' => [
                'testMarker'  => '/* testLiveCoding */',
                'targetToken' => \T_LIST,
            ],
        ];
    }

    /**
     * Test retrieving the details of the variable assignments for a list.
     *
     * @dataProvider dataGetAssignments
     *
     * @param string                                     $testMarker  The comment which prefaces the target token
     *                                                                in the test file.
     * @param int|string                                 $targetToken The token type(s) to look for.
     * @param array<int, array<string, int|string|bool>> $expected    The expected function return value.
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
            if (isset($subset['assignment_token']) && $subset['assignment_token'] !== false) {
                $expected[$index]['assignment_token'] += $stackPtr;
            }
            if (isset($subset['assignment_end_token']) && $subset['assignment_end_token'] !== false) {
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
     * @return array<string, array<string, int|string|array<int|string, int|string|array<string, int|string|bool>>>>
     */
    public static function dataGetAssignments()
    {
        return [
            'long-list-empty' => [
                'testMarker'  => '/* testEmptyLongList */',
                'targetToken' => \T_LIST,
                'expected'    => [],
            ],
            'short-list-empty' => [
                'testMarker'  => '/* testEmptyShortList */',
                'targetToken' => \T_OPEN_SHORT_ARRAY,
                'expected'    => [],
            ],
            'long-list-all-empty' => [
                'testMarker'  => '/* testLongListOnlyEmpties */',
                'targetToken' => \T_LIST,
                'expected'    => [
                    0 => [
                        'raw'                  => '',
                        'assignment'           => '',
                        'is_empty'             => true,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => false,
                        'assignment_end_token' => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    1 => [
                        'raw'                  => '/* comment */',
                        'assignment'           => '',
                        'is_empty'             => true,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => false,
                        'assignment_end_token' => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    2 => [
                        'raw'                  => '',
                        'assignment'           => '',
                        'is_empty'             => true,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => false,
                        'assignment_end_token' => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    3 => [
                        'raw'                  => '',
                        'assignment'           => '',
                        'is_empty'             => true,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => false,
                        'assignment_end_token' => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                ],
            ],
            'short-list-all-empty-with-comment' => [
                'testMarker'  => '/* testShortListOnlyEmpties */',
                'targetToken' => \T_OPEN_SHORT_ARRAY,
                'expected'    => [],
            ],
            'long-list-basic' => [
                'testMarker'  => '/* testSimpleLongList */',
                'targetToken' => \T_LIST,
                'expected'    => [
                    0 => [
                        'raw'                  => '$id',
                        'assignment'           => '$id',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$id',
                        'assignment_token'     => 2,
                        'assignment_end_token' => 2,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    1 => [
                        'raw'                  => '$name',
                        'assignment'           => '$name',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$name',
                        'assignment_token'     => 5,
                        'assignment_end_token' => 5,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                ],
            ],
            'short-list-basic' => [
                'testMarker'  => '/* testSimpleShortList */',
                'targetToken' => \T_OPEN_SHORT_ARRAY,
                'expected'    => [
                    0 => [
                        'raw'                  => '$this->propA',
                        'assignment'           => '$this->propA',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$this',
                        'assignment_token'     => 1,
                        'assignment_end_token' => 3,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    1 => [
                        'raw'                  => '$this->propB',
                        'assignment'           => '$this->propB',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$this',
                        'assignment_token'     => 6,
                        'assignment_end_token' => 8,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                ],
            ],
            'short-list-in-foreach-keyed-with-ref' => [
                'testMarker'  => '/* testShortListInForeachKeyedWithRef */',
                'targetToken' => \T_OPEN_SHORT_ARRAY,
                'expected'    => [
                    0 => [
                        'raw'                  => '\'id\' => & $id',
                        'assignment'           => '$id',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$id',
                        'assignment_token'     => 7,
                        'assignment_end_token' => 7,
                        'assign_by_reference'  => true,
                        'reference_token'      => 5,
                        'key'                  => "'id'",
                        'key_token'            => 1,
                        'key_end_token'        => 1,
                        'double_arrow_token'   => 3,
                    ],
                    1 => [
                        'raw'                  => '\'name\' => $name',
                        'assignment'           => '$name',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$name',
                        'assignment_token'     => 14,
                        'assignment_end_token' => 14,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => "'name'",
                        'key_token'            => 10,
                        'key_end_token'        => 10,
                        'double_arrow_token'   => 12,
                    ],
                ],
            ],
            'long-list-nested' => [
                'testMarker'  => '/* testNestedLongList */',
                'targetToken' => \T_LIST,
                'expected'    => [
                    0 => [
                        'raw'                  => '$a',
                        'assignment'           => '$a',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$a',
                        'assignment_token'     => 2,
                        'assignment_end_token' => 2,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    1 => [
                        'raw'                  => 'list($b, $c)',
                        'assignment'           => 'list($b, $c)',
                        'is_empty'             => false,
                        'is_nested_list'       => true,
                        'variable'             => false,
                        'assignment_token'     => 5,
                        'assignment_end_token' => 11,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                ],
            ],
            'long-list-with-keys' => [
                'testMarker'  => '/* testLongListWithKeys */',
                'targetToken' => \T_LIST,
                'expected'    => [
                    0 => [
                        'raw'                  => '\'name\' => $a',
                        'assignment'           => '$a',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$a',
                        'assignment_token'     => 6,
                        'assignment_end_token' => 6,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => "'name'",
                        'key_token'            => 2,
                        'key_end_token'        => 2,
                        'double_arrow_token'   => 4,
                    ],
                    1 => [
                        'raw'                  => '\'id\' => $b',
                        'assignment'           => '$b',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$b',
                        'assignment_token'     => 13,
                        'assignment_end_token' => 13,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => "'id'",
                        'key_token'            => 9,
                        'key_end_token'        => 9,
                        'double_arrow_token'   => 11,
                    ],
                    2 => [
                        'raw'                  => '\'field\' => $c',
                        'assignment'           => '$c',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$c',
                        'assignment_token'     => 20,
                        'assignment_end_token' => 20,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => "'field'",
                        'key_token'            => 16,
                        'key_end_token'        => 16,
                        'double_arrow_token'   => 18,
                    ],
                ],
            ],
            'long-list-with-empties' => [
                'testMarker'  => '/* testLongListWithEmptyEntries */',
                'targetToken' => \T_LIST,
                'expected'    => [
                    0 => [
                        'raw'                  => '',
                        'assignment'           => '',
                        'is_empty'             => true,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => false,
                        'assignment_end_token' => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    1 => [
                        'raw'                  => '$a',
                        'assignment'           => '$a',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$a',
                        'assignment_token'     => 5,
                        'assignment_end_token' => 5,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    2 => [
                        'raw'                  => '',
                        'assignment'           => '',
                        'is_empty'             => true,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => false,
                        'assignment_end_token' => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    3 => [
                        'raw'                  => '$b',
                        'assignment'           => '$b',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$b',
                        'assignment_token'     => 10,
                        'assignment_end_token' => 10,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    4 => [
                        'raw'                  => '',
                        'assignment'           => '',
                        'is_empty'             => true,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => false,
                        'assignment_end_token' => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    5 => [
                        'raw'                  => '$c',
                        'assignment'           => '$c',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$c',
                        'assignment_token'     => 14,
                        'assignment_end_token' => 14,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    6 => [
                        'raw'                  => '',
                        'assignment'           => '',
                        'is_empty'             => true,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => false,
                        'assignment_end_token' => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    7 => [
                        'raw'                  => '',
                        'assignment'           => '',
                        'is_empty'             => true,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => false,
                        'assignment_end_token' => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                ],
            ],
            'long-list-multi-line-keyed' => [
                'testMarker'  => '/* testLongListMultiLineKeyedWithTrailingComma */',
                'targetToken' => \T_LIST,
                'expected'    => [
                    0 => [
                        'raw'                  => '"name" => $this->name',
                        'assignment'           => '$this->name',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$this',
                        'assignment_token'     => 8,
                        'assignment_end_token' => 10,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => '"name"',
                        'key_token'            => 4,
                        'key_end_token'        => 4,
                        'double_arrow_token'   => 6,
                    ],
                    1 => [
                        'raw'                  => '"colour" => $this->colour',
                        'assignment'           => '$this->colour',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$this',
                        'assignment_token'     => 18,
                        'assignment_end_token' => 20,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => '"colour"',
                        'key_token'            => 14,
                        'key_end_token'        => 14,
                        'double_arrow_token'   => 16,
                    ],
                    2 => [
                        'raw'                  => '"age" => $this->age',
                        'assignment'           => '$this->age',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$this',
                        'assignment_token'     => 28,
                        'assignment_end_token' => 30,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => '"age"',
                        'key_token'            => 24,
                        'key_end_token'        => 24,
                        'double_arrow_token'   => 26,
                    ],
                    3 => [
                        'raw'                  => '"cuteness" => $this->cuteness',
                        'assignment'           => '$this->cuteness',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$this',
                        'assignment_token'     => 38,
                        'assignment_end_token' => 40,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => '"cuteness"',
                        'key_token'            => 34,
                        'key_end_token'        => 34,
                        'double_arrow_token'   => 36,
                    ],
                    4 => [
                        'raw'                  => '',
                        'assignment'           => '',
                        'is_empty'             => true,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => false,
                        'assignment_end_token' => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                ],
            ],
            'short-list-with-keys-nested-lists' => [
                'testMarker'  => '/* testShortListWithKeysNestedLists */',
                'targetToken' => Collections::shortArrayListOpenTokensBC(),
                'expected'    => [
                    0 => [
                        'raw'                  => '\'a\' => [&$a, $b]',
                        'assignment'           => '[&$a, $b]',
                        'is_empty'             => false,
                        'is_nested_list'       => true,
                        'variable'             => false,
                        'assignment_token'     => 5,
                        'assignment_end_token' => 11,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => "'a'",
                        'key_token'            => 1,
                        'key_end_token'        => 1,
                        'double_arrow_token'   => 3,
                    ],
                    1 => [
                        'raw'                  => '\'b\' => [$c, &$d]',
                        'assignment'           => '[$c, &$d]',
                        'is_empty'             => false,
                        'is_nested_list'       => true,
                        'variable'             => false,
                        'assignment_token'     => 18,
                        'assignment_end_token' => 24,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => "'b'",
                        'key_token'            => 14,
                        'key_end_token'        => 14,
                        'double_arrow_token'   => 16,
                    ],
                ],
            ],
            'long-list-with-array-vars' => [
                'testMarker'  => '/* testLongListWithArrayVars */',
                'targetToken' => \T_LIST,
                'expected'    => [
                    0 => [
                        'raw'                  => '$a[]',
                        'assignment'           => '$a[]',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$a',
                        'assignment_token'     => 2,
                        'assignment_end_token' => 4,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    1 => [
                        'raw'                  => '$a[0]',
                        'assignment'           => '$a[0]',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$a',
                        'assignment_token'     => 7,
                        'assignment_end_token' => 10,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    2 => [
                        'raw'                  => '$a[]',
                        'assignment'           => '$a[]',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$a',
                        'assignment_token'     => 13,
                        'assignment_end_token' => 15,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                ],
            ],
            'short-list-multi-line-with-variable-keys' => [
                'testMarker'  => '/* testShortListMultiLineWithVariableKeys */',
                'targetToken' => \T_OPEN_SHORT_ARRAY,
                'expected'    => [
                    0 => [
                        'raw'                  => '\'a\' . \'b\'        => $a',
                        'assignment'           => '$a',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$a',
                        'assignment_token'     => 11,
                        'assignment_end_token' => 11,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => "'a' . 'b'",
                        'key_token'            => 3,
                        'key_end_token'        => 7,
                        'double_arrow_token'   => 9,
                    ],
                    1 => [
                        'raw'                  => '($a * 2)
        => $b->prop->prop /* comment */ [\'index\']',
                        'assignment'           => '$b->prop->prop [\'index\']',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$b',
                        'assignment_token'     => 26,
                        'assignment_end_token' => 36,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => '($a * 2)',
                        'key_token'            => 15,
                        'key_end_token'        => 21,
                        'double_arrow_token'   => 24,
                    ],
                    2 => [
                        'raw'                  => 'CONSTANT & /*comment*/ OTHER => $c',
                        'assignment'           => '$c',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$c',
                        'assignment_token'     => 50,
                        'assignment_end_token' => 50,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => 'CONSTANT & OTHER',
                        'key_token'            => 40,
                        'key_end_token'        => 46,
                        'double_arrow_token'   => 48,
                    ],
                    3 => [
                        'raw'                  => '(string) &$c      => &$d["D"]',
                        'assignment'           => '$d["D"]',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$d',
                        'assignment_token'     => 62,
                        'assignment_end_token' => 65,
                        'assign_by_reference'  => true,
                        'reference_token'      => 61,
                        'key'                  => '(string) &$c',
                        'key_token'            => 54,
                        'key_end_token'        => 57,
                        'double_arrow_token'   => 59,
                    ],
                    4 => [
                        'raw'                  => 'get_key()[1]     => $e',
                        'assignment'           => '$e',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$e',
                        'assignment_token'     => 78,
                        'assignment_end_token' => 78,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => 'get_key()[1]',
                        'key_token'            => 69,
                        'key_end_token'        => 74,
                        'double_arrow_token'   => 76,
                    ],
                    5 => [
                        'raw'                  => '$prop[\'index\']   => $f->prop[\'index\']',
                        'assignment'           => '$f->prop[\'index\']',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$f',
                        'assignment_token'     => 89,
                        'assignment_end_token' => 94,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => '$prop[\'index\']',
                        'key_token'            => 82,
                        'key_end_token'        => 85,
                        'double_arrow_token'   => 87,
                    ],
                    6 => [
                        'raw'                  => '$obj->fieldname  => ${$g}',
                        'assignment'           => '${$g}',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => 104,
                        'assignment_end_token' => 107,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => '$obj->fieldname',
                        'key_token'            => 98,
                        'key_end_token'        => 100,
                        'double_arrow_token'   => 102,
                    ],
                    7 => [
                        'raw'                  => '$simple          => &$h',
                        'assignment'           => '$h',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$h',
                        'assignment_token'     => 116,
                        'assignment_end_token' => 116,
                        'assign_by_reference'  => true,
                        'reference_token'      => 115,
                        'key'                  => '$simple',
                        'key_token'            => 111,
                        'key_end_token'        => 111,
                        'double_arrow_token'   => 113,
                    ],
                    8 => [
                        'raw'                  => '',
                        'assignment'           => '',
                        'is_empty'             => true,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => false,
                        'assignment_end_token' => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                ],
            ],
            'long-list-with-close-parens-in-key' => [
                'testMarker'  => '/* testLongListWithCloseParensInKey */',
                'targetToken' => \T_LIST,
                'expected'    => [
                    0 => [
                        'raw'                  => 'get_key()[1] => &$e',
                        'assignment'           => '$e',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$e',
                        'assignment_token'     => 12,
                        'assignment_end_token' => 12,
                        'assign_by_reference'  => true,
                        'reference_token'      => 11,
                        'key'                  => 'get_key()[1]',
                        'key_token'            => 2,
                        'key_end_token'        => 7,
                        'double_arrow_token'   => 9,
                    ],
                ],
            ],
            'long-list-variable-vars' => [
                'testMarker'  => '/* testLongListVariableVar */',
                'targetToken' => \T_LIST,
                'expected'    => [
                    0 => [
                        'raw'                  => '${$drink}',
                        'assignment'           => '${$drink}',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => 3,
                        'assignment_end_token' => 6,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    1 => [
                        'raw'                  => '$foo->{$bar[\'baz\']}',
                        'assignment'           => '$foo->{$bar[\'baz\']}',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$foo',
                        'assignment_token'     => 9,
                        'assignment_end_token' => 16,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                ],
            ],
            'long-list-keyed-with-nested-lists' => [
                'testMarker'  => '/* testLongListKeyedNestedLists */',
                'targetToken' => \T_LIST,
                'expected'    => [
                    0 => [
                        'raw'                  => '\'a\' =>
        list("x" => $x1, "y" => $y1)',
                        'assignment'           => 'list("x" => $x1, "y" => $y1)',
                        'is_empty'             => false,
                        'is_nested_list'       => true,
                        'variable'             => false,
                        'assignment_token'     => 9,
                        'assignment_end_token' => 23,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => "'a'",
                        'key_token'            => 4,
                        'key_end_token'        => 4,
                        'double_arrow_token'   => 6,
                    ],
                    1 => [
                        'raw'                  => '\'b\' =>
        list("x" => $x2, "y" => $y2)',
                        'assignment'           => 'list("x" => $x2, "y" => $y2)',
                        'is_empty'             => false,
                        'is_nested_list'       => true,
                        'variable'             => false,
                        'assignment_token'     => 32,
                        'assignment_end_token' => 46,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => "'b'",
                        'key_token'            => 27,
                        'key_end_token'        => 27,
                        'double_arrow_token'   => 29,
                    ],
                ],
            ],
            'long-list-keyed-with-commas-in-key' => [
                'testMarker'  => '/* testKeyedLongListWithCommasInKey */',
                'targetToken' => \T_LIST,
                'expected'    => [
                    0 => [
                        'raw'                  => '$map->getKey($type, $urls) => $x',
                        'assignment'           => '$x',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$x',
                        'assignment_token'     => 16,
                        'assignment_end_token' => 16,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => '$map->getKey($type, $urls)',
                        'key_token'            => 4,
                        'key_end_token'        => 12,
                        'double_arrow_token'   => 14,
                    ],
                    1 => [
                        'raw'                  => 'array( $tab, $tabs ) => &$y[\'key\'][$tab]',
                        'assignment'           => '$y[\'key\'][$tab]',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$y',
                        'assignment_token'     => 33,
                        'assignment_end_token' => 39,
                        'assign_by_reference'  => true,
                        'reference_token'      => 32,
                        'key'                  => 'array( $tab, $tabs )',
                        'key_token'            => 20,
                        'key_end_token'        => 28,
                        'double_arrow_token'   => 30,
                    ],
                    2 => [
                        'raw'                  => 'get($year, $day) => $z[$year]',
                        'assignment'           => '$z[$year]',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$z',
                        'assignment_token'     => 53,
                        'assignment_end_token' => 56,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => 'get($year, $day)',
                        'key_token'            => 43,
                        'key_end_token'        => 49,
                        'double_arrow_token'   => 51,
                    ],
                    3 => [
                        'raw'                  => '#[MyAttribute]
    function($a, $b) { return \'key\'; } => $a',
                        'assignment'           => '$a',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$a',
                        'assignment_token'     => 84,
                        'assignment_end_token' => 84,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => '#[MyAttribute] function($a, $b) { return \'key\'; }',
                        'key_token'            => 60,
                        'key_end_token'        => 80,
                        'double_arrow_token'   => 82,
                    ],
                ],
            ],
            'short-list-keyed-with-commas-in-key' => [
                'testMarker'  => '/* testKeyedShortListWithCommasInKeyAndTrailingComma */',
                'targetToken' => \T_OPEN_SHORT_ARRAY,
                'expected'    => [
                    0 => [
                        'raw'                  => '$map->getKey($type, $urls) => $x',
                        'assignment'           => '$x',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$x',
                        'assignment_token'     => 15,
                        'assignment_end_token' => 15,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => '$map->getKey($type, $urls)',
                        'key_token'            => 3,
                        'key_end_token'        => 11,
                        'double_arrow_token'   => 13,
                    ],
                    1 => [
                        'raw'                  => '[$tab, $tabs] => &$y[\'key\'][$tab]',
                        'assignment'           => '$y[\'key\'][$tab]',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$y',
                        'assignment_token'     => 29,
                        'assignment_end_token' => 35,
                        'assign_by_reference'  => true,
                        'reference_token'      => 28,
                        'key'                  => '[$tab, $tabs]',
                        'key_token'            => 19,
                        'key_end_token'        => 24,
                        'double_arrow_token'   => 26,
                    ],
                    2 => [
                        'raw'                  => 'get($year, $day) => $z[$year]',
                        'assignment'           => '$z[$year]',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$z',
                        'assignment_token'     => 49,
                        'assignment_end_token' => 52,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => 'get($year, $day)',
                        'key_token'            => 39,
                        'key_end_token'        => 45,
                        'double_arrow_token'   => 47,
                    ],
                    3 => [
                        'raw'                  => '',
                        'assignment'           => '',
                        'is_empty'             => true,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => false,
                        'assignment_end_token' => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                ],
            ],

            'parse-error-long-list-mixed-keyed-unkeyed' => [
                'testMarker'  => '/* testLongListMixedKeyedUnkeyed */',
                'targetToken' => \T_LIST,
                'expected'    => [
                    0 => [
                        'raw'                  => '$unkeyed',
                        'assignment'           => '$unkeyed',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$unkeyed',
                        'assignment_token'     => 2,
                        'assignment_end_token' => 2,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    1 => [
                        'raw'                  => '"key" => $keyed',
                        'assignment'           => '$keyed',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$keyed',
                        'assignment_token'     => 9,
                        'assignment_end_token' => 9,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => '"key"',
                        'key_token'            => 5,
                        'key_end_token'        => 5,
                        'double_arrow_token'   => 7,
                    ],
                ],
            ],
            'parse-error-short-list-empties-and-key' => [
                'testMarker'  => '/* testShortListWithEmptiesAndKey */',
                'targetToken' => \T_OPEN_SHORT_ARRAY,
                'expected'    => [
                    0 => [
                        'raw'                  => '',
                        'assignment'           => '',
                        'is_empty'             => true,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => false,
                        'assignment_end_token' => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    1 => [
                        'raw'                  => '',
                        'assignment'           => '',
                        'is_empty'             => true,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => false,
                        'assignment_end_token' => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    2 => [
                        'raw'                  => '',
                        'assignment'           => '',
                        'is_empty'             => true,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => false,
                        'assignment_end_token' => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    3 => [
                        'raw'                  => '',
                        'assignment'           => '',
                        'is_empty'             => true,
                        'is_nested_list'       => false,
                        'variable'             => false,
                        'assignment_token'     => false,
                        'assignment_end_token' => false,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                    ],
                    4 => [
                        'raw'                  => '"key" => $keyed',
                        'assignment'           => '$keyed',
                        'is_empty'             => false,
                        'is_nested_list'       => false,
                        'variable'             => '$keyed',
                        'assignment_token'     => 10,
                        'assignment_end_token' => 10,
                        'assign_by_reference'  => false,
                        'reference_token'      => false,
                        'key'                  => '"key"',
                        'key_token'            => 6,
                        'key_end_token'        => 6,
                        'double_arrow_token'   => 8,
                    ],
                ],
            ],
        ];
    }

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @return void
     */
    public function testResultIsCached()
    {
        $methodName  = 'PHPCSUtils\\Utils\\Lists::getAssignments';
        $cases       = $this->dataGetAssignments();
        $testMarker  = $cases['short-list-with-keys-nested-lists']['testMarker'];
        $targetToken = $cases['short-list-with-keys-nested-lists']['targetToken'];
        $expected    = $cases['short-list-with-keys-nested-lists']['expected'];

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
            if (isset($subset['assignment_token']) && $subset['assignment_token'] !== false) {
                $expected[$index]['assignment_token'] += $stackPtr;
            }
            if (isset($subset['assignment_end_token']) && $subset['assignment_end_token'] !== false) {
                $expected[$index]['assignment_end_token'] += $stackPtr;
            }
        }

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = Lists::getAssignments(self::$phpcsFile, $stackPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, $stackPtr);
        $resultSecondRun = Lists::getAssignments(self::$phpcsFile, $stackPtr);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }
}
