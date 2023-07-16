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

/* testLiveCoding */
// Intentional parse error. Redundancy testing.
abstract class
