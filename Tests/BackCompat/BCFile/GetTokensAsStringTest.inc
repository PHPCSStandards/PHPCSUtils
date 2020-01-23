<?php

/* testNamespace */
namespace Foo\Bar\Baz;

/* testUseWithComments */
use Foo /*comment*/ \ Bar
	// phpcs:ignore Stnd.Cat.Sniff --	 For reasons.
	\ Bah;

/* testEchoWithTabs */
echo 'foo',
	'bar'	,
		'baz';

/* testEndOfFile */
echo   $foo;
