<?php

/* testAllVoid */
$excluded = [
    $var                        => 'excluded',
    MY_CONSTANT                 => 'excluded',
    PHP_INT_MAX                 => 'excluded',
    str_replace('.', '', '1.1') => 'excluded',
    self::CONSTANT              => 'excluded',
    $obj->get_key()             => 'excluded',
    $obj->prop                  => 'excluded',
    "my $var text"              => 'excluded',
    \some\ true\name            => 'excluded',
    \false\some\name            => 'excluded',
    \some\name\null             => 'excluded',
    <<<EOD
my $var text
EOD
                                => 'excluded',
    $var['key']{1}              => 'excluded',
    <<<EOD
my complex {${getName()}} text
EOD
                                => 'excluded',
    <<<EOD
$var text
EOD
                                => 'excluded',
    (unset) false               => 'excluded', // Type cast deprecated per PHP 7.2, removed 8.0.
];

/* testAllEmptyString */
$emptyStringKey = array(
    ''             => 'empty',
    null           => 'null',
    (string) false => 'false',
    \null          => 'null',
    (binary) false => 'binary false', // Type cast deprecated per PHP 8.5.
);

/* testAllZero */
$everythingZero = [
    '0',
    0                => 'a',
    0.0              => 'b',
    '0'              => 'c',
    0b0              => 'd',
    0x0              => 'e',
    00               => 'f',
    false            => 'g',
    0.4              => 'h',
    -0.8             => 'i',
    0e0              => 'j',
    0_0              => 'k',
    -1 + 1           => 'l',
    3 * 0            => 'm',
    00.00            => 'n',
    (int) 'nothing'  => 'o',
    15 > 200         => 'p',
    "0"              => 'q',
    0.               => 'r',
    .0               => 's',
    (true) ? 0 : 1   => 't',
    ! true           => 'u',
    \false           => 'v',
    (integer) ''     => 'w', // Type cast deprecated per PHP 8.5.
    (float) 0.14     => 'x',
    (real) 0.2       => 'y', // Type cast deprecated per PHP 7.4, removed 8.0.
    (double) 0.005   => 'z', // Type cast deprecated per PHP 8.5.
];

/* testAllOne */
$everythingOne = [
    '0',
    '1',
    1                => 'a',
    1.1              => 'b',
    '1'              => 'c',
    0b1              => 'd',
    0x1              => 'e',
    01               => 'f',
    true             => 'g',
    1.2 /*comment*/  => 'h',
    1e0              => 'i',
    0_1              => 'j',
    -1 + 2           => 'k',
    3 * 0.5          => 'l',
    01.00            => 'm',
    (int) '1 penny'  => 'n',
    15 < 200         => 'o',
    "1"              => 'p',
    1.               => 'q',
    001.             => 'r',
    (true) ? 1 : 0   => 's',
    ! false          => 't',
    (string) true    => 'u',
    \true            => 'v',
    (boolean) 1      => 'w',  // Type cast deprecated per PHP 8.5.
    (binary) true    => 'x',
    (float) 1.14     => 'y',
    (real) 1.2       => 'z',  // Type cast deprecated per PHP 7.4, removed 8.0.
    (double) 1.005   => 'aa', // Type cast deprecated per PHP 8.5.
];

/* testAllEleven */
$everythingEleven = [
    11                 => 'a',
    11.0               => 'b',
    '11'               => 'c',
    0b1011             => 'd',
    0Xb                => 'e',
    013                => 'f',
    11.8               => 'g',
    1.1e1              => 'h',
    1_1                => 'i',
    0_13               => 'j',
    -1 + 12            => 'k',
    22 / /*comment*/ 2 => 'l',
    0011.0011          => 'm',
    (int) '11 lane'    => 'n',
    "11"               => 'o',
    11.                => 'p',
    35 % 12            => 'q',
    (integer) '11'     => 'r', // Type cast deprecated per PHP 8.5.
];

/* testAllStringAbc */
$textualStringKeyVariations = [
    'abc'      => 1,
    'ab' . 'c' => 4,
    <<<EOT
abc
EOT
               => 5,
    <<< 'NOW'
abc
NOW
               => 6,
    "abc"      => 7,
];

/* testHeredocWithEscapedVarInKey */
$heredocStringKeyWithEscapedVar = [
    <<<EOD
a{\$b}c
EOD
              => 1,
    <<<EOD
a\$bc
EOD
              => 2,
    <<<EOD
$\{abc}
EOD
              => 3,
];

/* testStringLiteralsWithNumbers */
$validStringKeys = array(
    '_1'  => 'value1',
    '_2'  => 'value2',
    '3_'  => 'value3',
    '4_'  => 'value4',
    '_5_' => 'value5',
    '_6_' => 'value6',
);

/* testZeroPrefixedNumericStringKeys */
$validNumericStringKeys = array(
    '01'  => 'value1',
    '002' => 'value2',
    '0o3' => 'value3',
    '0b1' => 'value4',
    '0x7' => 'value5',
    '0.0' => 'value6',
);
