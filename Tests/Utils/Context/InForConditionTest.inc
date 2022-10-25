<?php

/* testNoParentheses */
echo $target;

/* testNoParenthesisOwner */
echo ($target + 1);

/* testOwnerNotFor */
if ($target + 1 > 10) {}

/* testOwnerNotForNestedParentheses */
foreach (function_call(array($target)) as $value) {}

/* testNotForMethodCall */
$obj->for ($target) {}

/* testForParseErrorTwoExpressions */
for ($i = $target; $i < 10) {} // Intentional parse error.

/* testForParseErrorFourExpressions */
for ($i = $target; $i < 10; $i++; $nonsense) {} // Intentional parse error.

/* testFor */
for ($i = 0; $i < 10; $target++) :
    // Do something.
endfor;

/* testForMultipleStatementsInExpr */
for ($i = 0, $c = namespace\sizeof($target); $i < $c /* testForSecondSemicolon */ ; $j += $i, print $i, ++$i) {}

/* testForEmptyExpr1 */
For (; $i < 10; $target + $i) {}

/* testForEmptyExpr2 */
for ($target = 0; ; ++$i) {}

/* testForEmptyExpr3 */
for ($i = 0; $target > $i;) {}

/* testForEmptyExpr12 */
for (;; $next = find($target)) {}

/* testForEmptyExpr13 */
for (; $target->valid();) {}

/* testForEmptyExpr23 */
for ($i = $target; ; /* Deliberately empty */) {}

/* testForEmptyExpr123 */
for (;;) {}

/* testForWithNestedSemiColon */
for ($closure = function($a, $b) { return $a * $b; }, $i = 10; $closure($a, $b) < 10; $i++) {}

/* testNestedFor */
add_action('something', function($array) {
    for($a = call(); $obj->valid($a); $obj->next($a)) {}
    return;
});

// Intentional parse error. This has to be the last test in the file.
/* testParseError */
for ($i = $target; $i < 10
