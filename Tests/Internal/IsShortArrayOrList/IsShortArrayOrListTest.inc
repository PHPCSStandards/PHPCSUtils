<?php

/* ===== Tests involving tokens correctly tokenized as square brackets. ===== */

/* testSquareBrackets */
$b[] = $c;

/* ===== Tests involving tokens correctly tokenized as short array brackets. ===== */

/* testShortArrayNonNested */
$array = [$a];

/* testShortArrayInComparison */
if( [$a, /* testShortArrayNestedInComparison */ [$b]] === $array ) {};

/* testShortArrayUnionFirst */
$array = [1, 2] + /* testShortArrayUnionSecond */ [3, 4];

/* testShortArrayEqualFirst */
if([1, 2] == /* testShortArrayEqualSecond */ [3, 4]) {}

/* testShortArrayIdenticalFirst */
if([1, 2] === /* testShortArrayIdenticalSecond */ [3, 4]) {}

/* testShortArrayNotEqualFirst */
if([1, 2] != /* testShortArrayNotEqualSecond */ [3, 4]) {}

/* testShortArrayNotEqualBracketsFirst */
if([1, 2] <> /* testShortArrayNotEqualBracketsSecond */ [3, 4]) {}

/* testShortArrayNonIdenticalFirst */
if([1, 2] !== /* testShortArrayNonIdenticalSecond */ [3, 4]) {}

foreach ([1, 2, 3] as /* testShortListInForeach */ [$id, $name, $info]) {}

echo 'just here to prevent the below test running into a tokenizer issue tested separately';

/* testShortList */
[$b] = $c;

/* testShortListMultiItem */
[$a, $b] = $c;

/* testShortListWithKeys */
["id" => $id1, "name" => $name1] = $data[0];

/* testShortListWithNesting */
[$a, /* testShortListNested */ [$b]] = array(new stdclass, array(new stdclass));

/* testShortlistMultiAssign */
$foo = [$baz, $bat] = /* testShortArrayMultiAssign */ [$a, $b] + [$c, $d];

/* testShortArrayWithNestingAndKeys */
$array = [
    /* testNestedShortArrayWithKeys_1 */
    ["x" => $x1, "y" => $y1],
    /* testNestedShortArrayWithKeys_2 */
    1 => ["x" => $x2, "y" => $y2],
    /* testNestedShortArrayWithKeys_3 */
    'key' => ["x" => $x3, "y" => $y3],
    /* testNestedShortArrayPHP74UnpackingWithoutKeys */
    ...[$x3, $y3],
    /* testNestedShortArrayPHP81UnpackingWithKeys */
    ...["x" => $x3, "y" => $y3],
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

[$x, [ $y, /* testDeeplyNestedShortList */ [$z]], $q] = $a;

if ( true ) {
    /* testShortListInBracedControlStructure */
    [ $a ] = [ 'hi' ];
}

if ( true ) :
    /* testShortListInAlternativeControlStructure */
    [ $a ] = [ 'hi' ];
endif;

return $a ?? '';

$array = [
    /* testShortArrayInShortArrowFunction */
    'key' => fn($p) : array => [$p],

    // Prevent going straight to outer brackets for last item.
    'skip' => [$b],
];


/* ===== Tests involving various illegal syntaxes/parse errors. ===== */

// Invalid as empty lists are not allowed, but it is short list syntax.
[$x, /* testNestedShortListEmpty */ [], $y] = $a;

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

// Intentional parse error. This has to be the last test in the file.
[$a, /* testLiveCodingNested */ [$b]
