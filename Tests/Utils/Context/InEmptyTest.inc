<?php

/* testNotEmptyMethodCall */
$obj->empty($target);

/* testOwnerNotEmpty */
if ($target + 1 > 10) {}

/* testInEmpty */
$a = empty($target);

/* testInEmptynested */
if ((Empty($obj->methodCall($target->prop)) || $somethingElse)) {}

/* testParseError */
// This has to be the last test in the file.
empty($target
