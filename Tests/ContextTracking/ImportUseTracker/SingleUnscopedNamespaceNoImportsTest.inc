<?php
/* testStartOfFile */

/* testNamespaceDeclaration */
namespace Vendor\Package\Name;

class Foo {
    /* testInClassBeforeFirstTrackingToken */
    use MyTrait;
    /* testInClassAfterFirstTrackingToken */
}

/* testClosureBeforeFirstTrackingToken */
$closure = function ($baz) use ($var) {
    /* testInClosureAfterFirstTrackingToken */
    return [ 1, 2, 3 ];
};

/* testAfterScoped */
echo 'something';
