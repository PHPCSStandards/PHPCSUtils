<?php

/* testPHPCSAnnotations */
final
    // phpcs:ignore Standard.Cat.SniffName -- Just because.
    class PHPCSAnnotation {}

/* testWithDocblockWithWeirdlyPlacedModifier */
final

/**
 * Class docblock.
 *
 * @package SomePackage
 *
 * @phpcs:disable Standard.Cat.SniffName -- Just because.
 */
class ClassWithModifierBeforeDocblock {}
