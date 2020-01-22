<?php

/* testGlobalConst */
const BAR = false;

function something()
{
    /* testFunctionConst */
    // Intentional parse error. Constants cannot be declared using the const keyword from within functions.
    const BAR = false;
}

class MyClass {
    /* testClassConst */
    const FOO = true;

    public function something()
    {
        /* testClassMethodConst */
        // Intentional parse error. Constants cannot be declared using the const keyword from within functions.
        const BAR = false;
    }
}

$a = new class {
    /* testAnonClassConst */
    public const FOO = true;
};

interface MyInterface {
    /* testInterfaceConst */
    const FOO = true;
}

trait MyTrait {
    // Intentional parse error. Constants are not allowed in traits.
    /* testTraitConst */
    const BAR = false;
}
