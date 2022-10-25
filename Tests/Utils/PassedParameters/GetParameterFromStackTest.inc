<?php

/* testNoParams */
setcookie();

/* testIsset */
isset( $var1, $var2, $var3 );

/* testArray */
$array = array( $var1, $var2, $var3 );

/* testAllParamsPositional */
setcookie( 'name', 'value', time() + (60 * 60 * 24), '/path/', 'sub.example.com', true, false );

/* testAllParamsNamedStandardOrder */
setcookie(
    name: 'name',
    value: 'value',
    expires_or_options: time() + (60 * 60 * 24),
    path: '/path/',
    domain: 'sub.example.com',
    secure: true,
    httponly: false
);

/* testAllParamsNamedNonStandardOrder */
setcookie(
    expires_or_options: time() + (60 * 60 * 24),
    secure: true,
    name: 'name',
    domain: 'sub.example.com',
    value: 'value',
    httponly: false,
    path: '/path/',
);

/* testMixedPositionalAndNamedParams */
setcookie(
    'name',
    'value',
    time() + (60 * 60 * 24),
    secure: true,
    path: '/path/',
    httponly: false,
    domain: 'sub.example.com',
);

/* testMixedPositionalAndNamedParamsNotAllOptionalSet */
setcookie(
    'name',
    expires_or_options: time() + (60 * 60 * 24),
    secure: true,
);

/* testMixedPositionalAndNamedParamsOldName */
setcookie(
    'name',
    expires: time() + (60 * 60 * 24),
);

/* testPHP81NamedParamAfterVariadic */
// Prior to PHP 8.1, this was a compile error, but this is now supported.
test($positional, ...$variadic, namedA: $valueA, namedB: $valueB);
