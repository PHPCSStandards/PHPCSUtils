<?php

/* ===== Tests involving tokens correctly not tokenized as short array brackets. ===== */

/* testLongArray */
$b = array( $a );

/* testLongList */
list( $a ) = $b;

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


/* ===== Tests involving tokens correctly tokenized as short array brackets. ===== */

/* testShortArrayNonNested */
$array = [$a];

/* testShortArrayInComparison */
if( [$a, /* testShortArrayNestedInComparison */ [$b]] === $array ) {};

/* testShortArrayInForeach */
foreach ([1, 2, 3] as /* testShortListInForeach */ [$id, $name, $info]) {}

echo 'just here to prevent the below test running into a tokenizer issue tested separately';

/* testShortList */
[$b] = $c;

/* testShortListDetectOnCloseBracket */
[$a, $b] = $c;

/* testShortListWithKeys */
["id" => $id1, "name" => $name1] = $data[0];

/* testShortListWithNesting */
[$a, /* testShortListNested */ [$b]] = array(new stdclass, array(new stdclass));

/* testShortListInForeachWithKey */
foreach ($data as $key => [$id, $name, $info]) {}

foreach ($array as [$a, /* testShortListInForeachNested */ [$b, $c]]) {}

/* testShortListInForeachWithKeysDetectOnCloseBracket */
foreach ($data as ["id" => $id, "name" => $name]) {}

echo 'just here to prevent the below test running into a tokenizer issue tested separately';

/* testShortlistMultiAssign */
$foo = [$baz, $bat] = /* testShortArrayMultiAssign */ [$a, $b];

/* testShortArrayWithNestingAndKeys */
$array = [
    /* testNestedShortArrayWithKeys_1 */
    ["x" => $x1, "y" => $y1],
    /* testNestedShortArrayWithKeys_2 */
    1 => ["x" => $x2, "y" => $y2],
    /* testNestedShortArrayWithKeys_3 */
    'key' => ["x" => $x3, "y" => $y3],
];

/* testShortListWithNestingAndKeys */
[
    /* testNestedShortListWithKeys_1 */
    ["x" => $x1, "y" => $y1],
    /* testNestedShortListWithKeys_2 */
    ["x" => $x2, "y" => $y2],
    /* testNestedShortListWithKeys_3 */
    ["x" => $x3, "y" => $y3],
] = $points;


/* ===== Tests involving various illegal syntaxes/parse errors. ===== */

// Invalid as empty lists are not allowed, but it is short list syntax.
[$x, /* testNestedShortListEmpty */ [], $y] = $a;

[$x, [ $y, /* testDeeplyNestedShortList */ [$z]], $q] = $a;

/* testShortListWithoutVars */
// Invalid list as it doesn't contain variables, but it is short list syntax.
[42] = [1];

/* testShortListNestedLongList */
// Invalid list as mixing short list syntax with list() is not allowed, but it is short list syntax.
[list($a, $b), list($c, $d)] = [[1, 2], [3, 4]];

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
// Intentional parse error. This has to be the last test in the file.
[$a, /* testLiveCodingNested */ [$b]
