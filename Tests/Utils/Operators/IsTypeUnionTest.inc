<?php

/*
 * Type union or bitwise or.
 */

/* testNotBitwiseOrToken */
echo 'foo';

/* testBitwiseOr1 */
$result = $value | $test /* testBitwiseOr2 */ | $another;

class TypeUnion
{
    /* testTypeUnionPropertySimple */
    public static Foo|Bar $obj;

    /* testTypeUnionPropertyReverseModifierOrder */
    static protected int|float $number /* testBitwiseOrPropertyDefaultValue */ = E_WARNING | E_NOTICE;

    private
        /* testTypeUnionPropertyMulti1 */
        array |
        /* testTypeUnionPropertyMulti2 */
        Traversable | // phpcs:ignore Stnd.Cat.Sniff
        false
        /* testTypeUnionPropertyMulti3 */
        | null $arrayOrFalse;

    /* testTypeUnionPropertyNamespaceRelative */
    public namespace\Sub\NameA|namespace\Sub\NameB $namespaceRelative;

    /* testTypeUnionPropertyPartiallyQualified */
    public Partially\Qualified\NameA|Partially\Qualified\NameB $partiallyQual;

    /* testTypeUnionPropertyFullyQualified */
    public \Fully\Qualified\NameA|\Fully\Qualified\NameB $fullyQual;

    public function paramTypes(
        /* testTypeUnionParam1 */
        int|float $paramA /* testBitwiseOrParamDefaultValue */ = CONSTANT_A | CONSTANT_B,

        /* testTypeUnionParam2 */
        Foo|\Bar /* testTypeUnionParam3 */ |Baz &...$paramB = null,
    ) {
        /* testBitwiseOr3 */
        return (($a1 ^ $b1) |($a2 ^ $b2)) + $c;
    }

    public function identifierNames(
        /* testTypeUnionParamNamespaceRelative */
        namespace\Sub\NameA|namespace\Sub\NameB $paramA,
        /* testTypeUnionParamPartiallyQualified */
        Partially\Qualified\NameA|Partially\Qualified\NameB $paramB,
        /* testTypeUnionParamFullyQualified */
        \Fully\Qualified\NameA|\Fully\Qualified\NameB $paramC,
    ) {}

    /* testTypeUnionReturnType */
    public function returnType() : int|false {}

    /* testTypeUnionConstructorPropertyPromotion */
    public function __construct( public bool|null $property) {}

    /* testTypeUnionAbstractMethodReturnType1 */
    abstract public function abstractMethod(): object|array /* testTypeUnionAbstractMethodReturnType2 */ |false;

    /* testTypeUnionReturnTypeNamespaceRelative */
    public function identifierNamesReturnRelative() : namespace\Sub\NameA|namespace\Sub\NameB {}

    /* testTypeUnionReturnPartiallyQualified */
    public function identifierNamesReturnPQ() : Partially\Qualified\NameA|Partially\Qualified\NameB {}

    /* testTypeUnionReturnFullyQualified */
    public function identifierNamesReturnFQ() : \Fully\Qualified\NameA|\Fully\Qualified\NameB {}
}

/* testTypeUnionClosureParamIllegalNullable */
$closureWithParamType = function (?string|null $string) {};

/* testBitwiseOrClosureParamDefault */
$closureWithReturnType = function ($string = NONSENSE | FAKE)/* testTypeUnionClosureReturn */ : \Package\MyA|PackageB {};

/* testTypeUnionArrowParam */
$arrowWithParamType = fn (object|array $param, /* testBitwiseOrArrowParamDefault */ ?int $int = CONSTA | CONSTB )
    /* testBitwiseOrArrowExpression */
    => $param | $int;

/* testTypeUnionArrowReturnType */
$arrowWithReturnType = fn ($param) : int|null => $param * 10;

/* testBitwiseOrInArrayKey */
$array = array(
    A | B => /* testBitwiseOrInArrayValue */ B | C
);

/* testBitwiseOrInShortArrayKey */
$array = [
    A | B => /* testBitwiseOrInShortArrayValue */ B | C
];

/* testBitwiseOrTryCatch */
try {
} catch ( ExceptionA | ExceptionB $e ) {
}

/* testBitwiseOrNonArrowFnFunctionCall */
$obj->fn($something | $else);

/* testTypeUnionNonArrowFunctionDeclaration */
function &fn(int|false $something) {}

/* testBitwiseOrInNestedTernaryPhpcsLt291 */
echo $a ? $b : CONST_A ? CONST_A | CONST_B : CONST_C;

/* testTypeUnionInTernaryNestedArrowFunction1 */
$fn = fn($a): int|float => $a ? /* testTypeUnionInTernaryNestedArrowFunction2 */ fn() : string|false => 'a' : /* testTypeUnionInTernaryNestedArrowFunction3 */ fn() : ?bool|null => 'b';

/* testBitwiseOrInTernaryNestedArrowFunction */
$fn = fn($a) => $a ? fn( $var = CONST_A | CONST_B ) : ?string => 'a' : fn() : string => 'b';

/* testLiveCoding */
// Intentional parse error. This has to be the last test in the file.
return function( type|
