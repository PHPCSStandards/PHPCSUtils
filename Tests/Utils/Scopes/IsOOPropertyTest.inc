<?php

/* testGlobalVar */
$var = false;

/* testFunctionParameter */
function something($param = false)
{
    /* testFunctionLocalVar */
    $var = false;
}

class MyClass {
    /* testClassPropPublic */
    public $publicProp = true;

    /* testClassPropVar */
    var $varPHP4style = true;

    /* testClassPropStaticProtected */
    static protected $staticProtected = true;

    /* testMethodParameter */
    public function something($param = false)
    {
        /* testMethodLocalVar */
        $var = false;
    }
}

$a = new class {
    /* testAnonClassPropPrivate */
    private $privateProp = true;

    /* testAnonMethodParameter */
    public function something($param = false)
    {
        /* testAnonMethodLocalVar */
        $var = false;
    }
};

interface MyInterface {
    // Intentional parse error. Properties are not allowed in interfaces.
    /* testInterfaceProp */
    public $interfaceProp = false;

    /* testInterfaceMethodParameter */
    public function something($param = false);
}

trait MyTrait {
    /* testTraitProp */
    public $traitProp = true;

    /* testTraitMethodParameter */
    function something($param = false) {}
}

// Multi-property declarations.
class MultiPropClass {
    /* testClassMultiProp1 */
    public $varA = true,
        /* testClassMultiProp2 */
        $varB = false,
        /* testClassMultiProp3 */
        $varC = 'string';
}

/* testGlobalVarObj */
$util->setLogger(
    new class {
        /* testNestedAnonClassProp */
        private $varName  = 'hello';
});

if ( has_filter( 'comments_open' ) === false ) {
    add_filter( 'comments_open', new class {
        /* testDoubleNestedAnonClassProp */
        public $year = 2017; // Ok.

        /* testDoubleNestedAnonClassMethodParameter */
        public function __construct( $open, $post_id ) {
            /* testDoubleNestedAnonClassMethodLocalVar */
            global $page;
        }
    /* testFunctionCallParameter */
    }, $priority, 2 );
}
