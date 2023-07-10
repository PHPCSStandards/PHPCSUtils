<?php

/* testGetDoubleArrowPtr */
$array = [
    /* testValueNoArrow */
    'value no arrow',

    /* testArrowNumericIndex */
    123 => 'arrow numeric index',

    /* testArrowStringIndex */
    'foo' => 'arrow string index',

    /* testArrowMultiTokenIndex */
    'concat' . 'index' => 'arrow multi token index',

    /* testNoArrowValueShortArray */
    [
        'value only' => 'arrow belongs to value',
    ],

    /* testNoArrowValueLongArray */
    array(
        'value only' => 'arrow belongs to value',
    ),

    /* testNoArrowValueNestedArrays */
    array(
        [
            array(
                ['key' => 'arrow belongs to nested array'],
            ),
        ],
    ),

    /* testNoArrowValueClosure */
    function() {
        echo 'closure as value arrow belongs to value';
        return array( $a => $b );
    },

    /* testArrowValueShortArray */
    'index and value short array' => [
        'index and value' => '',
    ],

    /* testArrowValueLongArray */
    'index and value long array' => array(
        'index and value' => '',
    ),

    /* testArrowValueClosure */
    'index and value closure' => function() {
        echo 'closure as value arrow belongs to value';
        return array( $a => $b );
    },

    /* testNoArrowValueAnonClassForeach */
    new class($iterable) {
        public function __construct($iterable) {
            $return = 0;
            foreach ($iterable as $key => $value) {
                $return = $key * $value;
            }
            return $return;
        }
    },

    /* testNoArrowValueClosureYieldWithKey */
    function() { yield 'k' => $x; },

    /* testArrowKeyClosureYieldWithKey */
    function() { yield 'k' => $x; }() => 'value',

    /* testFnFunctionWithKey */
    'fn' => fn ($x) => yield 'k' => $x,

    /* testNoArrowValueFnFunction */
    fn ($x) => yield 'k' => $x,

    /* testTstringKeyNotFnFunction */
    CONSTANT_NAME => 'value',

    /* testKeyPropertyAccessFnPHPCS353-354 */
    ($obj->fn) => 'value',

    /* testDoubleArrowTokenizedAsTstring-PHPCS2865 */
    $obj->fn => 'value',

    /* testNoArrowValueMatchExpr */
    match($a) {
        FOO     => BAR,
        default => [0 => BAZ],
    },

    /* testArrowValueMatchExpr */
    'key' => match($a) {
        [0 => 10] => BAR,
        default   => BAZ,
    },

    /* testArrowKeyMatchExpr */
    match($a) {
        FOO     => BAR,
        default => [0 => 10],
    } => 'value',
    
    /* testNoArrowKeyedLongListInValue */
    list( 'key1' => $a, 'key2' => $b ) = $array,

    /* testNoArrowKeyedShortListInValue */
    [ 'key1' => $a, 'key2' => $b ] = $array,

    /* testNoArrowValueClosureWithAttribute */
    #[MyAttribute([0 => 'value'])] function() { /* do something */ }(),

    /* testArrowKeyClosureWithAttribute */
    #[MyAttribute([0 => 'value'])] function() { /* do something */ }() => 'value',

    /* testEmptyArrayItem */
    // Intentional parse error.
    ,
];
