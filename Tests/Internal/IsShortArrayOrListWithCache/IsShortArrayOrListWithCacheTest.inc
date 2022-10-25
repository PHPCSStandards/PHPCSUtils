<?php

/* ===== Tests involving tokens which are not square brackets. ===== */

/* testLongArray */
$b = array( $a );

/* testLongList */
list( $a ) = $b;

/* ===== Tests involving tokens which *are* square brackets. ===== */

/* testShortArray */
$array = [1, 2];

/* testShortList */
[$a, $b] = $list;

/* testSquareBrackets */
$foo = $bar['key'];

/* testParseError */
something(]
