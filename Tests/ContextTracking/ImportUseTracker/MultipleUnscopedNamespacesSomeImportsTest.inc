<?php
/* testStartOfFile */

/* testNamespaceDeclarationA */
namespace Vendor\Package\NameA;

/* testGroupImportUse */
use Grouping\{
    Package,
    Some\Thing,
};
/* testSingleImportUse */
use function get_version;

class Foo {
    /* testInClassBeforeFirstTrackingToken */
    use MyTrait;
    /* testInClassAfterFirstTrackingToken */
}

/* testNamespaceDeclarationB */
namespace SecondNamespace;

/* testClosureBeforeFirstTrackingToken */
$closure = function ($baz) use ($var) {
    /* testInClosureAfterFirstTrackingToken */
    return $var * 2;
};

/* testNamespaceDeclarationC */
namespace Package\ThirdNamespace;

/* testMultiImportUse */
use const \M_PI, PHP_ROUND_HALF_DOWN, Package\NAN ?>
<?php

/* testAfterScoped */
echo 'something';
