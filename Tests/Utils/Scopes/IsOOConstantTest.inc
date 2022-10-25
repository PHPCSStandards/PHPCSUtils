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
    // Prior to PHP 8.2, this was a parse error. Since PHP 8.2, constants are allowed in traits.
    /* testTraitConst */
    const BAR = false;
}

enum Suit: string
{
    /* testEnumConst */
    const FOO = 'bar';

    case Hearts = 'H';
    case Diamonds = 'D';
    case Clubs = 'C';
    case Spades = 'S';
}
