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

/* testPHP82PseudoTypeTrue */
function pseudoTypeTrue(): ?true {}

/* testPHP82PseudoTypeFalseAndTrue */
// Intentional fatal error - Type contains both true and false, bool should be used instead, but that's not the concern of the method.
function pseudoTypeFalseAndTrue(): true|false {}
