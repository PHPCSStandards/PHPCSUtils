<?php

/* testClosureUse */
$closure = function() use($bar) {};

/* testUseNamePlainAliased */
use MyNamespace \ YourClass as ClassAlias;

/* testUseFunctionPlain */
use function MyNamespace\myFunction;

/* testUseConstPlain */
use const MyNamespace\MY_CONST;

/* testGroupUseMixed */
use Some\NS\ {
   ClassName,
   function SubLevel\functionName,
   const Constants\CONSTANT_NAME as SOME_CONSTANT,
   function SubLevel\AnotherName,
   AnotherLevel,
};

class Foo {
    /* testTraitUse */
    use MyNamespace\Bar;
}
