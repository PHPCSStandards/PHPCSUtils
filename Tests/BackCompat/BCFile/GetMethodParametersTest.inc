<?php

/* testPassByReference */
function passByReference(&$var) {}

/* testArrayHint */
function arrayHint(array $var) {}

/* testTypeHint */
function typeHint(foo $var1, bar $var2) {}

class MyClass {
    /* testSelfTypeHint */
    function typeSelfHint(self $var) {}
}

/* testNullableTypeHint */
function nullableTypeHint(?int $var1, ?\bar $var2) {}

/* testVariable */
function variable($var) {}

/* testSingleDefaultValue */
function defaultValue($var1=self::CONSTANT) {}

/* testDefaultValues */
function defaultValues($var1=1, $var2='value') {}

/* testBitwiseAndConstantExpressionDefaultValue */
function myFunction($a = 10 & 20) {}
