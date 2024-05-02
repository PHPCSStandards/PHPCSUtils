<?php
/* testStartOfFile */

/* testNamespaceDeclaration */
// Intentional parse error. Missing namespace scope closer.
namespace Vendor\Package\FirstNamespace {

class Foo {
    /* testInClassBeforeFirstTrackingToken */

    public function bar() {
        /* testNamespaceOperatorInClass */
        return new namespace\Bar();
    }
}

/* testEndOfFile */
