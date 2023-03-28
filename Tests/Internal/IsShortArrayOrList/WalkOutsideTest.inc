<?php

/* testShortArrayStopAtStartOfFile */
echo $arg, [$arg], $arg;


/* ===== Tests verifying that the loop correctly jumps over various code blocks which can be ignored for INNER brackets. ===== */

$array = [
    /**
     * Docblock.
     */
    'jump over docblock'               => 'text',
    'jump over non-cached short array' => [$a],
    'jump over short list'             => [$a] = $array,
    'jump over parenthesis'            => function_call($param, $param, $param),
    'jump over parenthesis to owner'   => array($var, $var, $var),
    'jump over scope curlies 1'        => match($a) {
        1 => true,
        2 => false,
    },
    'jump over scope curlies 2'        => function($a) {
        return $something,
    },
    'jump over non-scope curlies'      => ${'var_name'},
    'jump over heredoc'                => <<<EOD
text
EOD
    ,
    'jump over nowdoc'                 => <<<'EOD'
text
EOD
    ,
    'jump over square brackets'        => $a['key'],

    'jump over attributes'             => #[MyAttribute([1, 2, 3])] fn($a) => $something,

    /* testShortArrayJumpingOver */
    'array under investigation'        => [$a],

    // Prevent going straight to outer brackets for last item.
    [$b],
];

[
    /**
     * Docblock.
     */
    'key'                                    => $a['jump over docblock'],
    [1,2,3]['index']                         => $a['jump over short array'],
    function_call($param, $param, $param)    => $a['jump over parenthesis'],
    ${'var_name'}                            => $a['jump over non-scope curlies'],
    <<<EOD
key
EOD
                                             => $a['jump over heredoc'],
    <<<'EOD'
key
EOD
                                             => $a['jump over nowdoc'],
    $a['key']                                => $a['jump over square brackets'],

    /* testShortListJumpingOver */
    'list under investigation'               => [$a],

    // Prevent going straight to outer brackets for last item.
    'skip'                                   => [$b],

] = $array;


/* ===== Tests verifying that the loop stops as quickly as possible for OUTER brackets. ===== */

/* testOuterShortArrayStopAtSemicolon */
echo 'previous-semicolon';
echo $arg, [$arg], $arg;

?>
<?php
/* testOuterShortArrayStopAtOpenTag */
echo $arg, [$arg], $arg;

?>
<?=
/* testOuterShortArrayStopAtOpenEchoTag */
$arg, [$arg], $arg;

function foo() {
    /* testOuterShortArrayStopAtCurly */
    echo $arg, [$arg], $arg;
}

/* testOuterShortArrayStopAtParensFuncCall */
$var = function_call($arg, [$arg], $arg);

/* testOuterShortArrayStopAtParensClosureCall */
$var = $closure($arg, [$arg], $arg);

/* testOuterShortArrayStopAtParensFnCall */
$var = $fn($arg, [$arg], $arg);

/* testInnerShortListStopAtParensLongList */
// Invalid list as mixing short list syntax with list() is not allowed, but it is short list syntax.
list($a, [$b, $c], $d) = $array;


/* ===== Tests verifying that the loop stops as quickly as possible for INNER brackets. ===== */

$array = [
    'closure' => function() {
        /* testShortArrayInShortArrayStopAtCurly */
        echo $arg, [$arg], $arg;
    },
];

[
    'key' => $a,
    (function() {
        ob_start();
        /* testShortArrayInShortListStopAtSemicolon */
        echo $arg, [$arg], $arg;
        return ob_get_flush();
    })() => $b,
    'key' => $c,
] = $array;


$array = [
    /* testShortArrayInShortArrayStopAtParensFuncCall */
    'func'. 'Call' => function_call($arg, [$arg], $arg),

    /* testShortArrayInShortArrayStopAtParensClosureCall */
    'closureCall'  => $closure($arg, [$arg], $arg),

    /* testShortArrayInShortArrayStopAtParensFnCall */
    'fnCall'      => $fn($arg, [$arg], $arg),
];

[
    /* testShortArrayInShortListStopAtParensFuncCall */
    function_call($arg, [$arg], $arg) => $a,

    /* testShortArrayInShortListStopAtParensClosureCall */
    $closure($arg, [$arg], $arg)      => $b,

    /* testShortArrayInShortListStopAtParensFnCall */
    $fn($arg, [$arg], $arg)           => $c,
] = $array;


/* ===== Tests verifying that the loop stops as quickly as possible when an adjacent bracket set is found for which the type is known. ===== */

$array = [
    /* testShortArrayAdjacent1 */
    'adjacent with same code pattern' => [$a],

    /* testShortArrayAdjacent2 */
    [$a], // Adjacent not same code pattern.

    /* testShortArrayReuseTypeOfAdjacent */
    'array under investigation'      => [$a],

    // Prevent going straight to outer brackets for last item.
    [$b],
];

[
    /* testShortListAdjacent1 */
    'adjacent with same code pattern' => [$a],

    /* testShortListAdjacent2 */
    [$a], // Adjacent not same code pattern.

    /* testShortListReuseTypeOfAdjacent */
    'list under investigation'        => [$a],

    // Prevent going straight to outer brackets for last item.
    'skip'                            => [$b],

] = $array;


/* ===== Tests handling unclosed open brackets. ===== */

// Intentional parse error. This has to be the last test in the file.
[$a, /* testLiveCodingNested */ [$b],
