<?php

/* testPositionalArgs */
array_fill(START_INDEX, \COUNT, MyNS\VALUE);

/* testNamedArgs */
array_fill(start_index: 0, count: 100, value: 50);

/* testNamedArgsMultiline */
array_fill(
    start_index : 0,
    count       : 100,
    value       : 50,
);

/* testNamedArgsWithWhitespaceAndComments */
array_fill ( start_index : 0, /*comment */ count /*comment*/ : 100, value: 50);

/* testMixedPositionalAndNamedArgs */
htmlspecialchars($string, double_encode: false);

/* testNestedFunctionCallOuter */
array_fill(
    start_index: /* testNestedFunctionCallInner1 */ $obj->getPos(skip: false),
    count: /* testNestedFunctionCallInner2 */ count(array_or_countable: $array),
    value: 50
);

/* testNamespacedFQNFunction */
\Fully\Qualified\function_name(label: $string, more:false);

/* testVariableFunction */
$fn(label: $string, more:false);

/* testClassInstantiationStatic */
$obj = new static(label: $string, more:false);

/* testAnonClass */
$anon = new class(label: $string, more: false) {
    public function __construct($label, $more) {}
};

function myfoo( $💩💩💩, $Пасха, $_valid) {}
/* testNonAsciiNames */
foo(💩💩💩: [], Пасха: 'text', _valid: 123);

/* testMixedPositionalAndNamedArgsWithTernary */
foo( $cond ? true : false, name: $value2 );

/* testNamedArgWithTernary */
foo( label: $cond ? true : false, more: $cond ? CONSTANT_A : CONSTANT_B );

/* testTernaryWithFunctionCallsInThenElse */
echo $cond ? foo( label: $something ) : /* testTernaryWithFunctionCallsInElse */ bar( more: $something_else );

/* testExitWithNamedParam */
exit( status: 1 );

/* testFQNDieWithNamedParam */
\die( status: 1 );

/* testCompileErrorNamedBeforePositional */
// Not the concern of PHPCSUtils. Should still be handled.
test(param: $bar, $foo);

/* testDuplicateName */
// Error Exception, but not the concern of PHPCSUtils. Should still be handled.
test(param: 1, param: 2);

/* testIncorrectOrderWithVariadic */
// Error Exception, but not the concern of PHPCSUtils. Should still be handled.
array_fill(start_index: 0, ...[100, 50]);

/* testPHP81NamedParamAfterVariadic */
// Prior to PHP 8.1, this was a compile error, but this is now supported.
test(...$values, param: $value);

/* testParseErrorNoValue */
// Not the concern of PHPCSUtils. Should still be handled (but currently isn't).
//test(param1:, param2:);

/* testParseErrorDynamicName */
// Parse error. Ignore.
function_name($variableStoringParamName: $value);

/* testReservedKeywordAsName */
// Note: do not remove any of these - some are testing very specific cross-version tokenizer issues.
foobar(
    abstract: $value,
    class: $value,
    const: $value,
    function: $value,
    iterable: $value,
    match: $value,
    protected: $value,
    object: $value,
    parent: $value,
);
