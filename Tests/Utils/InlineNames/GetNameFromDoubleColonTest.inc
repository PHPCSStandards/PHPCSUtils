<?php

/* testNotDoubleColon */
echo $variable;

/* testFQName */
$a = \Fully\Qualified\Name::CONSTANT;

/* testPartiallyQualified */
$a = Partially\Qualified\Name::static_function();

/* testPartiallyQualifiedAndCommentsWhitespace */
$a = Partially\ /* comment */ Qualified \ Name ::$static_property;

/* testUnqualified */
$a =Name::class;

/* testQualified */
$a = \Name ::static_function();

/* testNamespaceOperatorWithSub */
$a = namespace\Sub\Name::$static_property;

/* testNamespaceOperator */
$a = namespace\Name::$static_property;

class Foo extends Bar {
    public function baz() {
        /* testSelf */
        $a = self::$static_property;

        /* testStatic */
        $a = static::CONSTANT;

        /* testParent */
        $a = PARENT::static_function();

        /* testVariableName */
        $a = $name::static_function();

        /* testVariableVariableName */
        $a = ${$name}::$static_property;
    }
}
