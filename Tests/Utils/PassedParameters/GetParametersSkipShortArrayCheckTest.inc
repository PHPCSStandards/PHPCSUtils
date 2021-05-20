<?php

/* testNoParams */
$foo = [];

/* testLongArray */
$foo = array( 1, 2 );

/* testShortArray */
$foo = [ 'a' => 1, 'b' => 2 ];

/* testShortList */
[ 'a' => 1, 'b' => 2 ] = $array;

/* testArrayAssign */
$array[] = 'foo';

/* testArrayAccess */
$foo = $array[$keys['key']];
