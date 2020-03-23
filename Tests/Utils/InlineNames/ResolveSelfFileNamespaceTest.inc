<?php

namespace Some\NamespaceName\Sub;

class Foo {
    const ABC = 123;

    public function bar() {
        /* testNamespacedClass */
        return self::ABC;
    }

    public function baz() {
        return new class() {
            const ABC = 123;

            public function bar() {
                /* testNestedAnonClass */
                return self::ABC;
            }
        };
    }
}
