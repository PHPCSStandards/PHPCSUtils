<?php

/* ===== Tests involving brackets in a condition, but not foreach. ===== */

/* testNotInForeach */
if ($array === [1, 2]) {}


/* ===== Tests involving brackets within a foreach condition DIRECTLY handled by isInForeach(). ===== */

/* testShortArrayInForeach */
foreach ([1, 2, 3] as /* testShortListInForeach */ [$id, $name, $info]) {}

/* testShortArrayInForeachWithNestedShortList */
foreach([$a, $b, [$d, $g, [$h]] = $array] as $c) {}

/* testShortArrayInForeachWithAssignment */
foreach($array = [$a, $b, [$d, $g, [$h]]] as $c) {}

/* testShortListInForeachWithKey */
foreach ($data as $key => [$id, $name, $info]) {}

/* testShortListInForeachWithListKeys */
foreach ($data as ["id" => $id, "name" => $name]) {}

/* testShortlistInForeachWithReference */
foreach ($array as [&$a, $b]) {}


/* ===== Tests involving brackets within a foreach condition INDIRECTLY handled by isInForeach(). ===== */
// Handled by the function for the outer set of brackets via walkOutside().

foreach ([/* testShortArrayInForeachNestedAtStart */ [$b, $c], $d]      as $k => $v) {}
foreach ([$a, /* testShortArrayInForeachNestedInMiddle */ [$b, $c], $d] as $k => $v) {}
foreach ([$a, /* testShortArrayInForeachNestedAtEnd */ [$b, $c]]        as $k => $v) {}

foreach ([$a, /* testShortArrayEmptyInForeachNestedInMiddle */ 'a' => [], $d] as $k => $v) {}

foreach ($array as [/* testShortListInForeachNestedAtStart */ [$b, $c], $d]) {}
foreach ($array as [$a, /* testShortListInForeachNestedInMiddle */ [$b, $c], $d]) {}
foreach ($array as [$a, /* testShortListInForeachNestedAtEnd */ [$b, $c]]) {}


/* ===== Tests involving brackets within a foreach condition NOT handled by isInForeach(). ===== */

foreach ($array as $key => $value[$this->get_key(/* testShortArrayInFunctionCallInForeach */ [$b])]) {}

foreach ($array as [/* testShortArrayAsKeyInShortList1 */[1,2,3][$a] => $b, /* testShortArrayAsKeyInShortList2 */ [$d, $e][$a] => $c]) {}

foreach([$a, $b, /* testShortListNestedInShortArrayInForeach */ [$d, $g, /* testNestedShortListNestedInShortArrayInForeach */ [$h]] = $array] as $c) {}

/* testShortArrayAsKeyAfterAs */
// Fatal error. Cannot use temporary expression in write context.
foreach ($array as [1,2,3][$a] => [$b, $c]) {}

/* testParseError */
// Parse error: missing close square bracket.
foreach ($array as [$a => [$b, $c], $d) {}

/* testForeachWithoutAs */
// Parse error.
foreach([1,2,3]) {}
