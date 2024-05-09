<?php

declare(strict_types=1);

namespace Unscoped\HasFunctions;

use PackageA\Something;
use PackageB\SomethingElse;

/* function: namespacedA */
function namespacedA() {}

/* function: namespacedB */
function namespacedB() {
    /* function: namespacedNestedInFunction */
    function namespacedNestedInFunction() {
        /* function: namespacedDoubleNestedInFunction */
        function namespacedDoubleNestedInFunction() {}
    }
}

if (function_exists('') === false) {
    /* function: namespacedConditionallyDeclared */
    function namespacedConditionallyDeclared() {}
}

/**
 * Potentially large docblock.
 */
trait Baz {
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
