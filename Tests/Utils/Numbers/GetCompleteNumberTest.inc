<?php

/*
 * Make sure that number literals and explicit octals are correctly identified.
 */

/* testNotAnLNumber */
const _100 = null;


/*
 * OK: ordinary numeric sequences.
 */
/* testIntDecimal */
$a = 1000000000;

/* testIntLargerThanIntMaxIsFloat */
$intMaxPlus3 = 10223372036854775810;

/* testFloat */
$b = 107925284.88;

/* testFloatNegative */
$discount = -58987.789;

/* testIntBinary */
$binary = 0b1;

/* testIntHex */
$hex = 0xA;

/* testIntOctal */
$octal = 052;

/* testParseError */
$a = 100 'test'; // Intentional parse error, not our concern.


/*
 * PHP 7.4 numeric sequences with a numeric literal separator.
 */
/* testPHP74IntDecimalMultiUnderscore */
$threshold = 1_000_000_000;

/* testPHP74IntLargerThanIntMaxIsFloat */
$intMaxPlus3 = 10_223_372_036_854_775_810;

/* testPHP74Float */
$testValue = 107_925_284.88;

/* testPHP74IntDecimalSingleUnderscore */
$discount  = 135_00;

/* testPHP74FloatExponentNegative */
$a = 6.674_083e-11; // float

/* testPHP74FloatExponentPositive */
$a = 6.674_083e+11; // float

/* testPHP74IntDecimalMultiUnderscore2 */
$a = 299_792_458;   // decimal

/* testPHP74IntHex */
$a = 0xCAFE_F00D;   // hexadecimal

/* testPHP74IntBinary */
$a = 0b0101_1111;   // binary

/* testPHP74IntOctal */
$a = 0137_041;      // octal

/* testPHP74FloatExponentMultiUnderscore */
$a = 1_2.3_4e1_23;


/*
 * Make sure the backfill doesn't do more than it should.
 */
/* testPHP74IntCalc1 */
$a = 667_083 - 11; // Calculation.

/* testPHP74IntCalc2 */
$a = 74_083 + 11; // Calculation.

/* testPHP74FloatCalc1 */
$a = 6.674_08e3 - 11; // Calculation.

/* testPHP74FloatCalc2 */
$a = 6.674_08e3 + 11; // Calculation.

/* testPHP74IntWhitespace */
$testValue = 107_925_284 .88;

/* testPHP74FloatComments */
$testValue = 107_925_284/*comment*/.88;


/*
 * Invalid use of underscores in numeric sequences.
 * Each underscore in a numeric literal must be directly between two digits.
 * The below all produce "Parse error: syntax error" in PHP 7.4.
 */
/* testPHP74Invalid1 */
$a = 100_;   // trailing underscore

/* testPHP74Invalid2 */
$a = 1__1;   // next to underscore

/* testPHP74Invalid3 */
$a = 1_.0;   // next to decimal point

/* testPHP74Invalid4 */
$a = 1._0;   // next to decimal point

/* testPHP74Invalid5 */
$a = 0x_123; // next to x

/* testPHP74Invalid6 */
$a = 0b_101; // next to b

/* testPHP74Invalid7 */
$a = 1_e2;   // next to e

/* testPHP74Invalid8 */
$a = 1e_2;   // next to e

/*
 * PHP 8.1 explicit octal notation.
 */
/* testPHP81ExplicitOctal */
$octal = 0o137041;

/* testPHP81ExplicitOctalUppercase */
$octal = 0O137041;

/* testPHP81ExplicitOctalWithSeparator */
$octal = 0o137_041;

/* testPHP81InvalidExplicitOctal1 */
$invalidOctal = 0o91;

/* testPHP81InvalidExplicitOctal2 */
$invalidOctal = 0O282;

/* testPHP81InvalidExplicitOctal3 */
$invalidOctal = 0o28_2;

/* testPHP81InvalidExplicitOctal4 */
$invalidOctal = 0o2_82;

/* testPHP74PHP81InvalidExplicitOctal */
$invalid = 0o_123; // Underscore next to o.

/* testLiveCoding */
// Intentional parse error. This has to be the last test in the file (and on the last line) !
$a = 100
