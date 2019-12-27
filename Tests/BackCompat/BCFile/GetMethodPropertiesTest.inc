<?php

/* testBasicFunction */
function myFunction() {}

/* testReturnFunction */
function myFunction(array ...$arrays): array
{
    /* testNestedClosure */
    return array_map(function(array $array): int {
        return array_sum($array);
    }, $arrays);
}

class MyClass {
    /* testBasicMethod */
    function myFunction() {}

    /* testPrivateStaticMethod */
    private static function myFunction() {}

    /* testFinalMethod */
    final public function myFunction() {}

    /* testProtectedReturnMethod */
    protected function myFunction() : int {}

    /* testPublicReturnMethod */
    public function myFunction(): array {}

    /* testNullableReturnMethod */
    public function myFunction(): ?array {}

    /* testMessyNullableReturnMethod */
    public function myFunction() /* comment
        */ :  
        /* comment */ ? // phpcs:ignore Stnd.Cat.Sniff -- For reasons.
        array {}

    /* testReturnNamespace */
    function myFunction(): \MyNamespace\MyClass {}

    /* testReturnMultilineNamespace */
    function myFunction(): \MyNamespace /** comment *\/ comment */
                           \MyClass /* comment */
                           \Foo {}
}

abstract class MyClass
{
    /* testAbstractMethod */
    abstract function myFunction();

    /* testAbstractReturnMethod */
    abstract protected function myFunction(): bool;
}

interface MyInterface
{
    /* testInterfaceMethod */
    function myFunction();
}

/* testNotAFunction */
return true;

/* testPhpcsIssue1264 */
function foo() : array {
    echo $foo;
}
