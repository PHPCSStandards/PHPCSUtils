<?php

/* testNotInstanceOf */
echo 'abc';

/* testUnqualifiedName */
$name = $var instanceof Name ? $var : $var->doSomething();

/* testQualifiedNameAndComments */
if (self::$staticProp instanceof /*comment*/ \Name /* comment */ === true && $b = true) {}

/* testPartiallyQualifiedName */
if ($obj->prop instanceof Partially\Qualified\Name) {}

/* testPartiallyQualifiedWithParenthesesAndCommentsWhitespace */
$a = $obj->prop instanceof Partially\ /* comment */ Qualified \ Name || true;

/* testFullyQualifiedName */
if (self::$staticProp instanceof \Fully\Qualified\Name) {}

/* testNamespaceOperator */
$a = $b instanceof namespace\Name;

/* testNamespaceOperatorWithSubLevel */
switch (true) {
    case $b instanceof namespace\Sub\Name:
        break;
}

/* testUnqualifiedNameWithClassModifier */
$a = $b instanceof Name::class ?>

<?php
class Foo extends Bar {
    public function baz() {
        /* testSelf */
        if ($me instanceof self
            /* testParent */
            && $me instanceof  /*comment*/ parent
        ) {}

        /* testStatic */
        return $value ? $value instanceof static : false;
    }
}

/* testStringName */
if ($a instanceof 'Name' || $somethingElse) {}

/* testMagicConstantWithStringName */
$array = [
    $b instanceof __NAMESPACE__ . '\Name',
];

/* testMagicConstantWithVariableName */
$c = $b instanceof __NAMESPACE__ . '\\' . $className;

/* testMagicConstantWithDoubleQuotedStringVariableName */
$c = $b instanceof __NAMESPACE__ . "\\{$className}";


/* testAnonClass */
// Nonsense code, but testing something anyhow.
$name = $var instanceof new class() {};

/* testVariableName */
return $a instanceof $b;

/* testVariableVariableName */
return $a instanceof ${$b};

/* testVariablePropertyName */
assert ($a instanceof $obj->expected);

/* testStaticPropertyName */
$a = $b instanceof ClassName::$property;

/* testClassConstantName */
$a = $b instanceof ClassName::CONST_NAME;

class FooFoo extends Bar {
    public function baz() {
        /* testSelfPropertyClassName */
        $a = $b instanceof self::$name;

        /* testParentConstantClassName */
        $a = $b instanceof parent::CONSTANT_NAME;

        /* testStaticPropertyClassName */
        $a = $b instanceof static::$name;

        /* testSelfInArrayKey */
        $a = $b instanceof CONSTANT_NAME[self::$name];
    }
}


/* testLiveCoding */
// Intentional parse error.
$a = $b instanceof Name
