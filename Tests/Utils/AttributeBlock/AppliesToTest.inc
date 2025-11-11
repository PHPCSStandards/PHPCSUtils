<?php

/* testNotAcceptedTypeExceptionOutsideAttribute1 */
/**
 * Docblock
 */
#[MyAttribute]
function notAcceptedTokenOutsideAttributeTest() {}

/* testNotAcceptedTypeExceptionOutsideAttribute2 */
echo 'notAcceptedTokenOutsideAttributeTest';

/* testAcceptedTokens */
#[AttributeName(10), \AnotherAttribute, namespace\ThirdAttribute(self::CONSTANT, 'foo')]
function acceptedTokensAttributeTarget() {}

/* testFindUnscopedConst */
#[MyAttribute]
const UNSCOPED = true;

array_map(
    /* testFindUnscopedClosure */
    #[MyAttribute]
    function($a) {
        return $a++;
    },
    $array
);

/* testFindUnscopedClosureStatic */
$closure = #[MyAttribute] static function($a) {};

array_map(
    /* testFindUnscopedArrowFunction */
    #[MyAttribute]
    fn($a) => $a++,
    $array
);

/* testFindUnscopedArrowFunctionStatic */
$closure = #[MyAttribute] static fn ($a) => $a * 10;

function hasParamAttributes(
    /* testFindFunctionParameter */
    #[MyAttribute]
    #[AnotherAttribute]
    $paramA,

    /* testFindFunctionParameterTypedNullable */
    #[MyAttribute] ?int $paramB,

    /* testFindFunctionParameterTypedUnion */
    #[MyAttribute] int|false $paramC,

    /* testFindFunctionParameterWithRef */
    #[MyAttribute]
    // Unrelated comment
    &$paramD,

    /* testFindFunctionParameterWithSpread */
    #[MyAttribute]
    /** docblock */
    ...$paramE,
) {}

function hasParamAttributesToo(
    /* testFindFunctionParameterAllTogetherNow */
    #[MyAttribute] (\Foo&Partial\Bar&namespace\Other)|array &...$paramF,
) {}

/* testFindClass */
#[MyAttribute]
class classHasAttribute {}

/* testFindClassFinal */
#[MyAttribute]
/**
 * Docblock.
 */
#[AnotherAttribute]
final class finalClass {}

/* testFindClassReadonly */
#[MyAttribute]
readonly class readonlyClass {}

/* testFindClassAbstract */
#[MyAttribute]
abstract class abstractClass {}

/* testFindClassFinalReadonly */
#[MyAttribute]
// Unrelated comment.

// Another unrelated comment.
final readonly class finalReadonlyClass {}

/* testFindAnonClass */
$anon = new #[MyAttribute] #[SecondAttribute] class {};

/* testFindAnonClassReadonly */
$anon = new #[MyAttribute] readonly class {};

/* testFindTrait */
#[MyAttribute(10), \Another]
trait traitHasAttribute {}

/* testFindInterface */
#[MyAttribute]



#[AnotherAttributeWithBlankLinesAroundIt(
    name: VALUE,
    other_name: VALUE,
)]



interface interfaceHasAttribute {}

/* testFindEnum */
#[MyAttribute]
/**
 * Docblock.
 *
 * @since 1.2.0
 * @see Some\Ref
 */
enum enumHasAttribute {}

abstract class AttributesEverywhere {
    /* testFindConstFinal */
    #[MyAttribute]
    final const FINAL_CONST = false;

    /* testFindConstPublicFinal */
    #[MyAttribute]
    public final const PUBLIC_FINAL_CONST = true;

    /* testFindConstPrivateTyped */
    #[MyAttribute]
    private const TypeA|TypeB PRIVATE_CONST = new TypeA;

    /* testFindPropertyVar */
    #[MyAttribute]
    var $propertyA;

    /* testFindPropertyPublicStatic */
    #[MyAttribute]
    public static $propertyB;

    /* testFindPropertyFinalProtectedReadonlyNullableType */
    #[MyAttribute]
    final protected readonly ?\TypeA $propertyC;

    // Intentional parse error, abstract private, but not our concern.
    /* testFindPropertyPrivateAbstractDNFType */
    #[MyAttribute]
    private abstract array|(DN&\F)|false $propertyD { get; }

    /* testFindPropertyReadonlyAsymPrivateIntersectionType */
    #[MyAttribute]
    readonly private(set) TypeA&namespace\TypeB $propertyE;

    /* testFindMethodPublicStaticReturnByRef */
    #[MyAttribute]
    public static function &methodA() {}

    /* testFindMethodAbstractProtected */
    #[MyAttribute]
    abstract protected function methodB();

    // Intentional parse error, final private, but not our concern.
    /* testFindMethodPrivateFinal */
    #[MyAttribute]
    private final function methodC() {}

    public function __construct(
        /* testFindPromotedProperty */
        #[MyAttribute] #[AnotherAttribute]
        public private(set) final ?int $propA,
    ) {}
}
