<?php

/* testNotAnAcceptedToken */
interface NotAFunctionCallOrArray {}

class Foo {
    public function Bar() {
        /* testNotACallToConstructor */
        $a = self::some_method();
    }
}

/* testShortListNotShortArray */
[ $a, $b ] = $array;

// Function calls: no parameters.

/* testNoParamsFunctionCall1 */
some_function();

/* testNoParamsFunctionCall2 */
some_function(     );

/* testNoParamsFunctionCall3 */
some_function( /*nothing here*/ );

/* testNoParamsFunctionCall4 */
$closure(/*nothing here*/);

// Function calls: has parameters.

/* testHasParamsFunctionCall1 */
some_function( 1 );

/* testHasParamsFunctionCall2 */
$closure(1,2,3);

class Bar {
    public static function getInstance() {
        /* testHasParamsFunctionCall3 */
        return new self(true);
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

/* testNoParamsNoParensAnonClass */
$anon = new class extends FooBar {};

/* testNoParamsWithParensAnonClass */
$anon = new class() {};

/* testHasParamsAnonClass */
$anon = new class( $param1, $param2 ) {};

// Intentional parse error.
/* testNoCloseParenthesis */
$array = array(1, 2, 3

// Intentional parse error.
/* testNoOpenParenthesis */
$array = function_call[];

// Intentional parse error. This has to be the last test in the file without a new line after it.
/* testLiveCoding */
$array = array