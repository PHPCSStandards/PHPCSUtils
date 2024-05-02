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
namespace SecondNamespace;

/**
 * Docblock.
 */
function baz() {
    /* testInFunctionBeforeFirstTrackingToken */

    $a = namespace\baz();

    /* testInFunctionAfterFirstTrackingToken */
    return $a;
}

/* testNamespaceDeclarationC */
namespace Package\ThirdNamespace /* testNamespaceDeclarationCEndOfStatement */;

/* testAfterScoped */
echo 'something';
