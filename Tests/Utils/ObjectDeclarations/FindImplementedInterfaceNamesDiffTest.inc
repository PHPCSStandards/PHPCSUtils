<?php

/* testDeclarationWithComments */
class testDeclarationWithComments
    /* comment */
    implements
        //phpcs:ignore Standard.Cat.Sniff -- For reasons
        \Vendor
        /* comment */
        \Package\Core
        //phpcs:disable Standard.Cat.Sniff -- For reasons
        \SubDir         \         SomeInterface,
        // comment
        InterfaceB {}

/* testDeclarationMultiImplementedNamespaceOperator */
class testMultiImplementedNSOperator implements namespace\testInterfaceA, namespace\testInterfaceB {}

/* testMultiImplementedStrayComma */
class testMultiImplementedStrayComma implements testInterfaceA, , testInterfaceB {} // Intentional parse error.
