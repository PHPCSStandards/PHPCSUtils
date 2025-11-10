<?php

/* testNotAConstToken */
define('FOO', true);

#[AttributeA, AttributeB(10)]
/* testGlobalConstantWithAttribute */
const GLOBAL_CONSTANT_A = true;

/* testGlobalConstantNoAttribute */
const GLOBAL_CONSTANT_B = true;

class ClassWithConstant {
    /* testInClassNoModifiersWithAttribute */
    #[AttributeA]
    const NO_MODIFIERS = true;

    final protected /* testInClassConstAsNameNoAttribute */ const CONST = true;

    #[WithAttribute(foo: 'bar'), MyAttribute]
    private /* testInClass */ const Type IN_CLASS = MyClass::getInstance();
}

enum EnumWithConstant {
    #[WithoutArgument]#[SingleArgument(0)]#[FewArguments('Hello', 'World')]
    final /* testInEnum */ const IN_ENUM = self::getInstance();
}

trait TraitWithConstant {
    #[AttributeA]

    /** Short docblock */

    #[AttributeB, AttributeC, \AttributeD]

    /**
     * Docblock
     */
    #[AttributeE]


    final public /* testInTrait */ const IN_TRAIT = null;
}

interface InterfaceWithConstant {
    #[
        WithAttribute(/* comment */ 'baz')
    ]
    private /* testInInterface */ const IN_INTERFACE = [];
}

$anonClass = ( $foo == $bar ? new stdClass() :
    new class {
        #[AttributeA] protected final /* testInAnonClass */ const IN_ANON_CLASS = new Unqualified();
    }
);
