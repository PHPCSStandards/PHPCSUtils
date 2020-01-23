<?php

/* testShortArray */
$array = ['foo' => 'bar'];

/* testArrayAccess */
echo $array['index'];

/* testLongList */
list($a, /* testNestedLongList */ list ( $b )) = $array;

/* testShortList */
[$a, /* testNestedShortList */ [$b]] = $array;

/* testListWithCommentsAndAnnotations */
list // Comment.
    /* phpcs:ignore Stnd.Cat.Sniff -- For reasons. */
    (
        $a,
        $b,
    ) = $array;

/* testParseError */
// Intentional parse error. This has to be the last test in the file.
list( $a
