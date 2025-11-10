<?php

// Intentional parse error.
// This must be the only test in the file.
// Testing parse error tolerance (keywords used in an invalid context).

function invalidNotAPropertyNorParameter() {
    /* testInvalidPHP */
    #[MyAttribute]
    static $target;
}
