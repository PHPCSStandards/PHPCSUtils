<?php

/* testInvalidTypeParseError */
// This test MUST have code containing name tokens after this code sample.
function InvalidType($param): ?\&\SomeType {}

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
