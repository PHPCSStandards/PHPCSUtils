<?php

/* testNotATernaryToken */
echo 123;

/* testLongTernary */
$foo = ($a === true) ? $b : $c;

/* testShortTernaryNoSpace */
$foo = $foo ?: 'bar';

/* testShortTernaryLongSpace */
$foo = $foo ?

    : 'bar';

/* testShortTernaryWithCommentAndAnnotations */
$foo = $foo ? /* deliberately left empty */
    // phpcs:ignore Stnd.Cat.Sniff -- For reasons.
    : 'bar';

/* testParseError */
// Intentional parse error. This has to be the last test in the file.
$foo = $foo ?
