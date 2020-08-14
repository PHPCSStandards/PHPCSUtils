<?php

namespace Some\NamespaceName\Sub {

    interface Foo {
        const ABC = 123;
        /* testNamespacedInterface */
        const DEF = self::ABC;

        public function bar();
    }
}

namespace {

    trait Foo {
        public $property = 123;

        public function bar() {
            /* testGlobalTrait */
            return self::ABC;
        }
    }
}
