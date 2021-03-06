<?php

/* testNotTheRightContent */
function_call();

/* testStandard */
$fn1 = fn($x) => $x + $y;

/* testMixedCase */
$fn1 = Fn($x) => $x + $y;

/* testWhitespace */
$fn1 = fn ($x) => $x + $y;

/* testComment */
$fn1 = fn /* comment here */ ($x) => $x + $y;

/* testFunctionName */
function &fn() {}

/* testNestedOuter */
$fn = fn($x) => /* testNestedInner */ fn($y) => $x * $y + $z;

/* testFunctionCall */
$extended = fn($c) => $callable($factory($c), $c);

/* testChainedFunctionCall */
$result = Collection::from([1, 2])
    ->map(fn($v) => $v * 2)
    ->reduce(/* testFunctionArgument */ fn($tmp, $v) => $tmp + $v, 0);

/* testClosure */
$extended = fn($c) => $callable(function() {
    for ($x = 1; $x < 10; $x++) {
        echo $x;
    }

    echo 'done';
}, $c);

$result = array_map(
    /* testReturnTypeNullableInt */
    static fn(int $number) : ?int => $number + 1,
    $numbers
);

/* testReference */
fn&($x) => $x;

/* testGrouped */
(fn($x) => $x) + $y;

/* testArrayValue */
$a = [
    'a' => fn() => return 1,
];

/* testYield */
$a = fn($x) => yield 'k' => $x;

/* testReturnTypeNamespacedClass */
$fn = fn($x) : ?\My\NS\ClassName => $x;

/* testReturnTypePartiallyQualifiedClass */
$fn = fn($x) : ?NS\ClassName => $x;

/* testReturnTypeNullableFQNClass */
$a = fn(?\DateTime $x) : ?\DateTime => $x;

/* testNamespaceOperatorInTypes */
$fn = fn(namespace\Foo $a) : ?namespace\Foo => $a;

/* testReturnTypeSelf */
$fn = fn(self $a) : ?self => $a;

/* testReturnTypeParent */
$fn = fn(parent $a) : parent => $a;

/* testReturnTypeCallable */
$fn = fn(callable $a) : callable => $a;

/* testReturnTypeArray */
$fn = fn(array $a) : array => $a;

/* testReturnTypeStatic */
$fn = fn(array $a) : static => $a;

/* testUnionParamType */
$arrowWithUnionParam = fn(int|float $param) : SomeClass => new SomeClass($param);

/* testUnionReturnType */
$arrowWithUnionReturn = fn($param) : int|float => $param | 10;

/* testReturnTypeArrayBug2773 */
$fn = fn(): array => [a($a, $b)];

array_map(
    /* testMoreArrayTypeDeclarations */
    static fn (array $value): array => array_filter($value),
    []
);

/* testTernary */
$fn = fn($a) => $a ? /* testTernaryThen */ fn() : string => 'a' : /* testTernaryElse */ fn() : string => 'b';

$foo = foo(
    /* testArrowFunctionAsArgument */
    fn() => bar()
);

$foo = foo(
    /* testArrowFunctionWithArrayAsArgument */
    fn() => [$row[0], $row[3]]
);

/* testConstantDeclaration */
const FN = 'a';

/* testConstantDeclarationLower */
const fn = 'a';

class Foo {
    /* testStaticMethodName */
    public static function fn($param) {
        /* testNestedInMethod */
        $fn = fn($c) => $callable($factory($c), $c);
    }

    public function foo() {
        /* testPropertyAssignment */
        $this->fn = 'a';
    }
}

$anon = new class() {
    /* testAnonClassMethodName */
    protected function fN($param) {
    }
}

/* testNonArrowStaticMethodCall */
$a = Foo::fn($param);

/* testNonArrowConstantAccess */
$a = MyClass::FN;

/* testNonArrowConstantAccessDeref */
$a = MyClass::Fn[$a];

/* testNonArrowObjectMethodCall */
$a = $obj->fn($param);

/* testNonArrowObjectMethodCallUpper */
$a = $obj->FN($param);

/* testNonArrowNamespacedFunctionCall */
$a = MyNS\Sub\Fn($param);

/* testNonArrowNamespaceOperatorFunctionCall */
$a = namespace\fn($param);

/* testNonArrowFunctionNameWithUnionTypes */
function &fn(int|float $param) : string|null {}

/* testLiveCoding */
// Intentional parse error. This has to be the last test in the file.
$fn = fn
