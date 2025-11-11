<?php

// Intentional parse error.
// This must be the only test in the file.
// Testing parse error tolerance (invalid multi-property declaration).

class LiveCoding {
    #[MyAttribute]
    public int $, $prop2, $prop3 = 10 * self::CONSTANT, /* testParseError */ $prop4;
}
