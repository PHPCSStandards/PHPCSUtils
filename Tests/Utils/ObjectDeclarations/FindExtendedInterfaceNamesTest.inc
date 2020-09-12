<?php

/* testNotAnInterface */
function notAnInterface() {}

/* testInterface */
interface testInterface {}

/* testExtendedInterface */
interface testExtendedInterface extends testInterface {}

/* testMultiExtendedInterface */
interface testMultiExtendedInterface extends testInterfaceA, testInterfaceB {}

/* testExtendedNamespacedInterface */
interface testNamespacedInterface extends \PHPCSUtils\Tests\ObjectDeclarations\testInterface {}

/* testMultiExtendedNamespacedInterface */
interface testMultiNamespacedInterface extends \PHPCSUtils\Tests\ObjectDeclarations\testInterfaceA, \PHPCSUtils\Tests\ObjectDeclarations\testFEINInterfaceB {}

/* testMultiExtendedInterfaceWithComments */
interface testMultiExtendedInterfaceWithComments
    extends
        /* a comment */
        testInterfaceA,
        \PHPCSUtils\Tests /* comment */ \Some \ Declarations \testInterfaceB,
        \testInterfaceC /* comment */
{
}

/* testNamespaceRelativeQualifiedInterface */
interface testQualifiedInterfaces extends Core\File\RelativeInterface, Another\RelativeInterface {}

/* testExtendsUsingNamespaceOperator */
interface testWithNSOperator extends namespace\BarInterface, namespace\Sub\SomeOther {}

// Intentional parse error. Has to be the last test in the file.
/* testParseError */
interface testParseError extends testInterface
