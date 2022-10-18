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
];

/* testAllEmptyString */
$emptyStringKey = array(
    ''             => 'empty',
    null           => 'null',
    (string) false => 'false',
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
