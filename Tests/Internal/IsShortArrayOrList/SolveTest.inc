<?php

/* ===== Tests involving tokens correctly tokenized as square brackets. ===== */

/* testSquareBrackets */
$b[] = $c;


/* ===== Tests involving tokens correctly tokenized as short array brackets. ===== */

/* testShortList */
[$b] = $c;

/* testShortArrayInAttribute */
$var = #[MyAttribute([1, self::MYCONSTANT])] fn($a) => $something;

/* testShortArrayInForeach */
foreach ([1, 2, 3] as /* testShortListInForeach */ [$id, $name, $info]) {}

/* testShortArray */
$array = [$a];

$array = [/* testNestedShortArrayParentBracketBefore */[$b], [$a]];
[$a, /* testNestedShortListParentBracketAfter */ [$a, $b]] = $array;
[$a, /* testNestedShortListParentBracketAfterWithTrailingComma */ [$a, $b],] = $array;

$array = [
    // Prevent going straight to outer brackets for first item.
    [$a, $b],

    /* testNestedShortArrayContentNonAmbiguous */
    [ 1, 2, ],

    // Prevent going straight to outer brackets for last item.
    [$a, $b],
];

[
    // Prevent going straight to outer brackets for first item.
    [$a, $b],

    /* testNestedShortListContentNonAmbiguous */
    [$a,, $b],

    // Prevent going straight to outer brackets for last item.
    [$a, $b],
] = $array;

/* testOuterShortArray */
echo 'previous-semicolon';
echo $arg, [$arg], $arg;


/* ===== Tests involving various illegal syntaxes/parse errors. ===== */

// Intentional parse error. This has to be the last test in the file.
[$a, /* testLiveCodingNested */ [$b]
