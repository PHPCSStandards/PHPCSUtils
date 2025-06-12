<?php

// Intentional parse error. Has to be the only test in the file.
// Missing enum case name.

/* testParseError */
enum ParseError: string {
    case  = 'name';
}
