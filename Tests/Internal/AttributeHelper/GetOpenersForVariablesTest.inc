<?php

/* testNotAVariableToken */
echo 'hello';

/* testVariableNotPropertyNorParam1 */
$abc = $def;

class notProperty {
    function multiStatic() {
        static $abc, $def = 10 + 10, /* testVariableNotPropertyNorParam2 */ $ghi;
    }
}

function globalFunctionA(
    #[AttributeA]
    /* testFunctionParamWithAttribute */ $paramA,
    /* testFunctionParamNoAttribute */ $paramB,
    #[AttributeA, AttributeB(10)]
    & /* testFunctionParamRefWithAttribute */ $paramC,
    & /* testFunctionParamRefNoAttribute */ $paramD,
    #[AttributeB]
    #[AttributeC]
    ... /* testFunctionParamSpreadWithAttribute */ $paramE,
) {}

function globalFunctionB(
    ... /* testFunctionParamSpreadNoAttribute */ $paramF,
) {}

function globalFunctionC(
    #[
        WithAttribute(/* comment */ 'baz')
    ]
    &... /* testFunctionParamRefAndSpreadWithAttribute */ $paramG,
) {}

$cl = function (
    #[AttributeA] /* testClosureParamWithAttributeSameLine */ $paramA,
    /* testClosureParamNoAttribute */ $paramB,
) {};

$arrow = fn (#[AttributeA] /* testArrowParamWithAttributeSameLine */ $paramA, /* testArrowParamNoAttribute */ $paramB) => $paramA * 10;

abstract class PropertiesAndParamsWithAttributes {

    #[AttributeA]
    var /* testOOPropertyVarNoTypeWithAttribute */ $untyped;

    #[AttributeA]

    /** Short docblock */

    #[AttributeB, \AttributeC, namespace\AttributeD]

    /**
     * Docblock
     */
    #[AttributeE]


    public abstract int /* testOOPropertyPublicAbstractPlainTypeWithMultipleAttributes */ $publicAbstract; // Note: this test should have { get;}, but that will be broken until support for property hooks is added to PHPCS.

    #[AttributeA]
    final protected readonly string /* testOOPropertyFinalProtectedReadonlyPlainTypeWithAttribute */ $finalProtectedReadonly;

    #[AttributeA]
    private static /* testOOPropertyPrivateStaticNoTypeWithAttribute */ $privateStatic;

    #[WithAttribute(foo: 'bar'), MyAttribute]
    public(set) int /* testOOPropertyAsymPublicPlainTypeWithAttribute */ $asymPublicPlainType;

    #[WithoutArgument]#[SingleArgument(0)]#[FewArguments('Hello', 'World')]
    final protected(set) ?string /* testOOPropertyFinalAsymProtectedNullableTypeWithAttribute */ $asymProtectedNullableType;

    public(set) ?bool /* testOOPropertyAsymPublicNullableTypeNoAttributes */ $asymPublicNullableType;

    #[AttributeA] private(set) public MyType /* testOOPropertyAsymPrivatePublicOOTypeWithAttributeSameLine */ $asymPrivateOOType;

    #[
        WithAttribute(/* comment */ 'baz')
    ]
    public final namespace\MyType /* testOOPropertyPublicFinalNamespaceRelativeTypeWithAttribute */ $publicFinalNamespaceRelativeType;

    #[AttributeA]
    protected private(set) final Partially\Qualified\MyType
        /* testOOPropertyProtectedAsymPrivateFinalPartiallyQualifiedTypeWithAttribute */ $asymPartiallyQualifiedType;

    #[AttributeA]
    #[AttributeB, AttributeC, namespace\AttributeD]
    #[AttributeE]
    readonly \Fully\Qualified\MyType /* testOOPropertyReadonlyFullyQualifiedTypeWithAttribute */ $readonlyFullyQualifiedType;

    #[AttributeA]
    public int|string|bool|float|array|object|iterable|null|true|false|self|parent|Type
        /* testOOPropertyPublicAllValidTypesWithAttribute */ $publicUnionTypeAllTypes;

    #[WithAttribute(foo: 'bar'), MyAttribute]
    final \FQN|namespace\Relative /* testOOPropertyFinalUnionOOTypesWithAttribute */ $finalUnionType;

    #[AttributeA]
    static Partially\Qualified&OtherType /* testOOPropertyStaticIntersectionOOTypesWithAttribute */ $staticIntersectionType;

    #[
        WithAttribute(/* comment */ 'baz')
    ]
    protected(set) static final false|(NameA&\NameB)|null
        /* testOOPropertyAsymProtectedStaticFinalDNFTypeWithAttribute */ $asymProtectedStaticFinalDNFType;

    #[AttributeA]
    public int $propA = 10,
        $propB = 20,
        /* testOOMultiPropertyLastWithAttribute */
        $propC = 'target';

    public function __construct(
        #[AttributeA]
        public(set) final MyType|false /* testCPPPropAsymPublicStaticUnionTypeWithAttribute */ $propA,
        #[AttributeA] final protected ?iterable /* testCPPPropFinalProtectedNullableTypeWithAttribute */ $propB { get; set; },
        private(set) \TypeA&TypeB /* testCPPPropAsymPrivateIntersectionTypeNoAttributes */ $propC,
        #[WithoutArgument]#[SingleArgument(0)]


        #[FewArguments('Hello', 'World')]
        readonly string /* testCPPPropReadonlyPlainTypeWithAttribute */ $propD,
        #[AttributeA] ?object &... /* testConstructorMethodParamNullableTypeRefAndSpreadWithAttributeSameLine */ $param,
    ) {}

    protected function notConstructor(
        #[AttributeA] int /* testMethodParamTypedWithAttributeSameLine */ $paramA,
        ?float /* testMethodParamNullableTypeNoAttribute */ $paramB,
        #[AttributeA, AttributeB(10)]
        TypeA|\TypeB & /* testMethodParamUnionTypeRefWithAttribute */ $paramC,
        #[
            WithAttribute(/* comment */ 'baz')
        ]
        (DNF&Type)|false &... /* testMethodParamDNFTypeRefAndSpreadWithAttribute */ $paramD,
    ) {}

    #[ExampleAttribute]
    public function alsoNotConstructor(#[ExampleAttribute] /* testMethodParamNoTypeSameLine */ $bar) {}
}
