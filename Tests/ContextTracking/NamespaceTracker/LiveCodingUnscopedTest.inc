<?php
/* testStartOfFile */

/* testNamespaceDeclarationA */
namespace Vendor\Package\FirstNamespace;

class Foo {
    /* testInClassBeforeFirstTrackingToken */

    public function bar() {
        /* testNamespaceOperatorInClass */
        return new namespace\Bar();
    }

    /* testInClassAfterFirstTrackingToken */
}

/* testNamespaceDeclarationB */
// Intentional parse error. Unfinished unscoped namespace declaration.
namespace SecondNamespace

/* testEndOfFile */
