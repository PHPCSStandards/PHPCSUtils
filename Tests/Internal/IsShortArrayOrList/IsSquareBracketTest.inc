<?php

/* ===== Test making sure that real short array brackets are not recognized as square brackets. ===== */

/* testShortArray */
$array = [1, 2];

/* ===== Tests involving tokens correctly tokenized as square brackets, not short array brackets. ===== */

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

/* testArrayAccessMagicConstant */
$foo = __CLASS__[1];

/* testNullsafeMethodCallDereferencing */
$var = $obj?->function_call()[$x];

$array = [
    'key1' => function($a, $b) {
        ['a' => $a, /* testArrayAccessForShortListKey */ $key['b'] => [$b]] = [$a, [$b]];
        return $a;
    },
    'key2' => [/* testListAssignmentToArrayStringKey */ $a['key'], /* testListAssignmentToArrayEmptyKey */ $a[], $c] = [$a, [$b]];
        return $a;
    },
    'key3' => [ ['a', 'b'] /* testArrayDerefOfShortArrayInShortListAsKey */[0] => $a] = $foo,
];

/* testLiveCoding */
// Intentional parse error. This has to be the last test in the file.
[$a,
