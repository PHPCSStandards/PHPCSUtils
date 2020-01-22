<?php

/* testLongArray */
$b = array( $a );

/* testArrayAssignmentEmpty */
$b[] = $c;

/* testArrayAssignmentStringKey */
$b['key'] = $c;

/* testArrayAssignmentIntKey */
$b[10] = $c;

/* testArrayAssignmentVarKey */
$b[$a] = $c;

/* testArrayAccessStringKey */
function_call($array['key']);

/* testArrayAccessIntKey1 */
function_call($array[1]/* testArrayAccessIntKey2 */[1]);

/* testArrayAccessFunctionCall */
return function_call()[1];

/* testArrayAccessConstant */
return MY_CONSTANT[1];

/* testNonNestedShortArray */
$array = [$a];

/* testInComparison */
if( [$a, /* testNestedInComparison */ [$b]] === $array ) {};

/* testShortArrayInForeach */
foreach ([1, 2, 3] as /* testShortListInForeach */ [$id]) {}

/* testMultiAssignShortlist */
$foo = [$baz, $bat] = /* testMultiAssignShortArray */ [$a, $b];

/* testShortArrayWithNestingAndKeys */
$array = [
    /* testNestedShortArrayWithKeys_1 */
    ["x" => $x1, "y" => $y1],
    /* testNestedShortArrayWithKeys_2 */
    1 => ["x" => $x2, "y" => $y2],
    /* testNestedShortArrayWithKeys_3 */
    'key' => ["x" => $x3, "y" => $y3],
];

/* testNestedAnonClassWithTraitUseAs */
// Parse error, but not our concern, it is short array syntax.
array_map(function($a) {
    return new class() {
        use MyTrait {
            MyTrait::functionName as [];
        }
    };
}, $array);

/* testParseError */
// Parse error, but not our concern, it is short array syntax.
use Something as [$a];

/* testLiveCoding */
// This has to be the last test in the file.
[$a, /* testLiveCodingNested */ [$b]
