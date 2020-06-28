<?php

/* testHeredoc */
$fn1 = fn() => <<<HTML
fn
HTML;

/* testNowdoc */
$fn1 = fn() => <<<'HTML'
fn
HTML;
