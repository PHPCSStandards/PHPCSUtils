<?php

/* testNotAClass */
interface NotAClass {}

/* testAnonClass */
$anon = new class() {};

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
