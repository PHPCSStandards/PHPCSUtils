<?php

/* testNoParams */
$array = array();

/* testFunctionCall */
myfunction( 1, 2, 3, 4, 5, 6, true );

/* testSimpleLongArray */
$foo = array( 1, 2, 3, 4, 5, 6, true );

/* testSimpleShortArray */
$foo = [ 1, 2, 3, 4, 5, 6, true ];

/* testLongArrayWithKeys */
$foo = array( 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6, 'g' => true );

/* testShortArrayWithKeys */
$foo = [
    'a' => 1,
    'b' => 2,
    'c' => 3,
    'd' => 4,
    'e' => 5,
    'f' => 6,
    'g' => true,
];

/*
 * NOTE: the below test code is duplicate, but the cache tests need unique $stackPtrs to
 * prevent the result of earlier tests poluting the results of the caching tests.
 */

/* testCachedWithLimit */
$foo = array( 1, 2, 3, 4, 5, 6, true );

/* testCachedWithoutLimitWhenTotalItemsLessThanLimit */
$foo = array( 'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6, 'g' => true )

/* testCachedWithoutLimitWhenTotalItemsEqualsLimit */
$foo = [
    'a' => 1,
    'b' => 2,
    'c' => 3,
    'd' => 4,
    'e' => 5,
    'f' => 6,
    'g' => true,
];

/* testRetrievedFromCacheWhenCachePreviouslySetWithoutLimit */
myfunction( 1, 2, 3, 4, 5, 6, true );

/* testRetrievedFromScratchIfNoSuitableCacheFound */
myfunction( 1, 2, 3, 4, 5, 6, true );
