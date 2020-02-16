<?php

/* testInvalidTokenPassed */
echo MY_CONSTANT;

/* testClosure */
$closure = function() {};

/* testAnonClassWithParens */
$anonClass = new class() {};

/* testAnonClassWithParens2 */
$class = new class() {
    private $property = 'test';
    public function test() {}
};

/* testAnonClassWithoutParens */
$anonClass = new class {};

/* testAnonClassExtendsWithoutParens */
$class = new class extends SomeClass {
    private $property = 'test';
    public function test() {}
};

/* testFunction */
function functionName() {}

/* testClass */
abstract class ClassName {
    /* testMethod */
    public function methodName() {}

    /* testAbstractMethod */
    abstract protected function abstractMethodName();
}

/* testExtendedClass */
class ExtendedClass extends Foo {}

/* testInterface */
interface InterfaceName {}

/* testTrait */
trait TraitName {
    /* testFunctionEndingWithNumber */
    function ValidNameEndingWithNumber5(){}
}

/* testClassWithNumber */
class ClassWith1Number implements SomeInterface {}

/* testInterfaceWithNumbers */
interface InterfaceWith12345Numbers extends AnotherInterface {}

/* testClassWithCommentsAndNewLines */
class /* comment */

// phpcs:ignore Standard.Cat.SniffName -- for reasons
    ClassWithCommentsAndNewLines {}

/* testFunctionFn */
function fn() {}

/* testLiveCoding */
// Intentional parse error. This has to be the last test in the file.
function // Comment.
