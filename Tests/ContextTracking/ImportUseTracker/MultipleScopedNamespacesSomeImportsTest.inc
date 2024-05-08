<?php
/* testStartOfFile */

/* testNamespaceDeclarationA */
namespace Vendor\Package\FirstNamespace {

    /* testSingleImportUse */
    use function array_map;
    /* testGroupImportUse */
    use Dependency\{
        Something,
        View\Template,
    };

    /* testInFirst */
    class Foo {
        use MyTrait;
    }

/* testNScloser */
}

/* testNamespaceDeclarationB */
namespace /* some comment */ {

    /* testInSecond */
    $closure = function ($baz) use ($var) {
        return $var * 2;
    };
}

/* testNamespaceDeclarationC */
namespace ThirdNamespace {

    /* testMultiImportUse */
    use const PHP_EOL, \DIRECTORY_SEPARATOR, Package\MYCONST;

    /* testInThird */
    function foo() {
        $foo = function_call();
    }
}

/* testAfterScoped */
// Intentional parse error. No code allowed outside namespace brackets in file containing scoped namespaces.
echo SOME_CONSTANT;
