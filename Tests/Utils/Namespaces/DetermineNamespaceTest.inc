<?php

/* Global namespace */
echo namespace\func();

echo ( 'arbitrary parentheses' . ' to skip over' );

/* testNoNamespace */
echo 0;

class Foo {
    function test() {
        /* testNoNamespaceNested */
        echo 0;
    }
}


/* Non-scoped named namespace 1 */
namespace Vendor\Package\Baz;

echo namespace\func();

$var = array_map(
    function () {
        /* testNonScopedNamedNamespace1 */
        echo 0;
    },
    $array
);

$array = [ /*long array */ ];

if (true) {
    // Control structure to skip over.
}

class Foo {
    function test() {
        /* testNonScopedNamedNamespace1Nested */
        echo 0;
    }
}


/* Scoped global namespace */
namespace {
    echo namespace\func();

    /* testGlobalNamespaceScoped */
    echo 0;

    class Bar {
        function test() {
            /* testGlobalNamespaceScopedNested */
            echo 0;
        }
    }
}

echo namespace\fn();

/* testNoNamespaceAfterUnnamedScoped */
echo 0;

$array = array( /*long array to skip over */ );

class Baz {
    function test() {
        /* testNoNamespaceNestedAfterUnnamedScoped */
        echo 0;
    }
}


/* Scoped named namespace */
namespace Vendor\Package\Foo {
    echo namespace\func();

    /* testNamedNamespaceScoped */
    echo 0;

    class ABC {
        function test() {
            /* testNamedNamespaceScopedNested */
            echo 0;
        }
    }
}

echo namespace\fn();

/* testNoNamespaceAfterNamedScoped */
echo 0;

class Baz {
    function test() {
        /* testNoNamespaceNestedAfterNamedScoped */
        echo 0;
    }
}


/* Non-scoped named namespace 2 */
namespace Vendor\Package\Foz;

echo namespace\func();

echo ${$string};

/* testNonScopedNamedNamespace2 */
echo 0;

/**
 * Docblock to skip over.
 */
#[
    AttributeToSkipOver,
    AnotherAttribute,
]
class Foo {
    #[AttributeToSkipOver()] #[AnotherAttribute]
    function test() {
        /* testNonScopedNamedNamespace2Nested */
        echo 0;
    }
}

// Intentional parse error. This has to be the last test in the file.
/* testParseError */
namespace MyNS\ /* comment */
