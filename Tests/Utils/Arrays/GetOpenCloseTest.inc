<?php

/* testShortList */
['foo' => $bar] = $array;

/* testArrayAccess */
echo $array['index'];

/* testLongArray */
$array = array($a, /* testNestedLongArray */ array ( $b ));

/* testShortArray */
$array = [$a, /* testNestedShortArray */ [$b]];

/* testArrayWithCommentsAndAnnotations */
$array = array // Comment.
    (
        0 => $a,
    /* phpcs:ignore Stnd.Cat.Sniff -- For reasons. */
        1 => $b,
    );

/* testParseError */
// Intentional parse error. This has to be the last test in the file.
array( $a
