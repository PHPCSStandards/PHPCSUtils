<?php

class Foo {
    const ABC = 123;

    public function bar() {
        /* testGlobalClass */
        return self::ABC;
    }

    public static function getInstance() {
        /* testNewSelfReturnPHPCS1245 */
        return new self;
    }
}
