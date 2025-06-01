<?php

/* testUseImport1 */
use Imported\ClassName;

/* testUseImport2 */
use function Imported\functionName;

/* testUseImport3 */
use const Imported\CONSTANT_NAME;

namespace Some\NS {
    /* testUseImport4 */
    use Imported\AnotherClass;
}

/* testClosureUse */
$closure = function($param) use ($var) {};

class ClassUsingTrait {
    /* testUseTrait */
    use SomeTrait;

    public function functionName() {
        /* testClosureUseNestedInClass */
        $closure = function($param) use ($var) {};

        $anon_class = new class($param) {
            /* testUseTraitInNestedAnonClass */
            use SomeOtherTrait;
        };
    }
}

trait TraitUsingTrait {
    /* testUseTraitInTrait */
    use SomeTrait;

    public function functionName() {
        /* testClosureUseNestedInTrait */
        $closure = function($param) use ($var) {};
    }
}

// Intentional parse error. Interfaces can not use traits.
interface InterfaceUsingTrait {
    /* testUseTraitInInterface */
    use SomeTrait;
}

/*
 * Test specific issue with PHPCS < 3 where the case/default parse errors below would cause them
 * to have a case/default condition, though without a switch.
 */
use FoobarA as Case;
/* testUseImportPHPCS2CaseNoSwitchA */
use FoobarB as Foo;
/* testUseImportPHPCS2CaseNoSwitchB */
use FoobarC as Default;
/* testUseImportPHPCS2DefaultNoSwitchA */
use FoobarD as Bar;

class PHPCS2ScopeConditionIssue {
    use TraitA { oldfunction as case; }
    /* testUseImportPHPCS2CaseNoSwitchC */
    use TraitB { oldfunction as Baz; }
    /* testUseImportPHPCS2CaseNoSwitchD */
    use TraitC { oldfunction as default; }
    /* testUseImportPHPCS2DefaultNoSwitchB */
    use TraitD { oldfunction as Fool; }
}
