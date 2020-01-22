<?php

/* testPHPCSAnnotations */
final
    // phpcs:ignore Standard.Cat.SniffName -- Just because.
    class PHPCSAnnotation {}

/* testWithDocblockWithWeirdlyPlacedProperty */
final

/**
 * Class docblock.
 *
 * @package SomePackage
 *
 * @phpcs:disable Standard.Cat.SniffName -- Just because.
 */
class ClassWithPropertyBeforeDocblock {}

/* testParseErrorAbstractFinal */
final /* comment */

    abstract // Intentional parse error, class cannot both be final and abstract.

        class AbstractFinal {}
