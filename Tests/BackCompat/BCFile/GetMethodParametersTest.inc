<?php

/* testImportUse */
use Vendor\Package\Sub as Alias;

/* testImportGroupUse */
use Vendor\Package\Sub\{
    ClassA,
    ClassB as BAlias,
};

if ($foo) {}

/* testTraitUse */
class TraitUse {
    use ImportedTrait;

    function methodName() {}
}

/* testNotAFunction */
interface NotAFunction {};

/* testFunctionNoParams */
function noParams() {}

/* testPassByReference */
function passByReference(&$var) {}

/* testArrayHint */
function arrayHint(array $var) {}

/* testTypeHint */
function typeHint(foo $var1, bar $var2) {}

class MyClass {
    /* testSelfTypeHint */
    function typeSelfHint(self $var) {}
}

/* testNullableTypeHint */
function nullableTypeHint(?int $var1, ?\bar $var2) {}

/* testVariable */
function variable($var) {}

/* testSingleDefaultValue */
function defaultValue($var1=self::CONSTANT) {}

/* testDefaultValues */
function defaultValues($var1=1, $var2='value') {}

/* testBitwiseAndConstantExpressionDefaultValue */
function myFunction($a = 10 & 20) {}

/* testArrayDefaultValues */
function arrayDefaultValues($var1 = [], $var2 = array(1, 2, 3) ) {}

/* testConstantDefaultValueSecondParam */
function constantDefaultValueSecondParam($var1, $var2 = M_PI) {}

/* testScalarTernaryExpressionInDefault */
function ternayInDefault( $a = FOO ? 'bar' : 10, ? bool $b ) {}

/* testVariadicFunction */
function variadicFunction( int ... $a ) {}

/* testVariadicByRefFunction */
function variadicByRefFunction( &...$a ) {}

/* testVariadicFunctionClassType */
function variableLengthArgument($unit, DateInterval ...$intervals) {}

/* testNameSpacedTypeDeclaration */
function namespacedClassType( \Package\Sub\ClassName $a, ?Sub\AnotherClass $b ) {}

/* testWithAllTypes */
class testAllTypes {
    function allTypes(
        ?ClassName $a,
        self $b,
        parent $c,
        object $d,
        ?int $e,
        string &$f,
        iterable $g,
        bool $h = true,
        callable $i = 'is_null',
        float $j = 1.1,
        array ...$k
    ) {}
}

/* testMessyDeclaration */
function messyDeclaration(
    // comment
    ?\MyNS /* comment */
        \ SubCat // phpcs:ignore Standard.Cat.Sniff -- for reasons.
            \  MyClass $a,
    $b /* test */ = /* test */ 'default' /* test*/,
    // phpcs:ignore Stnd.Cat.Sniff -- For reasons.
    ? /*comment*/
        bool // phpcs:disable Stnd.Cat.Sniff -- For reasons.
        & /*test*/ ... /* phpcs:ignore */ $c
) {}

/* testClosureNoParams */
function() {}

/* testClosure */
function( $a = 'test' ) {}

/* testClosureUseNoParams */
function() use() {}

/* testClosureUse */
function() use( $foo, $bar ) {}

/* testInvalidUse */
function() use {} // Intentional parse error.
