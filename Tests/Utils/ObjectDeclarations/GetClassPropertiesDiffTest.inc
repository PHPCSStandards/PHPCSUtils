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

/* testReadonlyClass */
readonly class ReadOnlyClass {}

/* testFinalReadonlyClass */
final readonly class FinalReadOnlyClass extends Foo {}

/* testReadonlyFinalClass */
readonly /*comment*/ final class ReadOnlyFinalClass {}

/* testAbstractReadonlyClass */
abstract readonly class AbstractReadOnlyClass {}

/* testReadonlyAbstractClass */
readonly
abstract
class ReadOnlyAbstractClass {}
