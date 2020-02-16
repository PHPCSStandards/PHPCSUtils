<?php

/* testNotDeclare */
echo 123;

/* testFileScope */
declare(ticks=1);

/* testCurlies */
declare(ticks=1) {
    // entire script here
}

/* testNestedCurliesOutside */
declare(ticks=1) {
    /* testNestedCurliesInside */
    declare ( ticks = 2 ) {
        // entire script here
    }
}

/* testAlternativeSyntax */
declare(ticks=1) :
    // entire script here
enddeclare;

/* testAlternativeSyntaxNestedLevel1 */
declare(ticks=1) :
    /* testAlternativeSyntaxNestedLevel2 */
    declare ( ticks = 2 ) :
        /* testAlternativeSyntaxNestedLevel3 */
        declare(ticks=3) :
            // entire script here
        enddeclare;
    enddeclare;
enddeclare;

/* testMixedNestedLevel1 */
declare(ticks=1) :
    /* testMixedNestedLevel2 */
    declare ( ticks = 2 ) {
        /* testMixedNestedLevel3 */
        declare(ticks=3) :
	        /* testMixedNestedLevel4 */
	        declare(ticks=4);
            // entire script here
        enddeclare;
    }
enddeclare;

/* testLiveCoding */
// Intentional parse error.
declare(ticks=1
