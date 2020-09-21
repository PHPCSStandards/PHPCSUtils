<?php

/* testNotIssetMethodCall */
$obj->isset($target);

/* testOwnerNotIsset */
if ($target + 1 > 10) {}

/* testInIsset */
$a = isset($target);

/* testInIssetnested */
if ((Isset($something, $target, $else) || $somethingElse)) {}

/* testParseError */
// This has to be the last test in the file.
isset($target
