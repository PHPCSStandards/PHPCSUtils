<?php

/* testParseError */
// Intentional parse error.
$incompleteUse = function(int $a, string $b = '') use(&$import,
