<?php

/* testNoParams */
$foo = [];

/* testLongArray */
$foo = array( 1, 2 );

/* testShortArray */
$foo = [ 'a' => 1, 'b' => 2 ];

/* testShortList */
[ 'a' => $a, 'b' => $b ] = $array;

/* testArrayAssign */
$array[] = 'foo';

/* testArrayAccess */
$foo = $array[$keys['key']];

/* testShortListWithEmptyItemsBefore */
[, , $a] = $array;

/* testShortListWithEmptyItemsAfter */
[$a,,] = $array;

/* testShortListWithAllEmptyItems */
// Not allowed, but not our concern.
[,,] = $array;
