<?php

/* testNotNew */
echo 'abc';

/* testAnonClassWithoutParentheses */
$anon = new class {};

/* testAnonClassWithParentheses */
$anon = new class() {};

/* testFQNWithoutParentheses */
$a = new \Fully\Qualified\Name;

/* testFQNWithParentheses */
$a = new \Fully\Qualified\Name () ;

/* testPartiallyQualifiedWithoutParentheses */
$a = new Partially\Qualified\Name;

/* testPartiallyQualifiedWithParenthesesAndCommentsWhitespace */
$a = new Partially\ /* comment */ Qualified \ Name();

/* testUnqualifiedWithoutParentheses */
$a = new Name ;

/* testQualifiedWithParentheses */
$a = new \Name();

/* testNamespaceOperatorWithoutParentheses */
$a = new namespace\Sub\Name;

/* testNamespaceOperatorWithParentheses */
$a = new namespace\Name();

class Foo extends Bar {
    public function baz() {
        /* testSelfWithParentheses */
        $a = new SELF();

        /* testStaticWithoutParentheses */
        $a = new static;

        /* testParentWithParentheses */
        $a = new parent();

        /* testVariableClassNameWithParentheses */
        $a = new $name();

        /* testVariableVariableClassNameWithParentheses */
        $a = new ${$name}();

        /* testStaticPropertyClassNameWithParentheses */
        $a = new static::$name();

        /* testSelfConstantClassNameWithParentheses */
        $a =new (self::CLASS_NAME)(); // TODO: find other example, this is a parse error.
    }
}

/* testLiveCoding */
// Intentional parse error.
$a = new
