<?php

/* testNoParams */
myfunction();

/* testFunctionCall */
myfunction( 1, 2, 3, 4, 5, 6, true );

/* testFunctionCallNestedFunctionCall */
dirname( dirname( __FILE__ ) ); // 1

/* testAnotherFunctionCall */
mktime($stHour, 0, 0, $arrStDt[0], $arrStDt[1], $arrStDt[2]); // 6

/* testFunctionCallTrailingComma */
json_encode( array(), );

/* testFunctionCallNestedShortArray */
json_encode(['a' => $a,] + (isset($b) ? ['b' => $b,] : []));

/* testLongArrayNestedFunctionCalls */
$foo = array(some_call(5, 1), another(1), why(5, 1, 2), 4, 5, 6); // 6

/* testSimpleLongArray */
$foo = array( 1, 2, 3, 4, 5, 6, true );

/* testLongArrayWithKeys */
$foo = array('a' => $a, 'b' => $b, 'c' => $c);

/* testShortArrayNestedFunctionCalls */
$bar = [0, 0, date('s', $timestamp), date('m'), date('d'), date('Y')]; // 6

/* testShortArrayMoreNestedFunctionCalls */
$bar = [str_replace("../", "/", trim($value))]; // 1

/* testShortArrayWithKeysAndTernary */
$bar = [0 => $a, 2 => $b, 6 => (isset($c) ? $c : null)];

/* testShortArrayWithKeysTernaryAndNullCoalesce */
$bar = [
    'foo' => 'foo',
    'bar' => $baz ?
        ['abc'] :
        ['def'],
    'hey' => $baz ??
        ['one'] ??
        ['two'],
];

/* testNestedArraysToplevel */
$array = array(
    '1' => array(
        0 => 'more nesting',
        /* testNestedArraysLevel2 */
        1 => array(1,2,3),
    ),
    /* testNestedArraysLevel1 */
    '2' => [
        0 => 'more nesting',
        1 => [1,2,3],
    ],
);

/* testFunctionCallNestedArrayNestedClosureWithCommas */
preg_replace_callback_array(
    /* testShortArrayNestedClosureWithCommas */
    [
        '~'.$dyn.'~J' => function ($match) {
            echo strlen($match[0]), ' matches for "a" found', PHP_EOL;
        },
        '~'.function_call().'~i' => function ($match) {
            echo strlen($match[0]), ' matches for "b" found', PHP_EOL;
        },
    ],
    $subject
);

/* testShortArrayNestedAnonClass */
$array = [
    /**
     * Docblock to skip over.
     */
    'class' => new class() {
        public $prop = [1,2,3];
        public function test( $foo, $bar ) {
            echo $foo, $bar;
        }
    },
    /**
     * Docblock to skip over.
     */
    'anotherclass' => new class() {
        public function test( $foo, $bar ) {
            echo $foo, $bar;
        }
    },
];

/* testLongArrayArrowFunctionWithYield */
$array = array(
          1 => '1',
          2 => fn ($x) => yield 'a' => $x,
          3 => '3',
         );

/* testVariableFunctionCall */
$closure($a, (1 + 20), $a & $b );

/* testStaticVariableFunctionCall */
self::$closureInStaticProperty($a->property, $b->call() );

/* testIsset */
if ( isset(
    $variable,
    $object->property,
    static::$property,
    $array[$name][$sub],
)) {}

/* testUnset */
unset( $variable, $object->property, static::$property, $array[$name], );

/* testDie */
die( $status );

/* testAnonClass */
$anon = new class( $param1, $param2 ) {
    public function __construct($param1, $param2) {}
};

/* testPHP74UnpackingInLongArrayExpression */
$arr4 = array(...$arr1, ...arrGen(), ...new ArrayIterator(['a', 'b', 'c']));

/* testPHP74UnpackingInShortArrayExpression */
// Also includes code sample for PHP 8.1 unpacking with string keys.
$fruits = ['banana', ...$parts, 'watermelon', ...["a" => 2],];

/* testPHP80ClassInstantiationInAttribute1 */
#[AttributeAttachedToClosure([1, 2, 3])]
$closure = function() {};

/* testPHP80ClassInstantiationInAttribute2 */
#[MyAttribute(1, self::Foo, 'string')]
function foo() {}

#[AttributeOne, /* testPHP80ClassInstantiationInMultiAttribute */ \AttributeTwo(1, self::Foo)]
function bar() {}

/* testPHP80SkippingOverAttributes */
$result = function_call(
    $value,
    #[MyAttribute()]
    #[AnotherAttribute([1, 2, 3])]
    function() { /* do something */}
);

/* testMissingParam */
$result = function_call( $value, /* todo */, $anotherValue); // Parse error, but that's not our concern.
