<?php

/* testNotIfElseifOrElse */
echo 123;

/* testIf */
if ( true ) {
    echo 123;

/* testElseIf */
} elseif (false) {
    echo 123;
}
/* testElseSpaceIf */
else if (false) {
    echo 123;

/* testElseCommentIfElse */
} else /*comment*/
  /* testElseCommentIfIf */
  if (false) {
    echo 123;

/* testElse */
} else {
    echo 123;
}

/* testAlternativeIf */
if ( true ) :
    echo 123;

/* testAlternativeElseIf */
elseif (false) :
    echo 123;

/* testAlternativeElse */
else /*comment*/ :
    echo 123;
endif;

/* testInlineIf */
if ( true )
    echo 123;

/* testInlineElseIf */
elseif (false)
    echo 123;

/* testInlineElse */
else /*comment*/
    echo 123;

    if ($a) {
        echo 123;
    /* testLiveCoding */
    // Intentional parse error. This test has to be the last in the file.
    } else
