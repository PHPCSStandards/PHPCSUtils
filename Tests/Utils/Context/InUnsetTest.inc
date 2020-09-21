<?php

/* testNotUnsetMethodCall */
$obj->unset($target);

function foo() {
    /* testOwnerNotUnset */
    if (isset($target) && $target + 1 > 10) {
        /* testInUnset */
        Unset($something['a'], $target->prop, $else);
    }
}

/* testParseError */
// This has to be the last test in the file.
unset($target
