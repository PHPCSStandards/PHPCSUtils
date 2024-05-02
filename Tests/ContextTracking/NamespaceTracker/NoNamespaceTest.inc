<?php
/* testStartOfFile */

class Foo {
    /* testInClassBeforeFirstTrackingToken */

    public function bar() {
        /* testNamespaceOperatorInClass */
        return new namespace\Bar();
    }

    /* testInClassAfterFirstTrackingToken */
}

function baz() {
    /* testInFunctionBeforeFirstTrackingToken */

    $a = namespace\baz();

    /* testInFunctionAfterFirstTrackingToken */
    return [ 1, 2, 3 ];
}

/* testAfterScoped */
echo 'something';
