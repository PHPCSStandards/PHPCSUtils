<?php

/* testNoParentheses */
echo $target;

/* testNoParenthesisOwner */
echo ($target + 1);

/* testOwnerNotForeach */
if ($target + 1 > 10) {}

/* testOwnerNotForeachNestedParentheses */
for ($i = 1; function_call(array($target)) < 10; $i++) {}

/* testNotForeachMethodCall */
$obj->foreach ($target) {}

/* testForeachWithoutAs */
foreach ($target) {} // Intentional parse error.

/* testForeachValue */
foreach ($array as $value) {}

/* testForeachKeyValue */
foreach ($iterator AS $key => $value) {}

/* testForeachBeforeLongArrayMinimalWhiteSpace */
foreach (array('a' => 1, 'b' => 2, 3)as$key => $value) {}

/* testForeachBeforeFunctionCall */
Foreach (callMe(array($target)) As $key => $value) {}

/* testForeachVarAfterAsList */
foreach($array   as   list($target, $else)) {}

/* testForeachVarAfterAsShortList */
foreach($array as [$something, $target]) {}

/* testForeachVarAfterAsKeyedList */
foreach ($array as list('id' => $id, 'name' => $target)):
    // Do something.
endforeach;

/* testNestedForeach */
add_action('something', function($array) {
    foreach($array as $target) {}
    return;
});

// Intentional parse error. This has to be the last test in the file.
/* testParseError */
foreach ($array as $target {}
