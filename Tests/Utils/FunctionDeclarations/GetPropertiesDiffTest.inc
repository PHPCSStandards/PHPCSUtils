<?php

/* testMessyPhpcsAnnotationsMethod */
trait FooTrait {
    /**
     * Method doc.
     */
    public // phpcs:disable Stnd.Cat.Sniff
        abstract // phpcs:ignore Stnd.Cat.Sniff
/*comment*/ function foo() {

        /* testMessyPhpcsAnnotationsStaticClosure */
        $closure = static // phpcs:ignore Stnd.Cat.Sniff
            function ( $foo ) {
                var_dump($foo);
            };
            $func();
    }
}
