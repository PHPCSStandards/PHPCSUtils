<?php

/* testPHP82PseudoTypeTrue */
function pseudoTypeTrue(?true $var = true) {}

/* testPHP82PseudoTypeFalseAndTrue */
// Intentional fatal error - Type contains both true and false, bool should be used instead, but that's not the concern of the method.
function pseudoTypeFalseAndTrue(true|false $var = true) {}
