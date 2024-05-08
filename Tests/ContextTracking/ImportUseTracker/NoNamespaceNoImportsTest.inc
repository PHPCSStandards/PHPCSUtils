<?php
/* testStartOfFile */

class Foo {
    /* testInClassBeforeFirstTrackingToken */
    use MyTrait;
    /* testInClassAfterFirstTrackingToken */
}

/* testClosureBeforeFirstTrackingToken */
$closure = function ($baz) use ($var) {
    /* testInClosureAfterFirstTrackingToken */
    return $var * 2;
};

/* testAfterScoped */
echo 'something';
