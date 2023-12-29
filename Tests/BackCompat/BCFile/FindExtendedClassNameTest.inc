<?php

/* testNotAClass */
function notAClass() {}

class testFECNClass {}

/* testExtendsUnqualifiedClass */
class testFECNExtendedClass extends testFECNClass {}

/* testExtendsFullyQualifiedClass */
class testFECNNamespacedClass extends \PHP_CodeSniffer\Tests\Core\File\testFECNClass {}

/* testNonExtendedClass */
class testFECNNonExtendedClass {}

/* testNonExtendedInterface */
interface testFECNInterface {}

/* testInterfaceExtendsUnqualifiedInterface */
interface testInterfaceThatExtendsInterface extends testFECNInterface{}

/* testInterfaceExtendsFullyQualifiedInterface */
interface testInterfaceThatExtendsFQCNInterface extends \PHP_CodeSniffer\Tests\Core\File\testFECNInterface{}

/* testNestedExtendedClass */
class testFECNNestedExtendedClass {
    public function someMethod() {
        /* testNestedExtendedAnonClass */
        $anon = new class extends testFECNAnonClass {};
    }
}

/* testExtendsPartiallyQualifiedClass */
class testFECNQualifiedClass extends Core\File\RelativeClass {}

/* testClassThatExtendsAndImplements */
class testFECNClassThatExtendsAndImplements extends testFECNClass implements InterfaceA, InterfaceB {}

/* testClassThatImplementsAndExtends */
class testFECNClassThatImplementsAndExtends implements InterfaceA, InterfaceB extends testFECNClass {}

/* testExtendedAnonClass */
$anon = new class( $a, $b ) extends testFECNExtendedAnonClass {};

/* testInterfaceMultiExtends */
interface Multi extends \Package\FooInterface, \BarInterface {};

/* testMissingExtendsName */
class testMissingExtendsName extends { /* missing classname */ } // Intentional parse error.

// Intentional parse error. Has to be the last test in the file.
/* testParseError */
class testParseError extends testFECNClass
