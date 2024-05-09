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

// Intentional parse error. Namespace declaration has to be the first statement.
namespace Vendor\Package\Name;

/**
 * Potentially large docblock.
 */
class Baz {
    public function method() {
        /* function: namespacedNestedInClassMethod */
        function namespacedNestedInClassMethod() {}
    }

    protected function returnAnonClass() {
        return new class {
            function method() {
                /* function: namespacedNestedInAnonClassMethod */
                function namespacedNestedInAnonClassMethod() {}
            }
        };
    }

    #[AttributeA(), AttributeB, AttributeC]
    private function returnClosure() {
        return static function() {
            /* function: namespacedNestedInClosure */
            function namespacedNestedInClosure() {}
        };
    }

    final static function returnArray() {
        return [
            'key 1' => static function() {
                /* function: namespacedNestedInClosureInShortArray */
                function namespacedNestedInClosureInShortArray() {}
            },
            'key 2' => array(
                function() {
                    /* function: namespacedNestedInClosureInLongArray */
                    function namespacedNestedInClosureInLongArray() {}
                },
            ),
        ]
    }
}
