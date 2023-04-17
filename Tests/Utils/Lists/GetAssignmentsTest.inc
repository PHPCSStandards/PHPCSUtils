<?php

/* testNotAList */
$a = [1, 2, 3];

/* testEmptyLongList */
list(
     ) = $array; // Intentional fatal error in PHP 7+.

/* testEmptyShortList */
[] = $array; // Intentional fatal error in PHP 7+.

/* testLongListOnlyEmpties */
list(, /* comment */ , , ) = $array; // Intentional fatal error in PHP 7+.

/* testShortListOnlyEmpties */
[
   // phpcs:ignore Standard.Cat.Sniff -- for reasons
] = $array; // Intentional fatal error in PHP 7+.

/* testSimpleLongList */
list($id, $name) = $data;

/* testSimpleShortList */
[$this->propA, $this->propB] = $array;

/* testShortListInForeachKeyedWithRef */
foreach ($data as ['id' => & $id, 'name' => $name]) {}

/* testNestedLongList */
list($a, list($b, $c)) = array(1, array(2, 3));

/* testLongListWithKeys */
list('name' => $a, 'id' => $b, 'field' => $c) = ['name' => 1, 'id' => 2, 'field' => 3];

/* testLongListWithEmptyEntries */
list( , $a, , $b,, $c, ,) = [1, 2, 3, 4, 5, 6, 7, 8];

/* testLongListMultiLineKeyedWithTrailingComma */
class Foo {
    function bar() {
        list(
            "name" => $this->name,
            "colour" => $this->colour,
            "age" => $this->age,
            "cuteness" => $this->cuteness,
        ) = $attributes;
    }
}

/* testShortListWithKeysNestedLists */
['a' => [&$a, $b], 'b' => [$c, &$d]] = $array;

/* testLongListWithArrayVars */
list($a[], $a[0], $a[]) = [1, 2, 3];

/* testShortListMultiLineWithVariableKeys */
[
    'a' . 'b'        => $a,
    ($a * 2)
        => $b->prop->prop /* comment */ ['index'],
    CONSTANT & /*comment*/ OTHER => $c,
    (string) &$c      => &$d["D"],
    get_key()[1]     => $e,
    $prop['index']   => $f->prop['index'],
    $obj->fieldname  => ${$g},
    $simple          => &$h,
] = $array;

/* testLongListWithCloseParensInKey */
list(get_key()[1] => &$e) = [1, 2, 3];

/* testLongListVariableVar */
list( ${$drink}, $foo->{$bar['baz']} ) = $infoArray;

/* testLongListKeyedNestedLists */
list(
    'a' =>
        list("x" => $x1, "y" => $y1),
    'b' =>
        list("x" => $x2, "y" => $y2)
) = $points;

/* testKeyedLongListWithCommasInKey */
list(
    $map->getKey($type, $urls) => $x,
    array( $tab, $tabs ) => &$y['key'][$tab],
    get($year, $day) => $z[$year],
    #[MyAttribute]
    function($a, $b) { return 'key'; } => $a
) = $array;

/* testKeyedShortListWithCommasInKeyAndTrailingComma */
[
    $map->getKey($type, $urls) => $x,
    [$tab, $tabs] => &$y['key'][$tab],
    get($year, $day) => $z[$year],
] = $array;

/* testLongListMixedKeyedUnkeyed */
// Parse error, but not our concern.
list($unkeyed, "key" => $keyed) = $array;

/* testShortListWithEmptiesAndKey */
// Empty elements are not allowed where keys are specified. Parse error, but not our concern.
[,,,, "key" => $keyed] = $array;


/* testLiveCoding */
// Intentional parse error. This has to be the last test in the file.
list(
