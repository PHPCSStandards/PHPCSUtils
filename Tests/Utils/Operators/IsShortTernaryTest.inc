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

/* testDontConfuseWithNullCoalesce */
$foo = $foo ?? 0;

/* testDontConfuseWithNullCoalesceEquals */
$foo ??= 0;

class Foo {
    /* testDontConfuseWithNullable1 */
    protected ?int $property = 1;

    public function bar(
        /* testDontConfuseWithNullable2 */
        ?bool $param
        /* testDontConfuseWithNullable3 */
    ) : ?\My\ClassName {
    }
}

/* testParseError */
// Intentional parse error. This has to be the last test in the file.
$foo = $foo ?
