<?php

declare(strict_types=1);

include_once __DIR__ . '/filename.php';

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
    #[\AttributeB]
    #[namespace\AttributeC]
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

    #[AttributeA(), \Fully\Qualified\AttributeB, AttributeC]
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

    #[Partially\Qualified\AttributeWithParam([1, 2, 3])]
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
