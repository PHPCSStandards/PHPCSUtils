<?php

// Intentional parse error.
// This must be the only test in the file.

/* testAttributeWithoutTarget */
#[MissingTarget]
#[AnotherAttribute]
/**
 * Docblock
 */
#[YetAnotherAttribute]

echo 'Hello!';

const FOO = 10;
