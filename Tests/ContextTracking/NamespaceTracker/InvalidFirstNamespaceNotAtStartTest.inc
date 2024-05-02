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

/* testNamespaceDeclaration */
// Intentional parse error. Namespace declaration has to be the first statement.
namespace Vendor\Package\Name;

/* testEndOfFile */
echo 'something';
