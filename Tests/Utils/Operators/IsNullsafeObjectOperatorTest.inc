<?php

/*
 * Null safe operator.
 */

/* testUnsupportedToken */
echo $obj::foo;

/* testObjectOperator */
echo $obj->foo;

/* testNullsafeObjectOperator */
echo $obj?->foo;

/* testNullsafeObjectOperatorWriteContext */
// Intentional parse error, but not the concern of this method.
$foo?->bar->baz = 'baz';

/* testTernaryThen */
echo $obj ? $obj->prop : /* testObjectOperatorInTernary */ $other->prop;

/* testParseErrorWhitespaceNotAllowed */
echo $obj ?
    -> foo;

/* testParseErrorCommentNotAllowed */
echo $obj ?/*comment*/-> foo;

/* testLiveCoding */
// Intentional parse error. This has to be the last test in the file.
echo $obj?

