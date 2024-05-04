<?php

/* testClosureUse */
$closure = function() use($bar) {};

class Foo {
    /* testTraitUse */
    use MyNamespace\Bar;
}

/* testUsePlain */
use MyNamespace\MyClass;

/* testUsePlainAliased */
use MyNamespace \ YourClass as ClassAlias;

/* testUsePlainLeadingBackslash */
use \MyNamespace\TheirClass;

/* testUseMultipleWithComments */
use Vendor\Foo\ClassA as ClassABC,
   Vendor \ /*comment*/ Bar \ /*another comment */ InterfaceB,
   // phpcs:ignore Standard.Category.Sniff -- for reasons.
   Vendor\Baz\ClassC;

/* testUseFunctionPlainEndsOnCloseTag */
use function MyNamespace\myFunction ?>

<?php

/* testUseFunctionPlainAliased */
use function Vendor\YourNamespace\yourFunction as FunctionAlias;

/* testUseFunctionMultiple */
use /* comment */ function foo\math\sin,foo\math\cos as FooCos,foo\math\cosh;

/* testUseConstPlainUppercaseConstKeyword */
use CONST MyNamespace\MY_CONST;

/* testUseConstPlainAliased */
use const MyNamespace\YOUR_CONST as CONST_ALIAS;

/* testUseConstMultiple */
use const foo\math\PI, \foo\math\GOLDEN_RATIO as MATH_GOLDEN;

/* testGroupUse */
use some\namespacing\{
    SomeClassA,
    deeper\level\SomeClassB,
    another\level\SomeClassC as C
};

/* testGroupUseLeadingBackslash */
use \world\namespacing\{
    SomeClassA,
    deeper\level\SomeClassB
};

/* testGroupUseFunctionTrailingComma */
use function bar\math\{
    Msin,
    level\Mcos as BarCos,
    Mcosh,
};

/* testGroupUseConst */
use // phpcs:ignore Standard.Category
    const
        bar\math\{ BGAMMA as BAR_GAMMA, BGOLDEN_RATIO };

// Mixed group use statement. Yes, this is allowed.
/* testGroupUseMixed */
use Some\NS\ {
   ClassName,
   function SubLevel\functionName,
   const Constants\CONSTANT_NAME as SOME_CONSTANT,
   function SubLevel\AnotherName,
   AnotherLevel,
};

/* testUsePlainReservedKeyword */
// Intentional parse error for PHP < 8.0 - use of reserved keyword in namespace.
use Vendor\break\ClassName;

/* testUseFunctionPlainReservedKeyword */
// Intentional parse error for PHP < 8.0  - use of reserved keyword in namespace.
use function Vendor\YourNamespace\switch\yourFunction;

/* testUseConstPlainReservedKeyword */
// Intentional parse error for PHP < 8.0  - use of reserved keyword in namespace.
use const Vendor\YourNamespace\function\yourConst;

/* testUsePlainAliasReservedKeyword */
// Intentional parse error - use of reserved keyword as alias.
use Vendor\YourNamespace\ClassName as class;

/* testUsePlainAliasReservedKeywordFunction */
// Intentional parse error - use of reserved keyword as alias.
use Vendor\{
    YourNamespace\ClassName as function
};

/* testUsePlainAliasReservedKeywordConst */
// Intentional parse error - use of reserved keyword as alias.
use Vendor\YourNamespace\ClassName as const;

// Intentional parse error. This has to be the last test in the file.
/* testParseError */
use MyNS\Level\{
    Something,
