<?php

/* testNotNew */
echo 'abc';

/* testAnonClassWithoutParentheses */
$anon = new class {};

/* testAnonClassWithParentheses */
$anon = new class() {};

/* testFQNWithoutParentheses */
$a = $true ? new \Fully\Qualified\Name : $somethingElse;

/* testFQNWithParentheses */
$a = new \Fully\Qualified\Name () ;

/* testPartiallyQualifiedWithoutParentheses */
$a = new Partially\Qualified\Name ?? $somethingElse;

/* testPartiallyQualifiedWithParenthesesAndCommentsWhitespace */
$a = new Partially\ /* comment */ Qualified \ Name();

/* testUnqualifiedWithoutParentheses */
$a = functionCall(new Name);

/* testQualifiedWithParentheses */
$a = new \Name();

/* testNamespaceOperatorWithoutParentheses */
$a = new namespace\Sub\Name ?>

<?php
/* testNamespaceOperatorWithParentheses */
$a = new namespace\Name();

class Foo extends Bar {
    public function baz() {
        /* testSelfWithParentheses */
        $a = new SELF();

        /* testSelfReturnPHPCS1245 */
        return [new self];

        /* testStaticWithoutParentheses */
        $a = functionCall(new static, $somethingElse);

        /* testParentWithParentheses */
        $a = new parent() instanceof SomeClass;

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
