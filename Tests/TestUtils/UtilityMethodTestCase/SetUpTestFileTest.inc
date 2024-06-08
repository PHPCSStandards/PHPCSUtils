<?php

/* testFindingTarget */
echo $a->method( false );

/* testFindingTargetWithContent */
echo $a->method( 'foo' ), $b->otherMethod( 'bar' );

/* testNotFindingTarget */
echo 'not found';

/* testDelimiter */
echo 'foo';
