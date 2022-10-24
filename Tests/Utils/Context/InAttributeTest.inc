<?php

/* testNotAttribute */
$var = [1, self::MYCONSTANT];

/* testAttribute */
$fn = #[MyAttribute(1, self::MYCONSTANT)] fn($a) => $something;

/* testMultiLineAttributeWithVars */
// Illegal syntax, vars not allowed in attribute, but not our concern.
#[
    MyAttribute([$a, $b])
]
function foo() {}

/* testParseError */
#[
    UnClosedAttribute()
