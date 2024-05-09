<?php

/* function: globalA */
function globalA() {}

/* function: globalB */
function globalB() {
    /* function: globalNestedInFunction */
    function globalNestedInFunction() {
        /* function: globalDoubleNestedInFunction */
        function globalDoubleNestedInFunction() {}
    }
}

if (function_exists('') === false) {
    /* function: globalConditionallyDeclared */
    Function globalConditionallyDeclared() {}
}

/**
 * Potentially large docblock.
 */
class Baz {
    public function method() {
        /* function: globalNestedInClassMethod */
        function globalNestedInClassMethod() {}
    }

    protected function returnAnonClass() {
        return new class {
            function method() {
                /* function: globalNestedInAnonClassMethod */
                function globalNestedInAnonClassMethod() {}
            }
        };
    }

    #[AttributeA(), AttributeB, AttributeC]
    private function returnClosure() {
        return static function() {
            /* function: globalNestedInClosure */
            FUNCTION globalNestedInClosure() {}
        };
    }

    final static function returnArray() {
        return [
            'key 1' => static function() {
                /* function: globalNestedInClosureInShortArray */
                function globalNestedInClosureInShortArray() {}
            },
            'key 2' => array(
                function() {
                    /* function: globalNestedInClosureInLongArray */
                    function globalNestedInClosureInLongArray() {}
                },
            ),
        ]
    }
}
