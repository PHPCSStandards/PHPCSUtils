<?php
/* testStartOfFile */

/* testUnscopedNamespace */
namespace Vendor\Package\Name;

/* testSingleImportUse */
use function str_replace;
/* testGroupImportUse */
use Supplier\{
    Exception,
    Some\Foo,
/* testEndOfGroupUse */};

#[AttributeClass([1, 2, 3])]
class MyClass {
    /* testInClassBeforeFirstTrackingToken */
    use MyTrait;
    /* testInClassAfterFirstTrackingToken */
}

/* testMultiImportUse */
// Yes, import use statements do not have to be at the start. This works just fine.
use const PHP_EOL, \DIRECTORY_SEPARATOR, Package\MYCONST /* testEndOfMultiUse */;

/* testClosureBeforeFirstTrackingToken */
$closure = function ($baz) use ($var) {
    /* testInClosureAfterFirstTrackingToken */
    return $var * 2;
};

/* testAfterScoped */
echo 'something';
