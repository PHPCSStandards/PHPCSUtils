<?php

/* testStartOfFile */

declare(strict_types=1);

/* testDocblock */

/**
 * Potentially large docblock.
 */
class IgnoreClassMethods extends Something {

    final protected const FUNCTION = 'name';

    public $property = '';

    /* method1 */
    final private function __construct() {}

    /* method2 */
    // Yes, this is valid PHP.
    public function function($param) {}

    /* testAttribute */
    #[AttributeA()]
    #[AttributeB]
    #[AttributeC]
    protected function methodA() {
        $this->function($var);
    }

    /* method4 */
    public function methodB() {
        /* testHeredoc */
        return <<<EOD
Potentially large heredoc.
EOD;
    }

    /* method5 */
    abstract protected function methodC();

    /* method6 */
    private static function methodD() {
        /* testNowdoc */
        return <<<'EOD'
Potentially large nowdoc.
EOD;
    }
}

/* function */
function globalFunction() {}

/* testEndOfFile */
