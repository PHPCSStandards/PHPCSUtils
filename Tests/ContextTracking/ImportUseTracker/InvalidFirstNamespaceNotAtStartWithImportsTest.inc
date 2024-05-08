<?php
/* testStartOfFile */

/* testSingleImportUse */
use Package\ClassName;

class Foo {
    /* testInClassBeforeFirstTrackingToken */
    use MyTrait;
    /* testInClassAfterFirstTrackingToken */
}

/* testNamespaceDeclaration */
// Intentional parse error. Namespace declaration has to be the first statement.
namespace Vendor\Package\Name;

/* testGroupImportUse */
use Another\{
    function do_something,
    Some\Thing,
};

/* testClosureBeforeFirstTrackingToken */
$closure = function ($baz) use ($var) {
    /* testInClosureAfterFirstTrackingToken */
    return $var * 2;
};

/* testAfterScoped */
echo 'something';
