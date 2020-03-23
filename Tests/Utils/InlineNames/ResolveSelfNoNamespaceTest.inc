<?php

class Foo {
    const ABC = 123;

    public function bar() {
        /* testGlobalClass */
        return self::ABC;
    }
}
