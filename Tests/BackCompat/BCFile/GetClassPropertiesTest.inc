<?php

/* testNotAClass */
interface NotAClass {}

/* testAnonClass */
$anon = new class() {};

/* testEnum */
enum NotAClassEither {}

/* testClassWithoutProperties */
class ClassWithoutProperties {}

/* testAbstractClass */
abstract class AbstractClass {}

/* testFinalClass */
final class FinalClass {}

/* testWithCommentsAndNewLines */
abstract
    /* comment */
    class ClassWithCommentsAndNewLines {}

/* testWithDocblockWithoutProperties */
/**
 * Class docblock.
 *
 * @package SomePackage
 *
 * @phpcs:disable Standard.Cat.SniffName -- Just because.
 */
class ClassWithDocblock {}

/* testParseErrorAbstractFinal */
final /* comment */

    abstract // Intentional parse error, class cannot both be final and abstract.

        class AbstractFinal {}
