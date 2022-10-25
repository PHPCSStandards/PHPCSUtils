<?php

/* testTokenizerIssue1971PHPCSlt330gt271A */
// This has to be the first test in the file!
[$a, /* testTokenizerIssue1971PHPCSlt330gt271B */ [$b]] = $array;

/* testTokenizerIssue1381PHPCSlt290A1 */
echo [ 1,2,3 ] /* testTokenizerIssue1381PHPCSlt290A2 */ [0];

/* testTokenizerIssue1381PHPCSlt290B */
echo 'PHP'[0];

/* testTokenizerIssue1381PHPCSlt290C */
echo $this->addedCustomFunctions['nonce'];

/* testTokenizerIssue1381PHPCSlt290D1 */
echo $this->deprecated_functions[ $function_name ]/* testTokenizerIssue1381PHPCSlt290D2 */['version'];

/* testTokenizerIssue1284PHPCSlt280A */
if ($foo) {}
[$a, $b] = $c;

/* testTokenizerIssue1284PHPCSlt280B */
if ($foo) {}
[$a, $b];

/* testTokenizerIssue1284PHPCSlt290C */
$foo = ${$bar}['key'];

/* testTokenizerIssue1284PHPCSlt280D */
$c->{$var}[ ] = 2;

/* testTokenizerIssue3013PHPCSlt356 */
$var = __FILE__[0];

/* testTokenizerIssuePHPCS28xA */
__NAMESPACE__[] = 'x';

/* testTokenizerIssuePHPCS28xB */
__method__[0]/* testTokenizerIssuePHPCS28xC */[1]/* testTokenizerIssuePHPCS28xD */[2] *= 'x';

/* testTokenizerIssue3172PHPCSlt360A */
$var = "PHP{$rocks}"[1]/* testTokenizerIssue3172PHPCSlt360B */[0];

if ( true )
    /* testTokenizerIssue3632PHPCSlt372 */
    [ $a ] = [ 'hi' ];

return $a ?? '';
