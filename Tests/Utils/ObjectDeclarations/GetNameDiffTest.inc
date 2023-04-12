<?php

/* testTraitStartingWithNumber */
trait 5InvalidNameStartingWithNumber {
}

/* testInterfaceFullyNumeric */
interface 12345 {}

/* testInvalidInterfaceName */
interface switch{ // Intentional parse error.
    public function someFunction();
}

/* testFunctionReturnByRefWithReservedKeywordParent */
function &parent() {}

/* testFunctionReturnByRefWithReservedKeywordSelf */
function &self() {}

/* testFunctionReturnByRefWithReservedKeywordStatic */
function &static() {}

/* testLiveCoding */
// Intentional parse error. Redundancy testing.
abstract class
