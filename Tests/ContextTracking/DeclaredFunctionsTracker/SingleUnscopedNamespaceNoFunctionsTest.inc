<?php

declare(strict_types=1);

namespace Unscoped\Name\NoFunctions;

use PackageA\Something;
use PackageB\SomethingElse;

include_once __DIR__ . '/filename.php';

/**
 * Potentially large docblock.
 */
class IgnoreClassMethods extends Something
{
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
$anon = new class extends SomethingElse {

    final public const FUNCTION = 'name';

    #[AttributeA(), AttributeB, AttributeC]
    public function __construct() {}

    private function methodA() {}

    private function methodB() {}

    private function methodC() {}
};

/**
 * Potentially large docblock.
 */
enum IgnoreEnumMethods : int
{
    final protected const FUNCTION = 'name';

    CASE FOO = 1;
    CASE BAR = 2;

    #[AttributeWithParam([1, 2, 3])]
    public function __construct() {}

    protected function methodA() {
        $closure = static function() {};
    }

    protected function methodB() {}

    private function methodC() {
        return <<<"EOD"
Potentially large quoted heredoc.
EOD;
    }
}
