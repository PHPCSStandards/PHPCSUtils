<?php

/* testNotAnOOToken */
echo 'hello';

#[AttributeA]
/* testClassNoModifiersWithAttribute */
class ClassWithAttributeA {
    function noModifiers() {}
}

#[WithoutArgument]#[SingleArgument(0)]#[FewArguments('Hello', 'World')]
/* testClassTwoModifiersWithAttribute */
abstract readonly class ClassWithAttributeB {}

/* testClassNoAttribute */
readonly final class ClassNoAttribute {}

#[WithAttribute(foo: 'bar'), MyAttribute] readonly /* testClassOneModifierWithAttributeSameLine */ class ClassWithAttributeC {}

#[AttributeA]
/* testEnumAttribute */
enum EnumAttribute: int {}

#[AttributeA]

/** Short docblock */

#[\AttributeB, Partial\AttributeC, AttributeD]

/**
 * Docblock
 */
#[AttributeE]


/* testTraitAttribute */
trait TraitAttribute {}

#[
    WithAttribute(/* comment */ 'baz')
]
/* testInterfaceAttribute */
interface InterfaceAttribute {
    public function inInterface();
}

$anonClass = new #[AttributeA] /* testAnonClassAttributeSameLine */ class {
    protected function inAnonClass() {}
};

$anonClass = new /* testAnonClassNoAttribute */ class {
    protected function inAnonClass() {}
};

$anonClass = new #[AttributeA] readonly /* testReadonlyAnonClassAttributeSameLine */ class {
    protected function inAnonClass() {}
};

$anonClass = new readonly /* testReadonlyAnonClassNoAttributes */ class {
    protected function inAnonClass() {}
};

$anonClass = new
    #[AttributeA]
    readonly /* testReadonlyAnonClassAttribute */ class {
        protected function inAnonClass() {}
    };
