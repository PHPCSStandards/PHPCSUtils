<?php

declare(strict_types=1);

namespace Unscoped\FirstNamespace;

use PackageA\Something;
use PackageB\SomethingElse;

/* function: firstNSfnA */
function fnA() {}

/* function: firstNSfnB */
function fnB() {
    /* function: firstNSfnNestedInFunction */
    function fnNestedInFunction() {
        /* function: firstNSfnDoubleNestedInFunction */
        function fnDoubleNestedInFunction() {}
    }
}

if (function_exists('') === false) {
    /* function: firstNSfnConditionallyDeclared */
    function fnConditionallyDeclared() {}
}

/**
 * Potentially large docblock.
 */
enum Baz {
    public function method() {
        /* function: firstNSfnNestedInClassMethod */
        function fnNestedInClassMethod() {}
    }

    protected function returnAnonClass() {
        return new class {
            function method() {
                /* function: firstNSfnNestedInAnonClassMethod */
                function fnNestedInAnonClassMethod() {}
            }
        };
    }

    #[AttributeA(), AttributeB, AttributeC]
    private function returnClosure() {
        return static function() {
            /* function: firstNSfnNestedInClosure */
            function fnNestedInClosure() {}
        };
    }

    final static function returnArray() {
        return [
            'key 1' => static function() {
                /* function: firstNSfnNestedInClosureInShortArray */
                function fnNestedInClosureInShortArray() {}
            },
            'key 2' => array(
                function() {
                    /* function: firstNSfnNestedInClosureInLongArray */
                    function fnNestedInClosureInLongArray() {}
                },
            ),
        ]
    }
}

namespace Unscoped\Second\Name;

/* function: secondNSfnC */
function fnC() {}

/* function: secondNSfnD */
function fnD() {
    /* function: secondNSfnNestedInFunction */
    function fnNestedInFunction() {
        /* function: secondNSfnDoubleNestedInFunction */
        function fnDoubleNestedInFunction() {}
    }
}

if (function_exists('') === false) {
    /* function: secondNSfnConditionallyDeclared */
    function fnConditionallyDeclared() {}
}

/**
 * Potentially large docblock.
 */
$anon = new class {
    public function method() {
        /* function: secondNSfnNestedInClassMethod */
        function fnNestedInClassMethod() {}
    }

    protected function returnAnonClass() {
        return new class {
            function method() {
                /* function: secondNSfnNestedInAnonClassMethod */
                function fnNestedInAnonClassMethod() {}
            }
        };
    }

    #[AttributeA(), AttributeB, AttributeC]
    private function returnClosure() {
        return static function() {
            /* function: secondNSfnNestedInClosure */
            function fnNestedInClosure() {}
        };
    }

    final static function returnArray() {
        return [
            'key 1' => static function() {
                /* function: secondNSfnNestedInClosureInShortArray */
                function fnNestedInClosureInShortArray() {}
            },
            'key 2' => array(
                function() {
                    /* function: secondNSfnNestedInClosureInLongArray */
                    function fnNestedInClosureInLongArray() {}
                },
            ),
        ]
    }
};

namespace Unscoped\No\Functions\Here;

/**
 * Potentially large docblock.
 */
class IgnoreClassMethods extends Something {

    final protected const FUNCTION = 'name';

    public $property = '';

    final private function __construct() {}

    // Yes, this is valid PHP.
    public function function($param) {}

    #[AttributeA()]
    #[AttributeB]
    #[AttributeC]
    protected function methodA() {
        $this->function($var);
    }

    public function methodB() {
        return <<<EOD
Potentially large heredoc.
EOD;
    }

    private static function methodC() {
        return <<<'EOD'
Potentially large nowdoc.
EOD;
    }
}

/**
 * Potentially large docblock.
 */
interface IgnoreInterfaceMethods extends SomethingElse {

    final public const FUNCTION = 'name';

    #[AttributeA(), AttributeB, AttributeC]
    public function __construct();

    public function methodA();

    public function methodB();

    public function methodC();
}

/**
 * Potentially large docblock.
 */
trait IgnoreTraitMethods {

    final protected const FUNCTION = 'name';

    public $property = '';

    #[AttributeWithParam([1, 2, 3])]
    public function __construct() {}

    protected function methodA() {
        $closure = static function() {};
    }

    abstract protected function methodB();

    private function methodC() {
        return <<<"EOD"
Potentially large quoted heredoc.
EOD;
    }
}
