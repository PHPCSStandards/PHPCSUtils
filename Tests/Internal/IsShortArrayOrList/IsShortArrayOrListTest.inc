<?php

/* ===== Tests involving tokens correctly tokenized as short array brackets. ===== */

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

/* testShortListWithShortArrayAsKey */
[/* testShortArrayAsKeyForShortList */[1,2,3][$a] => /* testShortListWithShortArrayAsKeyNested */ [$a, $b]] = $array;

/* testShortListWithShortArrayInKey */
// Please sack anyone who writes code like this.
[
    /* testShortArrayInKeyForShortListA */
    (function($a = [1,2,3]) {
        return $a[$b];
    })(/* testShortArrayInKeyForShortListB */[1,2,3]) => /* testShortListWithShortArrayInKeyNested */ [$a, $b]
] = $array;

[/* testNestedShortListWithEmpties */[,,$a]] = $array;

$array = [
    'key1' => function($a, $b) {
        /* testShortListInClosureInShortArrayValue */
        ['a' => $a, $key['b'] => /* testNestedShortListInClosureInShortArrayValue */ [$b]] =
            /* testShortArrayInClosureInShortArrayValue */
            [$a, /* testNestedShortArrayInClosureInShortArrayValue */ [$b]];
        return $a;
    },
    /* testShortArrayInFunctionCallInShortArrayValue */
    'key2' => function_call([$a, $b]),
    /* testPlainShortListInShortArrayValue */
    'key3' => [$a, $b] = $foo,
    /* testPlainShortListInShortArrayValueNoKey */
    [$a, $b] = $foo,
    /* testKeyedShortListInShortArrayValue1 */
    'key4' => [ /* testShortArrayInShortListAsKey */['a', 'b'] [0] => $a] = $foo,
    /* testKeyedShortListInShortArrayValue2 */
    'key5' => [ get_key( /* testShortArrayInFunctionCallInShortListKey */ ['a', 'b']) => $a] = $foo,
    'key6' => [ $a, /* testEmptyShortListInShortListInShortArrayValue */ [], $b ] = $foo,
];

$keys[ /* testOuterShortArrayStopAtBrackets */ [$a, $b, $c]['index'] ] = 'value';

$array = [
    $keys[ /* testShortArrayInShortArrayKeyStopAtBrackets */ [$a, $b, $c]['index'] ] => 'value',
];

[
    $keys[ /* testShortArrayInShortListKeyStopAtBrackets */ [$a, $b, $c]['index'] ] => $a,
] = $array;

/* testRiskySyntaxCombiButNonNested-FunctionCall */
$var = function_call($arg, [$arg], $arg);

/* testRiskySyntaxCombiButNonNested-ClosureCall */
$var = $closure($arg, [$arg], $arg);

/* testRiskySyntaxCombiButNonNested-FnCall */
$var = $fn($arg, [$arg], $arg);

/* testRiskySyntaxCombiButNonNested-Echo */
echo $arg, [$arg], $arg;

$array = [
    /* testShortArrayInShortArrowFunction */
    'key' => fn($p) : array => [$p],

    // Prevent going straight to outer brackets for last item.
    'skip' => [$b],
];

$match = match($foo) {
    /* testShortArrayConditionInMatchExpression */
    [$bar]  => 10,
    default => 20,
};

$match = match($foo) {
    [$bar, /* testNestedShortArrayConditionInMatchExpression */ [$baz], $bal] => 10,
    default => 20,
};

$match = match($foo) {
    /* testShortArrayReturnedFromMatchExpression */
    10 => [$bar],
    default => 20,
};

$match = match($foo) {
    10 => [$bar, /* testNestedShortArrayReturnedFromMatchExpression */ [$baz], $bal],
    default => 20,
};

$match = match($foo) {
    /* testShortListConditionInMatchExpression */
    [$bar] = $array => 10,
    default => 20,
};

$match = match($foo) {
    [$bar, /* testNestedShortListConditionInMatchExpression */ [$baz], $bal] = $array => 10,
    default => 20,
};

$match = match($foo) {
    /* testShortListReturnedFromMatchExpression */
    10 => [$bar] = $array,
    default => 20,
};

$match = match($foo) {
    10 => [$bar, /* testNestedShortListReturnedFromMatchExpression */ [$baz], $bal] = $array,
    default => 20,
};

/* testShortArrayNestedInLongArray */
$val = array($a, [$b, $c], $d);


/* ===== Tests involving various illegal syntaxes/parse errors. ===== */

// Invalid as empty lists are not allowed, but it is short list syntax.
[$x, /* testNestedShortListEmpty */ [], $y] = $a;

/* testShortListWithoutVars */
// Invalid list as it doesn't contain variables, but it is short list syntax.
[42] = [1];

/* testShortListNestedLongList */
// Invalid list as mixing short list syntax with list() is not allowed, but it is short list syntax.
[list($a, $b), list($c, $d)] = [[1, 2], [3, 4]];

/* testLongListNestedShortList */
// Invalid list as mixing short list syntax with list() is not allowed, but it is short list syntax.
list($a, [$b, $c], $d) = $array;

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
