<?php

/* testEmptyArray */
$array = array(  );

/* testSingleLineShortArrayNoKeysNoTrailingComma */
$array = [1, 2];

/* testMultiLineLongArrayKeysTrailingComma */
$array = array(
    1 => 'a',
    2 => 'b',
    3 => 'c',
    4 => 'd',
);

/* testMultiLineShortArrayMixedKeysNoKeys */
$array = [
    12 => 'numeric key',
    'value',
    'string' => 'string key',
];

/* testEmptyArrayItem */
// Intentional parse error.
$array = array(1,, 'a' => 2);

/* testShortCircuit */
$array = [1, 'a' => 2, ];

/* testShortList */
[$a, $b] = $array;

/* testLiveCoding */
// This must be the last test in the file!!
$array = array(
