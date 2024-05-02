<?php

/* testStartOfFile */

/* testNamespaceDeclarationA */
namespace Vendor\Package\FirstNamespace {

    /* testInFirst */
    class Foo extends namespace\Bar {}

/* testNScloser */
}

/* testNamespaceDeclarationB */
namespace /* some comment */ {

    /* testInSecond */
    echo namespace\CONSTANT_NAME;
}

/* testNamespaceDeclarationC */
namespace ThirdNamespace /* testNamespaceDeclarationCOpener */{

    /* testInThird */
    function foo() {
        $foo = namespace\function_call();
    }
}

/* testAfterScoped */
// Intentional parse error. No code allowed outside namespace brackets in file containing scoped namespaces.
echo namespace\SOME_CONSTANT;
