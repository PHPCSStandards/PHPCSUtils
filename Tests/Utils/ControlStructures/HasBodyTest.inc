<?php

/* testNotControlStructure */
echo 123;

/*
 * Test curly brace / no body control structures.
 */

/* testIfWithoutBody */
if ($a); // Surprisingly enough not a parse error.

/* testIfWithoutBodyDueToCloseTag */
if ($a) ?>
    This will always be printed as the if is closed by the PHP close tag.
<?php

if (true)
    do_something();
/* testElseIfWithoutBodyDueToCloseTag */
else if(false) ?>
    This will always be printed as the elseif is closed by the PHP close tag.
<?php

if (true)
    do_something();
/* testElseWithoutBodyDueToCloseTag */
else ?>
    This will always be printed as the else is closed by the PHP close tag.
<?php

/* testIfEmptyBody */
if ( true ) {
    // Comment.

/* testElseIfEmptyBody */
} elseif (false) {}

/* testElseSpaceIfEmptyBody */
else if (false) {
    // Code.

/* testElseEmptyBody */
} else {
}

/* testIfWithCode */
if ( true ) {
    echo 123;

/* testElseIfWithCode */
} elseif (false) {
    echo 123;
}

/* testElseSpaceIfWithCode */
else if (false) {
    echo 123;

/* testElseWithCode */
} else {
    echo 123;
}

/* testForWithoutBody */
for ($i = 1, $j = 0; $i <= 10; $j += $i, print $i, $i++);

/* testForWithoutBodyDueToCloseTag */
for ($i = 1, $j = 0; $i <= 10; $j += $i, print $i, $i++) ?>
    This will always be printed as the for is closed by the PHP close tag.
<?php

/* testForEmptyBody */
for ($i = 1; $i <= 10; $i++) {}

/* testForWithCode */
for ($i = 1; $i <= 10; $i++) {
    echo $i;
}

/* testForEachWithoutBody */
foreach($a as $b => $c); // Surprisingly enough not a parse error.

/* testForEachWithoutBodyDueToCloseTag */
foreach($a as $b => $c) ?>
    This will always be printed as the foreach is closed by the PHP close tag.
<?php

/* testForEachEmptyBody */
foreach ($a as $k => $v) {
    // phpcs:ignore Stnd.Cat.Sniff -- for reasons.
}

/* testForEachWithCode */
foreach ($a as $k => $v) {
    echo "Key: $k; Current value of \$a: $v.\n";
}

/* testWhileWithoutBody */
while (++$i <= 10) /*comment*/ ;

/* testWhileWithoutBodyDueToCloseTag */
while (++$i <= 10) ?>
    This will always be printed as the while is closed by the PHP close tag.
<?php

/* testWhileEmptyBody */
while (++$i <= 10) {}

/* testWhileWithCode */
while (++$i <= 10) {
    echo $i;
}

/* testDoWhileEmptyBody */
do {
} while (++$i <= 10);

/* testDoWhileWithCode */
do {
    echo $i;
} while (++$i <= 10);

/* testSwitchWithoutBody */
// Intentional parse error.
switch ($foo);

/* testSwitchWithoutBodyDueToCloseTag */
// Intentional parse error.
switch ($foo) ?>
<?php

/* testSwitchEmptyBody */
switch ($foo) {
    // Kind of useless, but not a parse error.
}

/* testSwitchWithCode */
switch ($foo) {
    case 1:
        echo '<div>something</div>';
        break;
}

/* testDeclareWithoutBody */
declare(ticks=1);

/* testDeclareWithoutBodyDueToCloseTag */
declare(ticks=1) ?>
<?php

/* testDeclareEmptyBody */
declare(ticks=1) {
    // Comment.
}

/* testDeclareWithCode */
declare(ticks=1) {
    echo 'ticking';
}

/*
 * Alternative control structure syntax.
 */

/* testAlternativeIfEmptyBody */
if (true):
    // Code.

/* testAlternativeElseIfWithCode */
elseif (false):
    echo 123;

/* testAlternativeElseWithCode */
else:
    echo 123;
endif;

/* testAlternativeForEmptyBody */
for ($i = 1; $i <= 10; $i++) :

endfor;

/* testAlternativeForWithCode */
for ($i = 1; $i <= 10; $i++) :
    echo $i;
endfor;

/* testAlternativeForeachEmptyBody */
foreach ($a as $k => $v):
    // Comment.
endforeach;

/* testAlternativeForeachWithCode */
foreach ($a as $k => $v):
    echo "Key: $k; Current value of \$a: $v.\n";
endforeach;

/* testAlternativeWhileEmptyBody */
while (++$i <= 10):
    // phpcs:disable Stnd.Cat.Sniff -- for reasons.
endwhile;

/* testAlternativeWhileWithCode */
while (++$i <= 10):
    echo $i;
endwhile;

/* testAlternativeSwitchEmptyBody */
switch ($foo) :
endswitch;

/* testAlternativeSwitchWithCode */
switch ($foo) :
    case 1:
        echo '<div>something</div>';
endswitch;

/* testAlternativeDeclareEmptyBody */
declare (ticks = 1):
    // comment
enddeclare;

/* testAlternativeDeclareWithCode */
declare (ticks = 1):
    echo 'ticking';
enddeclare;

/*
 * Control structures without braces.
 * Without a body, these are a parse error straight away.
 */

/* testInlineIfWithCode */
if (true)
    function_call($a);

/* testInlineElseIfWithCode */
elseif (false)
    function_call($a);

/* testInlineElseWithCode */
else
    function_call($a);

/* testInlineForWithCode */
for ($i = 1; $i <= 10; $i++)
    echo $i;

/* testInlineForEachWithCode */
foreach ($a as $k => $v)
    echo "Key: $k; Current value of \$a: $v.\n";

/* testInlineWhileWithCode */
while (++$i <= 10)
    echo $i;

/* testInlineDoWhileWithCode */
do
    echo $i;
while (++$i <= 10);


/*
 * PHP 8.0 match expressions.
 */

/* testMatchEmptyBody */
// Intentional fatal error, "unhandled match case", but not the concern of this method.
$match = match($a) {};

/* testMatchEmptyBodyWithComment */
// Intentional fatal error, "unhandled match case", but not the concern of this method.
$match = match($a) {
    // Deliberately empty.
};

/* testMatchWithCode */
$match = match ($a) {
    0 => 'Foo',
    1 => 'Bar',
    2 => 'Baz',
};

// Live coding.
// Intentional parse error. This test has to be the last in the file.
    if ($a) {
        // Code.
    /* testElseLiveCoding */
    } else {
        // Code.
