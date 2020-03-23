<?php

/* testNotSelf */
echo 'abc';

/* testSelfOutsideClassContext */
$a = self::method();

$anon = new class() {
    const ABC = 123;

    public function bar() {
        /* testAnonClass */
        return self::ABC;
    }
};
