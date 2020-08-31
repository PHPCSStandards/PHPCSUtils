<?php

/* testMessyPhpcsAnnotationsMethod */
trait FooTrait {
    /**
     * Method doc.
     */
    public // phpcs:disable Stnd.Cat.Sniff
        abstract // phpcs:ignore Stnd.Cat.Sniff
/*comment*/ function foo() {

        /* testMessyPhpcsAnnotationsStaticClosure */
        $closure = static // phpcs:ignore Stnd.Cat.Sniff
            function ( $foo ) {
                var_dump($foo);
            };
            $func();
    }
}

/* testReturnTypeEndTokenIndex */
function myFunction(): ?\MyNamespace /* comment */
                        \MyClass // comment
                        \Foo {}

/* testPHP8UnionTypesSimple */
function unionTypeSimple($number) : int|float {}

/* testPHP8UnionTypesTwoClasses */
$fn = fn($var): MyClassA|\Package\MyClassB => $var;

/* testPHP8UnionTypesAllBaseTypes */
function unionTypesAllBaseTypes() : array|bool|callable|int|float|null|Object|string {}

/* testPHP8UnionTypesAllPseudoTypes */
// Intentional fatal error - mixing types which cannot be combined, but that's not the concern of the method.
function unionTypesAllPseudoTypes($var) : false|MIXED|self|parent|static|iterable|Resource|void {}

/* testPHP8UnionTypesNullable */
// Intentional fatal error - nullability is not allowed with union types, but that's not the concern of the method.
$closure = function () use($a) :?int|float {};

/* testPHP8PseudoTypeNull */
// Intentional fatal error - null pseudotype is only allowed in union types, but that's not the concern of the method.
function pseudoTypeNull(): null {}

/* testPHP8PseudoTypeFalse */
// Intentional fatal error - false pseudotype is only allowed in union types, but that's not the concern of the method.
function pseudoTypeFalse(): false {}

/* testPHP8PseudoTypeFalseAndBool */
// Intentional fatal error - false pseudotype is not allowed in combination with bool, but that's not the concern of the method.
function pseudoTypeFalseAndBool(): bool|false {}

/* testPHP8ObjectAndClass */
// Intentional fatal error - object is not allowed in combination with class name, but that's not the concern of the method.
function objectAndClass(): object|ClassName {}

/* testPHP8PseudoTypeIterableAndArray */
// Intentional fatal error - iterable pseudotype is not allowed in combination with array or Traversable, but that's not the concern of the method.
interface FooBar {
    public function pseudoTypeIterableAndArray(): iterable|array|Traversable;
}

/* testPHP8DuplicateTypeInUnionWhitespaceAndComment */
// Intentional fatal error - duplicate types are not allowed in union types, but that's not the concern of the method.
function duplicateTypeInUnion(): int | /*comment*/ string | INT {}

/* testNamespaceOperatorTypeHint */
function namespaceOperatorTypeHint() : ?namespace\Name {}
