<?php

/* ===== Tests where the nature of a set of (nested) brackets cannot be determined by their content. ===== */

$array = [
    // Prevent going straight to outer brackets for first item.
    [$a, $b],

    /* testNestedShortArrayEmpty */
    [],

    /* testNestedShortArrayUndetermined */
    [$a['key'], $b, Sub\ClassName::$staticProp, namespace\ClassName::$staticProp, \Fully\Qualified::$staticProp,],
    /* testNestedShortArrayWithKeysUndetermined */
    ['a' => $a, 'b' => $b->prop, 'c' => self::$staticProp, 'd' => static::$staticProp, 'e' => parent::$staticProp],
    /* testNestedShortArrayWithReferenceUndetermined */
    [&Foo::$a, &$b[++$i], $c[$i . '1'], $d['key']->prop,],

    // Prevent going straight to outer brackets for last item.
    [$a, $b],
];

[
    // Prevent going straight to outer brackets for first item.
    [$a, $b],

    /* testNestedShortListEmpty */
    [],

    /* testNestedShortListUndetermined */
    [$a, $b->prop, Sub\ClassName::$staticProp, namespace\ClassName::$staticProp, $c[$i . '1'],],
    /* testNestedShortListWithKeysUndetermined */
    ['a' => $a['key'], 'b' => $b[1]['text'],],
    /* testNestedShortListWithReferenceUndetermined */
    [&$a, &Bar::$b, $d['key']->prop],

    // Prevent going straight to outer brackets for last item.
    [$a, $b],
] = $array;


// Complex variables at start of item. Undetermined for now.
$array = [
    // Prevent going straight to outer brackets for first item.
    [$a, $b],

    /* testNestedShortArrayValueHasContentAfterVar1 */
    ['arithmetic' => $c[1]['text'] + $d,],
    /* testNestedShortArrayValueHasContentAfterVar2 */
    ['concat' => $c->prop . 'text'],
    /* testNestedShortArrayValueHasContentAfterVar3 */
    ['compare' => $c->prop?->deeperProp === true,],
    /* testNestedShortArrayValueHasContentAfterVar4 */
    ['ternary' => $c[$key]::CONST_NAME ? 10 : 20],

    // Prevent going straight to outer brackets for last item.
    [$a, $b],
];


/* ===== Tests where the nature of a set of (nested) brackets CAN be determined by their content. ===== */

$array = [
    // Prevent going straight to outer brackets for first item.
    [$a, $b],

    /* testNestedShortArrayNoVarsOrNestedNull */
    [null],
    /* testNestedShortArrayNoVarsOrNestedInts */
    [1, 2, 3,],
    /* testNestedShortArrayWithKeysNoVarsOrNestedTextStrings */
    [1 => 'text', 2 => 'more text'],
    /* testNestedShortArrayWithKeysNoVarsOrNestedBools */
    [1 => true, 2 => false,],
    /* testNestedShortArrayNoVarsOrNestedFloats */
    [1.5, 3.7],
    /* testNestedShortArrayNoVarsOrNestedLongArray */
    [array(1, 2, 3),],
    /* testNestedShortArrayNoVarsOrNestedObject */
    [new stdClass()],

    /* testNestedShortArrayFuncCall */
    [$b->prop, functionCall(),],
    /* testNestedShortArrayNamespacedConstant */
    [$a, namespace\CONSTANT_NAME],
    /* testNestedShortArrayClassConstant1 */
    [$b, PartiallyQ\ClassName::CONSTANT_NAME,],
    /* testNestedShortArrayClassConstant2 */
    [$b, static::CONSTANT_NAME,],
    /* testNestedShortArrayMethodCall */
    [$b, \FullyQ\ClassName::functionCall(),],
    /* testNestedShortArrayMixedContent */
    [$a, $b, [$c], 1,],

    /* testNestedShortArrayValueHasContentAfterShortArray1 */
    ['union' => [$c] + [$d]],
    /* testNestedShortArrayValueHasContentAfterShortArray2 */
    ['compare' => [$c] === [1],],
    /* testNestedShortArrayValueHasContentAfterShortArray3 */
    ['list' => [$c] = $array,],

    /* testNestedShortArrayRecursion1 */
    [$a, $b, ['text'], $c],

    /* testNestedShortArrayRecursion2 */
    [
        $a,
        [
            $b,
            [
                10,
            ],
        ],
    ],

    /* testNestedShortArrayRecursion3 */
    [
        $a,
        [
            $b,
            [
                $c,
                [
                    10,
                ],
            ],
        ],
    ],

    // Prevent going straight to outer brackets for last item.
    [$a, $b],
];

[
    // Prevent going straight to outer brackets for first item.
    [$a, $b],

    /* testNestedShortListEmptyEntryAtStart */
    [, $a, $b],
    /* testNestedShortListEmptyEntryInMiddle */
    [$a,, $b],
    /* testNestedShortListEmptyEntryInMiddleWithComment */
    [$a, /*empty*/, $b],
    /* testNestedShortListEmptyEntryAtEnd */
    [$a, $b, , ],

    // Prevent going straight to outer brackets for last item.
    [$a, $b],
] = $array;


/* ===== Test where the nature of a set of (nested) brackets COULD be determined by their content,
         but the amount of parameters retrieved is too small. ===== */

$array = [
    // Prevent going straight to outer brackets for first item.
    [$a, $b],

    /* testNestedShortArraySampleTooSmall */
    [$a, $b, $c, [$d], $e, 10],

    // Prevent going straight to outer brackets for last item.
    [$a, $b],
];


/* ===== Tests where the nature of a set of (nested) brackets CAN be determined by their content, but the recursion limit prevent this. ===== */

$array = [
    // Prevent going straight to outer brackets for first item.
    [$a, $b],

    /* testNestedShortArrayRecursion4 */
    [
        $a,
        [
            $b,
            [
                $c,
                [
                    $d,
                    [
                        20.5,
                    ],
                ],
            ],
        ],
    ],

    /* testNestedShortArrayRecursion6 */
    [
        $a,
        [
            $b,
            [
                $c,
                [
                    $d,
                    [
                        $e,
                        [
                            $f,
                            [
                                10
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],

    // Prevent going straight to outer brackets for last item.
    [$a, $b],
];

[
    // Prevent going straight to outer brackets for first item.
    [$a, $b],

    /* testNestedShortListRecursion4 */
    [
        $a,
        [
            $b,
            [
                $c,
                [
                    $d,
                    [
                        $e,
                        $f,
                    ],
                ],
            ],
        ],
    ],

    // Prevent going straight to outer brackets for last item.
    [$a, $b],
] = $array;
