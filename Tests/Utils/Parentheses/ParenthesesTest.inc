<?php

/* testNoParentheses */
$a = 1;

/* testIfWithArray */
if (($a + $b) === function_call(array($c, $d))) {

/* testElseIfWithClosure */
} elseif ( array_map( function( $a ) {}, $array) ) {}

/* testForeach */
foreach( array(array(1, array(2, 3)), array(23, array(45, 67))) as list /* comment */ ( $a, list($b, $c) ) ) {}

/* testFunctionwithArray */
function test($param, $array = array(1, 2, 3)) {}

/* testForWithTernary */
for ($i = ( $a ? get_a($b) : get_b($c) ); $i < count($array); $i++ ) {}

/* testWhileWithClosure */
while (($result = function_call( $a )) > 0 && ((function($p) {/* do something */})($result) === true)) {}

/* testAnonClass */
$anonClass = new class(
    new class implements Countable {
		function test($param) {
			do {
				try {
				} catch( Exception $e ) {
				}
			} while($a === true);
		}
    }
) extends DateTime {
};

/* testListOnCloseParens */
list($a, $b) = $array;

/* testArrayFunctionCallWithArrowFunctionParam */
$value = array(ClassName::functionCall('Text', fn($param) => $param->get()));

/* testFunctionNamedFn */
function fn() {}

/* testNoOwnerOnCloseParens */
$a = ($b + $c);

/* testFunctionCallFnPHPCS353-354 */
$value = $obj->fn(true);

/* testArrowFunctionByReference */
$fn = fn &($x) => $x;

/* testIfWithIssetEmpty */
if ( isset( $a, $b ) && ! empty ($c) ) {
    /* testUnset */
    unset($a[1], $b->prop);
}

/* testEval */
eval($a . $b . $c );

/* testExit */
if (defined('FOO') || die('message')) {}

/* testFQNExit */
if (defined('FOO') || \exit(10)) {}

/* testMatch */
$m = match(count($a)) {
    1 => true,
    2 => false,
    default => false,
};

/* testClosureUseInArray */
$array = array(
    'cl' => function () use /*comment*/ ($var) {},
);

// Intentional parse error. This has to be the last test in the file.
/* testParseError */
declare(ticks=1
