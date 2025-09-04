<?php

/* testNotAnAcceptedToken */
interface NotAFunctionCallOrArray {}

class Foo {
    public function Bar() {
        /* testNotACallToConstructor1 */
        $a = parent::SOME_CONSTANT;

        /* testNotACallToConstructor2 */
        $a = static::some_method();

        /* testNotACallToConstructor3 */
        $a = $obj instanceof self;
    }
}

/* testShortListNotShortArray */
[ $a, $b ] = $array;

/* testExitAsConstant */
exit;

/* testDieAsConstant */
die;


// Function calls: no parameters.

/* testNoParamsFunctionCall1 */
some_function();

/* testNoParamsFunctionCall2 */
some_function(     );

/* testNoParamsFunctionCall3 */
some_function( /*nothing here*/ );

/* testNoParamsFunctionCall4 */
$closure(/*nothing here*/);

class HierarchyKeywordsNoParens {
    public static function getInstance() {
        /* testNoParamsFunctionCall5 */
        $a = new self;
        /* testNoParamsFunctionCall6 */
        $a = new static;
        /* testNoParamsFunctionCall7 */
        $a = new parent;
    }
}

// Function calls: has parameters.

/* testHasParamsFunctionCall1 */
some_function( 1 );

/* testHasParamsFunctionCall2 */
$closure(1,2,3);

class HierarchyKeywordsWithParam {
    public static function getInstance() {
        /* testHasParamsFunctionCall3 */
        $a = new self(true);
        /* testHasParamsFunctionCall4 */
        $a = new static(true);
        /* testHasParamsFunctionCall5 */
        $a = new parent(true);
    }
}

class HierarchyKeywordsAsMethodNames {
    public function self() {}
    public function static() {}
    public function parent() {
        /* testHasParamsFunctionCall6 */
        $a = self::self(true);
        /* testHasParamsFunctionCall7 */
        $a = $this->static(true);
        /* testHasParamsFunctionCall8 */
        $a = $this->parent(true);
    }

    public function callGlobalFunctionsUsingKeywords() {
        /* testHasParamsFunctionCall9 */
        $a = self(true);
        /* testHasParamsFunctionCall10 */
        $a = static(true);
        /* testHasParamsFunctionCall11 */
        $a = parent(true);
    }
}

/* testNoParamsFunctionCallFullyQualified */
\myfunction(   );

/* testHasParamsFunctionCallFullyQualifiedWithNamespace */
\My\Named\myfunction( $a );

/* testNoParamsFunctionCallPartiallyQualified */
Partially\Qualified\myfunction();

/* testHasParamsFunctionCallNamespaceOperator */
namespace\myfunction( $a );


// Arrays: no parameters.

/* testNoParamsLongArray1 */
$foo = array();

/* testNoParamsLongArray2 */
$foo = array(     );

/* testNoParamsLongArray3 */
$foo = array( /*nothing here*/ );

/* testNoParamsLongArray4 */
$foo = array(/*nothing here*/);

/* testNoParamsShortArray1 */
$bar = [];

/* testNoParamsShortArray2 */
$bar = [     ];

/* testNoParamsShortArray3 */
$bar = [ /*nothing here*/ ];

/* testNoParamsShortArray4 */
$bar = [/*nothing here*/];

// Arrays: has parameters.

/* testHasParamsLongArray1 */
$foo = array( 1 );

/* testHasParamsLongArray2 */
$foo = array(1,2,3);

/* testHasParamsLongArray3 */
$foo = array(true);

/* testHasParamsShortArray1 */
$bar = [ 1 ];

/* testHasParamsShortArray2 */
$bar = [1,2,3];

/* testHasParamsShortArray3 */
$bar = [true];

/* testNoParamsIsset */
$a = isset( /* comment */ ); // Intentional parse error.

/* testHasParamsIsset */
$a = isset( $array[$key] );

/* testNoParamsUnset */
unset(


); // Intentional parse error.

/* testHasParamsUnset */
unset(

      $hello,

);

/* testNoParamsExit */
exit();

/* testHasParamsExit */
\exit( $code );

/* testNoParamsDie */
\die( /*comment*/ );

/* testHasParamsDie */
die( $status );

/* testNoParamsNoParensAnonClass */
$anon = new class extends FooBar {};

/* testNoParamsWithParensAnonClass */
$anon = new class() {};

/* testHasParamsAnonClass */
$anon = new class( $param1, $param2 ) {};

/* testHasParamsPHP80ClassInstantiationInAttribute */
#[MyAttribute(1, self::Foo, 'string')]
function foo() {}

/* testHasParamsPHP80ClassInstantiationInMultiAttribute */
#[AttributeOne, \AttributeTwo(1, self::Foo)]
function bar() {}

/* testPHP81FirstClassCallableNotFunctionCallGlobalFunction */
$fn = strlen(...);

/* testPHP81FirstClassCallableNotFunctionCallOOMethod */
$fn = $this->method(
   ...
);

/* testPHP81FirstClassCallableNotFunctionCallVariableStaticOOMethod */
$fn = $name1::$name2( /*comment*/ ...);

// Intentional parse error.
/* testNoCloseParenthesis */
$array = array(1, 2, 3

// Intentional parse error.
/* testNoOpenParenthesis */
$array = function_call[];

// Intentional parse error. This has to be the last test in the file without a new line after it.
/* testLiveCoding */
$array = array