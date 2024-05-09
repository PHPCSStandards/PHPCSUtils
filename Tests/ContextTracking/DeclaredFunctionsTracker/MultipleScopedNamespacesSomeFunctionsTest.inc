<?php

namespace Scoped\FirstNS {

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
    class Baz extends Something {
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
}

namespace /* deliberately not named = global */ {

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
    trait Baz {
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
}

namespace Scoped\Third\Name {

    /* function: thirdNSfnA */
    function fnA() {}

    /* function: thirdNSfnB */
    function fnB() {
        /* function: thirdNSfnNestedInFunction */
        function fnNestedInFunction() {
            /* function: thirdNSfnDoubleNestedInFunction */
            function fnDoubleNestedInFunction() {}
        }
    }

    if (function_exists('') === false) {
        /* function: thirdNSfnConditionallyDeclared */
        function fnConditionallyDeclared() {}
    }

    /**
     * Potentially large docblock.
     */
    enum Foo: string {
        public function method() {
            /* function: thirdNSfnNestedInClassMethod */
            function fnNestedInClassMethod() {}
        }

        protected function returnAnonClass() {
            return new class {
                function method() {
                    /* function: thirdNSfnNestedInAnonClassMethod */
                    function fnNestedInAnonClassMethod() {}
                }
            };
        }

        #[AttributeA(), AttributeB, AttributeC]
        private function returnClosure() {
            return static function() {
                /* function: thirdNSfnNestedInClosure */
                function fnNestedInClosure() {}
            };
        }

        final static function returnArray() {
            return [
                'key 1' => static function() {
                    /* function: thirdNSfnNestedInClosureInShortArray */
                    function fnNestedInClosureInShortArray() {}
                },
                'key 2' => array(
                    function() {
                        /* function: thirdNSfnNestedInClosureInLongArray */
                        function fnNestedInClosureInLongArray() {}
                    },
                ),
            ]
        }
    };

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
}

namespace Scoped\No\Functions\Here {
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
}

// Intentional parse error. No code allowed outside namespace brackets in file containing scoped namespaces.
/* function: globalAfterScopedParseError */
function globalAfterScopedParseError() {}
