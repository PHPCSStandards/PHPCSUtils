<?php

/* testTokenizerIssue1971PHPCSlt330gt271A */
// This has to be the first test in the file!
[&$a, [$b, /* testTokenizerIssue1971PHPCSlt330gt271B */ &$c]] = $array;

/* testTokenizerIssue1284PHPCSlt280A */
if ($foo) {}
[&$a, /* testTokenizerIssue1284PHPCSlt280B */ &$b] = $c;

/* testTokenizerIssue1284PHPCSlt280C */
if ($foo) {}
[&$a, $b];
