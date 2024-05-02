<?php
/* testStartOfFile */

/* testNamespaceDeclaration */
namespace Vendor\Package\Name;

class Foo {
    /* testInClassBeforeFirstTrackingToken */

    public function bar() {
        /* testNamespaceOperatorInClass */
        return new namespace\Bar();
    }

    /* testInClassAfterFirstTrackingToken */
}

/* testAttribute */
#[AttributeClass([1, 2, 3])]
function baz() {
    /* testInFunctionBeforeFirstTrackingToken */

    $a = namespace\baz();

    /* testInFunctionAfterFirstTrackingToken */
    return $a;
}

/* testAfterScoped */
echo 'something';
