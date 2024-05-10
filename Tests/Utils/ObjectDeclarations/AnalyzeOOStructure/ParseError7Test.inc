<?php

// Intentional parse error. Has to be the only test in the file.
// Missing function parentheses in abstract function.

/* testParseError */
abstract class ParseError {
    /* markerFunction1 */
    abstract public function name ;

    /* markerFunction2 */
    public function another() {}
}
