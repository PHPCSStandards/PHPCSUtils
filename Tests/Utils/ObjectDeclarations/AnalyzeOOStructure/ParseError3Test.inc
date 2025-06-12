<?php

// Intentional parse error. Has to be the only test in the file.
// Missing constant name.

/* testParseError */
enum ParseError {
    public const = 'name';
}
