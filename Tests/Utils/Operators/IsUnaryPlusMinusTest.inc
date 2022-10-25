<?php

/* testNonUnaryPlus */
$a = 1 + 2;

/* testNonUnaryMinus */
$a = 1-2;

/* testNonUnaryPlusArrays */
$a = [1] + [2];

/* testUnaryMinusArithmetic */
$a = 1 / -2;

/* testUnaryPlusArithmetic */
$a = 1 ** +2;

/* testUnaryMinusConcatenation */
$a = 'ID' . -$var;

/* testUnaryPlusIntAssignment */
$a = +1;

/* testUnaryMinusVariableAssignment */
$a += -$b;

/* testUnaryPlusFloatAssignment */
$a **= + 1.1;

/* testUnaryMinusBoolAssignment */
$a /= -true;

/* testUnaryPlusStringAssignmentWithComment */
$a &= + /* comment */ '1';

/* testUnaryMinusStringAssignment */
$a .= - <<<'EOD'
123
EOD;

/* testUnaryPlusNullAssignment */
$a = +null;

/* testUnaryMinusVariableVariableAssignment */
$a .= -${$var};

/* testUnaryPlusIntComparison */
$a = ($b === + 1);

/* testUnaryPlusIntComparisonYoda */
$a = (+1 <=> $b);

/* testUnaryMinusFloatComparison */
$a = ($b !== - 1.1);

/* testUnaryMinusStringComparisonYoda */
$a = (-'1' != $b);

/* testUnaryPlusVariableBoolean */
$a = ($a && - $b);

/* testUnaryMinusVariableBoolean */
$a = ($a || -$b);

/* testUnaryPlusLogicalXor */
$a = ($a xor -$b);

/* testUnaryMinusTernaryThen */
$a = $a ? -1 :
    /* testUnaryPlusTernaryElse */
    + 10;

/* testUnaryMinusCoalesce */
$a = $a ?? -1 ;

/* testUnaryPlusIntReturn */
return +1;

/* testUnaryMinusFloatReturn */
return -1.1;

/* testUnaryPlusIntExit */
exit -1;

/* testUnaryPlusPrint */
print +$b;

/* testUnaryMinusEcho */
echo -$a;

/* testUnaryPlusYield */
yield +$a;

/* testUnaryPlusArrayAccess */
$a = $array[ + 2 ];

/* testUnaryMinusStringArrayAccess */
if ($line{-1} === ':') {}

$array = array(
    /* testUnaryPlusLongArrayAssignment */
    +1,
    /* testUnaryMinusLongArrayAssignmentKey */
    -1 =>
        /* testUnaryPlusLongArrayAssignmentValue */
        +20,
);

$array = [
    /* testUnaryPlusShortArrayAssignment */
    +1 =>
        /* testNonUnaryMinusShortArrayAssignment */
        5-20,
];

/* testUnaryMinusCast */
$a = (bool) -2;

functionCall(
    /* testUnaryPlusFunctionCallParam */
    +2,
    /* testUnaryMinusFunctionCallParam */
    - 123.456,
);

/* testUnaryPlusDeclare */
declare( ticks = +10 );

switch ($a) {
    /* testUnaryPlusCase */
    case +20:
        /* testUnaryPlusContinue */
        continue +2;

    /* testUnaryMinusCase */
    case -1.23:
        /* testUnaryPlusBreak */
        break +1;
}

/* testUnaryMinusArrowFunction */
$fn = static fn(DateTime $a, DateTime $b): int => -($a->getTimestamp() <=> $b->getTimestamp());

$matcher = match ($a) {
    /* testUnaryPlusMatchArrow */
    'a' => +1,
    /* testUnaryMinusMatchArrow */
    'b', 'c', 'd' => -2,
    /* testUnaryMinusMatchDefault */
    default => -3,
};

// Testing `$a = -+-+10`;
$a =
    /* testSequenceNonUnary1 */
    -
    /* testSequenceNonUnary2 */
    +
    /* testSequenceNonUnary3 */
    -
    /* testSequenceUnaryEnd */
    + /*comment*/ 10;

/* testPHP74NumericLiteralIntCalc1 */
$a = 667_083 - 11;

/* testPHP74NumericLiteralIntCalc2 */
$a = 74_083 + 1_1;

/* testPHP74NumericLiteralFloatCalc1 */
$a = 6.674_08e3 - 1_1;

/* testPHP74NumericLiteralFloatCalc2 */
$a = 6.674_08e3 + 11;

// Intentional parse error. This has to be the last test in the file.
/* testParseError */
$a = -
