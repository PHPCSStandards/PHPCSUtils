<?php

/* testPHP8UnionTypesSimple */
function unionTypeSimple(int|float $number, self|parent &...$obj) {}

/* testPHP8UnionTypesSimpleWithBitwiseOrInDefault */
$fn = fn(int|float $var = CONSTANT_A | CONSTANT_B) => $var;

/* testPHP8UnionTypesTwoClasses */
function unionTypesTwoClasses(MyClassA|\Package\MyClassB $var) {}

/* testPHP8UnionTypesAllBaseTypes */
function unionTypesAllBaseTypes(array|bool|callable|int|float|null|object|string $var) {}

/* testPHP8UnionTypesAllPseudoTypes */
// Intentional fatal error - mixing types which cannot be combined, but that's not the concern of the method.
function unionTypesAllPseudoTypes(false|mixed|self|parent|iterable|Resource $var) {}

/* testPHP8UnionTypesNullable */
// Intentional fatal error - nullability is not allowed with union types, but that's not the concern of the method.
$closure = function (?int|float $number) {};

/* testPHP8PseudoTypeNull */
// Intentional fatal error - null pseudotype is only allowed in union types, but that's not the concern of the method.
function pseudoTypeNull(null $var = null) {}

/* testPHP8PseudoTypeFalse */
// Intentional fatal error - false pseudotype is only allowed in union types, but that's not the concern of the method.
function pseudoTypeFalse(false $var = false) {}

/* testPHP8PseudoTypeFalseAndBool */
// Intentional fatal error - false pseudotype is not allowed in combination with bool, but that's not the concern of the method.
function pseudoTypeFalseAndBool(bool|false $var = false) {}

/* testPHP8ObjectAndClass */
// Intentional fatal error - object is not allowed in combination with class name, but that's not the concern of the method.
function objectAndClass(object|ClassName $var) {}

/* testPHP8PseudoTypeIterableAndArray */
// Intentional fatal error - iterable pseudotype is not allowed in combination with array or Traversable, but that's not the concern of the method.
function pseudoTypeIterableAndArray(iterable|array|Traversable $var) {}

/* testPHP8DuplicateTypeInUnionWhitespaceAndComment */
// Intentional fatal error - duplicate types are not allowed in union types, but that's not the concern of the method.
function duplicateTypeInUnion( int | string /*comment*/ | INT $var) {}
