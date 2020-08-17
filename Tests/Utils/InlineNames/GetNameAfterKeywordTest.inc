<?php

/* testHierarchyKeywordNotFirstAndOnlyParent */
$a = $b instanceof My\static;

/* testHierarchyKeywordNotFirstAndOnlyStatic */
$a = $b instanceof static::$property;

/* testNonAnonClassUsingReservedClassKeyword */
$obj = new class\instance;

/* testNamespaceOperatorNotFirstExtends */
class Foo extends My\Namespace\ClassName {}

/* testNamespaceOperatorNotFirstArrayAccess */
$obj = new SOME_CONSTANT[namespace\OTHER_CONSTANT]();

/* testParseError1 */
$a = $b instanceof My ClassName;

/* testParseError2 */
$a = $b instanceof My\\ClassName;

/* testParseError3 */
$a = $b instanceof self/*comment*/ClassName;

/* testParseError4 */
$a = $b instanceof namespace ClassName;

/* testLiveCoding */
$a = $b instanceof ClassName
