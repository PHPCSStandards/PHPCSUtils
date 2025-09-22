<?php

// Intentional parse error.
// This must be the only test in the file.
// Testing parse error tolerance (keywords used in an invalid context).

/* testInvalidPHP */
#[MyAttribute]
#[AnotherAttribute]
/**
 * Docblock
 */
#[YetAnotherAttribute]

final protected const FOO = 10;
