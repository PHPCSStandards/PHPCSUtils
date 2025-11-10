<?php

/* testNotAFunctionToken */
echo 'hello';

#[AttributeA, AttributeB(10)]
/* testGlobalFunctionWithAttribute */
function globalFunctionA() {}

/* testGlobalFunctionNoAttribute */
function globalFunctionB() {}

$closure = #[AttributeA] /* testClosureAttributeSameLine */ function() { do_something(); };
$closure = /* testClosureNoAttribute */ function() { do_something(); };
$closure = #[AttributeA]
    static /* testStaticClosureAttribute */ function() { do_something(); };

$arrow = #[AttributeA] /* testArrowFnAttributeSameLine */ fn() => do_something();
$arrow = /* testArrowFnNoAttribute */ fn() => do_something();
$arrow = #[AttributeA]
    static /* testStaticArrowFnAttribute */ fn() => do_something();

class ClassWithMethod {
    /* testInClassNoModifiersWithAttribute */
    #[AttributeA]
    function noModifiers() {}

    final protected /* testInClassNoAttribute */ function noAttribute() {}

    #[WithAttribute(foo: 'bar'), MyAttribute]
    private /* testInClass */ function inClass() {}
}

enum EnumWithMethod {
    #[AttributeA]
    final /* testInEnum */ function inEnum() {}
}

trait TraitWithMethod {

    #[AttributeA]

    /** Short docblock */

    #[AttributeB, AttributeC, namespace\AttributeD]

    /**
     * Docblock
     */
    #[AttributeE]


    abstract static public /* testInTrait */ function inTrait();
}

interface InterfaceWithMethod {
    #[
        WithAttribute(/* comment */ 'baz')
    ]
    public /* testInInterface */ function inInterface();
}

$anonClass = ( $foo == $bar ? new stdClass() :
    new class {
        #[WithoutArgument]#[SingleArgument(0)]#[FewArguments('Hello', 'World')]
        protected static /* testInAnonClass */ function inAnonClass() {}
    }
);
