<?php

/* ===== Tests involving tokens which are not square brackets. ===== */

/* testLongArray */
$b = array( $a );

/* testLongList */
list( $a ) = $b;

/* ===== Tests to make sure all supported brackets are accepted. ===== */

/* testShortArray */
$array = [1, 2];

/* testShortList */
[$a, $b] = $list;

/* testSquareBrackets */
$foo = $bar['key'];
