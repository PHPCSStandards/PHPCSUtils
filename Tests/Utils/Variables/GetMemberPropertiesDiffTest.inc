<?php

interface Base
{
    /* testInterfaceProperty */
    protected $anonymous;
}

$anon = class() {
    /* testPHP8UnionTypesSimple */
    public int|float $unionTypeSimple;

    /* testPHP8UnionTypesTwoClasses */
    private MyClassA|\Package\MyClassB $unionTypesTwoClasses;

    /* testPHP8UnionTypesAllBaseTypes */
    protected array|bool|int|float|NULL|object|string $unionTypesAllBaseTypes;

    /* testPHP8UnionTypesAllPseudoTypes */
    // Intentional fatal error - mixing types which cannot be combined, but that's not the concern of the method.
    var false|mixed|self|parent|iterable|Resource $unionTypesAllPseudoTypes;

    /* testPHP8UnionTypesIllegalTypes */
    // Intentional fatal error - types which are not allowed for properties, but that's not the concern of the method.
    public callable|static|void $unionTypesIllegalTypes;

    /* testPHP8UnionTypesNullable */
    // Intentional fatal error - nullability is not allowed with union types, but that's not the concern of the method.
    public ?int|float $unionTypesNullable;

    /* testPHP8PseudoTypeNull */
    // Intentional fatal error - null pseudotype is only allowed in union types, but that's not the concern of the method.
    public null $pseudoTypeNull;

    /* testPHP8PseudoTypeFalse */
    // Intentional fatal error - false pseudotype is only allowed in union types, but that's not the concern of the method.
    public false $pseudoTypeFalse;

    /* testPHP8PseudoTypeFalseAndBool */
    // Intentional fatal error - false pseudotype is not allowed in combination with bool, but that's not the concern of the method.
    public bool|FALSE $pseudoTypeFalseAndBool;

    /* testPHP8ObjectAndClass */
    // Intentional fatal error - object is not allowed in combination with class name, but that's not the concern of the method.
    public object|ClassName $objectAndClass;

    /* testPHP8PseudoTypeIterableAndArray */
    // Intentional fatal error - iterable pseudotype is not allowed in combination with array or Traversable, but that's not the concern of the method.
    public iterable|array|Traversable $pseudoTypeIterableAndArray;

    /* testPHP8DuplicateTypeInUnionWhitespaceAndComment */
    // Intentional fatal error - duplicate types are not allowed in union types, but that's not the concern of the method.
    public int |string| /*comment*/ INT $duplicateTypeInUnion;
};
