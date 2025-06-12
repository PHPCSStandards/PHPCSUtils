<?php

/* testNotAConstToken */
define('FOO', true);

/* testGlobalConstantCannotHaveModifiersOrType */
const GLOBAL_UNTYPED = true;

class ClassWithTypedConstants {

    public function foo() {
        /* testConstInMethodIsNotOO */
        const NOT_OO_CONST = true;
    }

    /* testNoModifiersNoTypesWithDocblock */
    /**
     * Comment here.
     *
     * @phpcs:ignore Standard.Category.Sniff -- because
     * @var boolean
     */
    const NO_MODIFIERS_UNTYPED = true;

    /* testFinalNoTypesConstAsName */
    final const CONST = true;

    /* testPublicNoTypes */
    public const PUBLIC_UNTYPED = true;

    /* testProtectedNoTypesWithComment */
    protected /*comment*/ const PROTECTED_UNTYPED = true;

    /* testPrivateNoTypesWithCommentAndWhitespace */
    private
        /*comment*/
        const
        // phpcs:ignore Stnd.Cat.Sniff -- for reasons.
        PRIVATE_UNTYPED = true;

    /* testFinalPublicNoTypes */
    final public const FINAL_PUBLIC_UNTYPED = true;

    /* testProtectedFinalNoTypes */
    protected final const PROTECTED_FINAL_UNTYPED = true;

    /* testTypedTrue */
    const true TYPED_TRUE = true;

    /* testTypedFalse */
    final const false TYPED_FALSE = false;

    /* testTypedNull */
    public const null TYPED_NULL = null;

    /* testTypedBoolWihComment */
    final protected const/*comment*/bool TYPED_BOOL = false;

    /* testTypedNullableIntWithDocblock */
    /**
     * Comment here.
     *
     * @phpcs:ignore Standard.Category.Sniff -- because
     * @var int|null
     */
    private const ?int TYPED_INT = 0;

    /* testTypedFloatWithAttribute */
    #[ConstantWithAttribute]
    const float TYPED_FLOAT = 0.5;

    /* testTypedNullableStringWithComment */
    public /*comment*/ final const ?string/*comment*/TYPED_STRING = 'string';

    /* testTypedArray */
    private final const array TYPED_ARRAY = [];

    /* testTypedObjectWithExtraWhitespace */
    const
        object
        TYPED_OBJECT
        // comment.
        =
        MyClass::getInstance();

    /* testTypedNullableIterableLowercaseName */
    const ?iterable typed_iterable = [];

    /* testTypedMixed */
    const mixed TYPED_MIXED = 'string';

    /* testTypedClassUnqualifiedWithComment */
    const ? /*comment*/ MyClass TYPED_UNQUALIFIED_CLASSNAME = MyClass::getInstance();

    /* testTypedClassFullyQualifiedWithDocblock */
    /**
     * Comment here.
     *
     * @phpcs:ignore Standard.Category.Sniff -- because
     * @var MyClass
     */
    public const \MyClass TYPED_FULLY_QUALIFIED_CLASSNAME = MyClass::getInstance();

    /* testTypedClassNamespaceRelative */
    protected const namespace\MyClass TYPED_NAMESPACE_RELATIVE_CLASSNAME = MyClass::getInstance();

    /* testTypedClassPartiallyQualifiedWithMultipleAttributes */
    #[PropertyWithAttribute(foo: 'bar'), MyAttribute]
    private const Partial\MyClass TYPED_PARTIALLY_QUALIFIED_CLASSNAME = MyClass::getInstance();

    /* testTypedNullableParentMessy */
    const /* comment
         */ ? //comment
        parent TYPED_PARENT = parent::getInstance();

    /* testMultiConstSingleLine */
    public const bool NAME_1 = true, NAME_2 = false;

    /* testMultiConstMultiLine */
    public const ?array
        // First constant.
        NAME_1 = [],
        // Second constant.
        NAME_2 = [];
}

enum EnumWithOOHierarchyTypedConstants {
    // Illegal types in a class, but legal in an enum.
    /* testEnumConstTypedSelf */
    final const self TYPED_SELF = self::getInstance();

    /* testEnumConstTypedNullableStatic */
    const ?static TYPED_STATIC = static::getInstance();
}

trait TraitWithNullableTypedConstants {
    // Illegal types - the fact that these are not allowed in PHP is not the concern of this method.
    /* testTypedNullableCallable */
    protected const ?callable TYPED_CALLABLE = 'function_name';

    /* testTypedVoid */
    final public const void TYPED_VOID = null;

    /* testTypedNever */
    private const never TYPED_NEVER = null;
}

interface InterfaceWithUnionTypedConstants {
    /* testTypedUnionTrueNull */
    const true|null /*comment*/ UNION_TRUE_NULL = true;

    /* testTypedUnionArrayObjectWithMultilineAttribute */
    #[
        PropertyWithAttribute(/* comment */ 'baz')
    ]
    final const array|object UNION_ARRAY_OBJECT = [];

    /* testTypedUnionStringArrayInt */
    const string | array | int UNION_STRING_ARRAY_INT = 'array middle';

    /* testTypedUnionFloatBoolArrayIllegalNullable */
    public final const ?float /*comment*/| bool|array UNION_FLOAT_BOOL_ARRAY = false;

    /* testTypedUnionIterableFalse */
    const iterable|false UNION_ITERABLE_FALSE = false;

    /* testTypedUnionUnqualifiedNamespaceRelativeWithWhiteSpaceAndComments */
    final protected const Unqualified /*comment*/ | namespace\Relative UNION_UNQUALIFIED_NSRELATIVE = new Unqualified();

    /* testTypedUnionFullyQualifiedPartiallyQualified */
    private /*comment*/ const \Fully\Qualified|Partially\Qualified UNION_FQN_PARTIAL = new Partial\Qualified;
}

$a = ( $foo == $bar ? new stdClass() :
    new class {
        /* testTypedIntersectUnqualifiedNamespaceRelative */
        final const Unqualified&namespace\Relative UNION_UNQUALIFIED_NSRELATIVE = new Unqualified();

        /* testTypedIntersectFullyQualifiedPartiallyQualified */
        protected const \Fully\Qualified&Partially\Qualified UNION_FQN_PARTIAL = new Partial\Qualified;
    }
);

interface DNFTypes {
    /* testPHP82DNFType */
    protected const float|(Partially\Qualified&Traversable) DNF_PROTECTED = 1.5;

    /* testPHP82DNFTypeWithWhiteSpaceAndComment */
    public final const ( Unqualified & namespace\Relative ) /*comment*/ | bool DNF_PUBLIC_FINAL = true;

    /* testPHP82DNFTypeIllegalNullable */
    const ?(A&\Pck\B)|bool DNF_ILLEGAL_NULLABLE = null;
}
