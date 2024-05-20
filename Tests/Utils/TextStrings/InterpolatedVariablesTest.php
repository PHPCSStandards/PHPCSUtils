<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\TextStrings;

use PHPCSUtils\Internal\NoFileCache;
use PHPCSUtils\Utils\TextStrings;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHPCSUtils\Utils\TextStrings::get/stripEmbeds() methods.
 *
 * @covers \PHPCSUtils\Utils\TextStrings::getEmbeds
 * @covers \PHPCSUtils\Utils\TextStrings::stripEmbeds
 * @covers \PHPCSUtils\Utils\TextStrings::getStripEmbeds
 *
 * @since 1.0.0
 */
final class InterpolatedVariablesTest extends TestCase
{

    /**
     * Collection of various variables and other embeds which are valid in double quoted strings.
     *
     * @var array<string>
     */
    private static $embeds = [
        // Simple.
        '$foo',
        '{$foo}',
        '${foo}',

        // DIM.
        '$foo[2]',
        '$foo[-12]',
        '{$foo[0]}',
        '${foo[132]}',
        '$foo[bar]',
        '{$foo[\'bar\']}',
        '${foo[\'bar\']}',
        '{$foo[8][35]}',
        '{$foo[10][\'bar\']}',
        '{$foo[\'bar\'][\'baz\']}',
        '{$foo[\'bar\'][12]}',

        // Property.
        '$foo->bar',
        '{$foo->bar}',
        '$foo?->bar',
        '{$foo?->bar}',
        '{${beers::$ale}}',
        '${beers::$ale}',

        // Class constant.
        '{${beers::softdrink}}',
        '${beers::softdrink}',

        // Method.
        '{$foo->bar()}',
        '{$foo?->bar()}',
        '{${$object->getName()}}',
        '{${$object?->getName()}}',

        // Closure/Function call.
        '{$foo()}',
        '{${getName()}}',
        '{${getName( $test )}}',
        '{${getName( \'abc\' )}}',
        '${substr(\'laruence\', 0, 2)}',

        // Chain.
        '{$foo[42]->baz()()}',
        '{$foo[\'bar\']->baz()()}',
        '{$foo[42]?->baz()()}',
        '{$foo[\'bar\']?->baz()()}',
        '{$obj->values[3]->name}',
        '{$obj->values[5]?->name}',

        // Variable variables.
        '${$bar}',
        '{$$bar}',
        '${(foo)}',
        '${foo->bar}',
        '{$foo->$bar}',
        '{$foo?->$bar}',

        // Nested.
        '${foo["${bar}"]}',
        '${foo["${ba23}"]}',
        '${foo["${bar[3]}"]}',
        '${foo["${bar[\'baz\']}"]}',
        '${foo->{$baz}}',
        '${foo->{${\'a\'}}}',
        '${foo->{"${\'a\'}"}}',
        '${foo?->{$baz}}',
        '${foo?->{${\'a\'}}}',
        '${foo?->{"${\'a\'}"}}',
        '{$foo->{$baz[1]}}',

        // Using non-ascii UTF8 variable names.
        '$IÑTËRNÂTÎÔNÀLÍŽÆTIØN',
        '${IÑTËRNÂTÎÔNÀLÍŽÆTIØN}',
        '$Iñtërnâtîônàlížætiøn[nât]',
        '$Iñtërnâtîônàlížætiøn?->îôn',
        '$МояРабота',
        '${$МояРабота}',
        '$💟',
        '$💟[◾]',
        '$😝->🤞',
    ];

    /**
     * Collections of phrases to use during the test.
     *
     * Phrases used will be selected at random.
     *
     * @var array<string, string>
     */
    private static $phrases = [
        'single line'                                => "%s this is nonsense %s\tbut that's not the point %s",
        'single line, embed followed by non-space 1' => '%s- dash %s+ plus %s',
        'single line, embed followed by non-space 2' => '%s. dash %s= plus %s',
        'single line, embed followed by non-space 3' => '%s` dash %s%% plus %s',
        'single line, embed followed by non-space 4' => '%s\\ dash %s) plus %s',
        'single line, embed followed by non-space 5' => '%s] dash %s} plus %s',
        'single line, embed followed by non-space 6' => '%s\' dash %s# plus %s',
        'single line, contains escaped non-embed 1'  => '%s this {\$name} foo %s but that\'s \$mane[not] the point %s',
        'single line, contains escaped non-embed 2'  => '%s this $\{name} foo %s but that\'s \$mane->not the point %s',
        'multi line'                                 => "%s this is\nnonsense %s but\nthat's not the point %s",
        'multi line, empty first line'               => "\n%s this is\nnonsense %s but\nthat's not the point %s",
        'multi line, empty last line'                => "%s this is\nnonsense %s but\nthat's not the point %s\n",
    ];

    /**
     * Test getting embedded variables and expressions from an arbitrary text string.
     *
     * @dataProvider dataEmbedsInPhrases
     *
     * @param string                                   $input    The input string.
     * @param array<string, string|array<int, string>> $expected The expected function output of the respective functions.
     *
     * @return void
     */
    public function testGetEmbeds($input, $expected)
    {
        $this->assertSame($expected['get'], \array_values(TextStrings::getEmbeds($input)));
    }

    /**
     * Test getting embedded variables and expressions from an arbitrary text string and verify the offset
     * at which the embed was found is correctly set as well.
     *
     * @dataProvider dataEscaping
     * @dataProvider dataSpecificCases
     *
     * @param string                                   $input    The input string.
     * @param array<string, string|array<int, string>> $expected The expected function output of the respective functions.
     *
     * @return void
     */
    public function testGetEmbedsAndCheckOffset($input, $expected)
    {
        $this->assertSame($expected['get'], TextStrings::getEmbeds($input));
    }

    /**
     * Test stripping embedded variables and expressions from an arbitrary text string.
     *
     * @dataProvider dataEmbedsInPhrases
     * @dataProvider dataEscaping
     * @dataProvider dataSpecificCases
     *
     * @param string                                   $input    The input string.
     * @param array<string, string|array<int, string>> $expected The expected function output of the respective functions.
     *
     * @return void
     */
    public function testStripEmbeds($input, $expected)
    {
        $this->assertSame($expected['stripped'], TextStrings::stripEmbeds($input));
    }

    /**
     * Data provider.
     *
     * @see testGetEmbeds()   For the array format.
     * @see testStripEmbeds() For the array format.
     *
     * @return array<string, array<string, string|array<string, string|array<int, string>>>>
     */
    public static function dataEmbedsInPhrases()
    {
        $data = [];
        foreach (self::$embeds as $embed) {
            $data[$embed . '| Plain embed (heredoc)'] = [
                'input'    => $embed,
                'expected' => [
                    'get'      => [$embed],
                    'stripped' => '',
                ],
            ];
            $data[$embed . '| Double quoted embed']   =  [
                'input'    => '"' . $embed . '"',
                'expected' => [
                    'get'      => [$embed],
                    'stripped' => '""',
                ],
            ];

            // Plain, no double quotes (heredoc).
            $phraseKey      = \array_rand(self::$phrases);
            $dataKey        = $embed . '| Embed at start of plain phrase in: ' . $phraseKey;
            $data[$dataKey] =  [
                'input'    => \sprintf(self::$phrases[$phraseKey], $embed, '', ''),
                'expected' => [
                    'get'      => [$embed],
                    'stripped' => \sprintf(self::$phrases[$phraseKey], '', '', ''),
                ],
            ];

            $phraseKey      = \array_rand(self::$phrases);
            $dataKey        = $embed . '| Embed in middle of plain phrase in: ' . $phraseKey;
            $data[$dataKey] =  [
                'input'    => \sprintf(self::$phrases[$phraseKey], '', $embed, ''),
                'expected' => [
                    'get'      => [$embed],
                    'stripped' => \sprintf(self::$phrases[$phraseKey], '', '', ''),
                ],
            ];

            $phraseKey      = \array_rand(self::$phrases);
            $dataKey        = $embed . '| Embed at end of plain phrase in: ' . $phraseKey;
            $data[$dataKey] =  [
                'input'    => \sprintf(self::$phrases[$phraseKey], '', '', $embed),
                'expected' => [
                    'get'      => [$embed],
                    'stripped' => \sprintf(self::$phrases[$phraseKey], '', '', ''),
                ],
            ];

            // Phrase in double quotes.
            $phraseKey      = \array_rand(self::$phrases);
            $dataKey        = $embed . '| Embed at start of quoted phrase in: ' . $phraseKey;
            $data[$dataKey] =  [
                'input'    => '"' . \sprintf(self::$phrases[$phraseKey], $embed, '', '') . '"',
                'expected' => [
                    'get'      => [$embed],
                    'stripped' => '"' . \sprintf(self::$phrases[$phraseKey], '', '', '') . '"',
                ],
            ];

            $phraseKey      = \array_rand(self::$phrases);
            $dataKey        = $embed . '| Embed in middle of quoted phrase in: ' . $phraseKey;
            $data[$dataKey] =  [
                'input'    => '"' . \sprintf(self::$phrases[$phraseKey], '', $embed, '') . '"',
                'expected' => [
                    'get'      => [$embed],
                    'stripped' => '"' . \sprintf(self::$phrases[$phraseKey], '', '', '') . '"',
                ],
            ];

            $phraseKey      = \array_rand(self::$phrases);
            $dataKey        = $embed . '| Embed at end of quoted phrase in: ' . $phraseKey;
            $data[$dataKey] =  [
                'input'    => '"' . \sprintf(self::$phrases[$phraseKey], '', '', $embed) . '"',
                'expected' => [
                    'get'      => [$embed],
                    'stripped' => '"' . \sprintf(self::$phrases[$phraseKey], '', '', '') . '"',
                ],
            ];
        }

        return $data;
    }

    /**
     * Data provider.
     *
     * @see testGetEmbedsAndCheckOffset() For the array format.
     * @see testStripEmbeds()             For the array format.
     *
     * @return array<string, array<string, string|array<string, string|array<int, string>>>>
     */
    public static function dataEscaping()
    {
        $embedAtEnd   = '"Foo: %s%s"';
        $embedAtStart = '%s%s Foo'; // Not, no double quotes!
        $data         = [];

        for ($i = 0; $i < 10; $i++) {
            $escaped = (($i % 2) !== 0);
            $slashes = \str_repeat('\\', $i);
            $offset  = 6 + $i;

            $dataKey        = "Escaping handling test, embed at start: slashes before \$ - $i slashes = ";
            $dataKey       .= ($escaped === true) ? 'escaped' : 'not escaped';
            $data[$dataKey] = [
                'input'    => \sprintf($embedAtStart, $slashes, '$foo'),
                'expected' => [
                    'get'      => ($escaped === true) ? [] : [$i => '$foo'],
                    'stripped' => ($escaped === true)
                        ? \sprintf($embedAtStart, $slashes, '$foo')
                        : \sprintf($embedAtStart, $slashes, ''),
                ],
            ];

            $dataKey        = "Escaping handling test, embed at start: slashes before { - $i slashes = ";
            $dataKey       .= ($escaped === true) ? 'escaped' : 'not escaped';
            $data[$dataKey] = [
                'input'    => \sprintf($embedAtStart, $slashes, '{$foo}'),
                'expected' => [
                    'get'      => ($escaped === true) ? [($i + 1) => '$foo'] : [$i => '{$foo}'],
                    'stripped' => ($escaped === true)
                        ? \sprintf($embedAtStart, $slashes, '{}')
                        : \sprintf($embedAtStart, $slashes, ''),
                ],
            ];

            $dataKey        = "Escaping handling test, embed at end: slashes before \$ - $i slashes = ";
            $dataKey       .= ($escaped === true) ? 'escaped' : 'not escaped';
            $data[$dataKey] = [
                'input'    => \sprintf($embedAtEnd, $slashes, '$foo'),
                'expected' => [
                    'get'      => ($escaped === true) ? [] : [$offset => '$foo'],
                    'stripped' => ($escaped === true)
                        ? \sprintf($embedAtEnd, $slashes, '$foo')
                        : \sprintf($embedAtEnd, $slashes, ''),
                ],
            ];

            $dataKey        = "Escaping handling test, embed at end: slashes before { - $i slashes = ";
            $dataKey       .= ($escaped === true) ? 'escaped' : 'not escaped';
            $data[$dataKey] = [
                'input'    => \sprintf($embedAtEnd, $slashes, '{$foo}'),
                'expected' => [
                    'get'      => ($escaped === true) ? [($offset + 1) => '$foo'] : [$offset => '{$foo}'],
                    'stripped' => ($escaped === true)
                        ? \sprintf($embedAtEnd, $slashes, '{}')
                        : \sprintf($embedAtEnd, $slashes, ''),
                ],
            ];
        }

        return $data;
    }

    /**
     * Data provider.
     *
     * @see testGetEmbedsAndCheckOffset() For the array format.
     * @see testStripEmbeds()             For the array format.
     *
     * @return array<string, array<string, string|array<string, string|array<int, string>>>>
     */
    public static function dataSpecificCases()
    {
        return [
            // No embeds.
            'Text string without any embeds' => [
                'input'    => '"He drank some orange juice."',
                'expected' => [
                    'get'      => [],
                    'stripped' => '"He drank some orange juice."',
                ],
            ],
            'Text string without any valid embeds - not a valid variable name 1' => [
                'input'    => '"He drank some orange $--."',
                'expected' => [
                    'get'      => [],
                    'stripped' => '"He drank some orange $--."',
                ],
            ],
            'Text string without any valid embeds - not a valid variable name 2' => [
                'input'    => '"He drank some orange $\name."',
                'expected' => [
                    'get'      => [],
                    'stripped' => '"He drank some orange $\name."',
                ],
            ],

            // Variations on embeds not tested via the above generated test cases.
            'No braces, one character variable name' => [
                'input'    => '"This is $g"',
                'expected' => [
                    'get'      => [
                        9 => '$g',
                    ],
                    'stripped' => '"This is "',
                ],
            ],
            'Wrappped in outer braces with space between brace and dollar' => [
                'input'    => '"This is { $great}"',
                'expected' => [
                    'get'      => [
                        11 => '$great',
                    ],
                    'stripped' => '"This is { }"',
                ],
            ],

            'Text string containing multiple embeds 1' => [
                'input'    => '"$people->john drank some $juices[0] juice."',
                'expected' => [
                    'get'      => [
                        1  => '$people->john',
                        26 => '$juices[0]',
                    ],
                    'stripped' => '" drank some  juice."',
                ],
            ],
            'Text string containing multiple embeds 2' => [
                'input'    => '"$people->john then said hello to $people->jane."',
                'expected' => [
                    'get'      => [
                        1  => '$people->john',
                        34 => '$people->jane',
                    ],
                    'stripped' => '" then said hello to ."',
                ],
            ],
            'Text string containing multiple embeds 3' => [
                'input'    => '"$people->john\'s wife greeted $people->robert."',
                'expected' => [
                    'get'      => [
                        1  => '$people->john',
                        // Note: the backslash escaping the ' will be removed, so doesn't count for offset.
                        30 => '$people->robert',
                    ],
                    'stripped' => '"\'s wife greeted ."',
                ],
            ],
            'Text string containing multiple embeds 4' => [
                'input'    => '"This is the value of the var named $name: {${$name}}"',
                'expected' => [
                    'get'      => [
                        36 => '$name',
                        43 => '{${$name}}',
                    ],
                    'stripped' => '"This is the value of the var named : "',
                ],
            ],
            'Text string containing multiple embeds 5 (nothing between embeds, plain)' => [
                'input'    => '"This is the value of the var named $name$name"',
                'expected' => [
                    'get'      => [
                        36 => '$name',
                        41 => '$name',
                    ],
                    'stripped' => '"This is the value of the var named "',
                ],
            ],
            'Text string containing multiple embeds 6 (nothing between embeds, outer braces)' => [
                'input'    => '"This is the value of the var named {$name}{$name}"',
                'expected' => [
                    'get'      => [
                        36 => '{$name}',
                        43 => '{$name}',
                    ],
                    'stripped' => '"This is the value of the var named "',
                ],
            ],
            'Text string containing multiple embeds 7 (nothing between embeds, inner braces)' => [
                'input'    => '"This is the value of the var named ${name}${name}"',
                'expected' => [
                    'get'      => [
                        36 => '${name}',
                        43 => '${name}',
                    ],
                    'stripped' => '"This is the value of the var named "',
                ],
            ],
            'Text string containing multiple embeds 8 (nothing between embeds, mixed)' => [
                'input'    => '"This is the value of the var named $name${name}{$name}"',
                'expected' => [
                    'get'      => [
                        36 => '$name',
                        41 => '${name}',
                        48 => '{$name}',
                    ],
                    'stripped' => '"This is the value of the var named "',
                ],
            ],

            // These can't be tested via the generated code as it won't work without braces.
            'Embed without braces, variable variable will not work' => [
                'input'    => '"$$bar"',
                'expected' => [
                    'get'      => [
                        2 => '$bar',
                    ],
                    'stripped' => '"$"',
                ],
            ],
            'Embed in outer braces followed by number' => [
                'input'    => '"This square is {$square->width}00 centimeters broad."',
                'expected' => [
                    'get'      => [
                        16 => '{$square->width}',
                    ],
                    'stripped' => '"This square is 00 centimeters broad."',
                ],
            ],
            'Embed in inner braces followed by number' => [
                'input'    => '"This square is ${square->width}00 centimeters broad."',
                'expected' => [
                    'get'      => [
                        16 => '${square->width}',
                    ],
                    'stripped' => '"This square is 00 centimeters broad."',
                ],
            ],
            'Without braces, multi-level array access does not work' => [
                'input'    => '"This works: {$arr[4][3]}, but this doesn\'t: $arr[3][4]"',
                'expected' => [
                    'get'      => [
                        13 => '{$arr[4][3]}',
                        // Note: the backslash escaping the ' will be removed, so doesn't count for offset.
                        45 => '$arr[3]',
                    ],
                    'stripped' => '"This works: , but this doesn\'t: [4]"',
                ],
            ],
            'Without braces, multi-level property access does not work' => [
                'input'    => '"This works: {$obj->prop->key}, but this doesn\'t: $obj->prop->key"',
                'expected' => [
                    'get'      => [
                        13 => '{$obj->prop->key}',
                        // Note: the backslash escaping the ' will be removed, so doesn't count for offset.
                        50 => '$obj->prop',
                    ],
                    'stripped' => '"This works: , but this doesn\'t: ->key"',
                ],
            ],
            'Embed in braces, multi-level array access, string key missing quotes' => [
                'input'    => '"This interprets the key foo as a constant: {$arr[foo][3]}"',
                'expected' => [
                    'get'      => [
                        44 => '{$arr[foo][3]}',
                    ],
                    'stripped' => '"This interprets the key foo as a constant: "',
                ],
            ],

            // Multi-line expressions.
            'Embed in dollar brace, multi-line expression' => [
                'input'    => '"Testing ${foo["${bar
  [\'baz\']
}"]} and more testing"',
                'expected' => [
                    'get'      => [
                        9 => '${foo["${bar
  [\'baz\']
}"]}',
                    ],
                    'stripped' => '"Testing  and more testing"',
                ],
            ],
            'Embed in braces, multi-line expression' => [
                'input'    => '"Testing {${foo["${bar
  [\'baz\']
}"]}} and more testing"',
                'expected' => [
                    'get'      => [
                        9 => '{${foo["${bar
  [\'baz\']
}"]}}',
                    ],
                    'stripped' => '"Testing  and more testing"',
                ],
            ],
        ];
    }

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @return void
     */
    public function testResultIsCached()
    {
        $methodName = 'PHPCSUtils\\Utils\\TextStrings::getStripEmbeds';
        $input      = '"This is the $value of {$name}"';
        $expected   = [
            'embeds'    => [
                13 => '$value',
                23 => '{$name}',
            ],
            'remaining' => '"This is the  of "',
        ];

        // Verify the caching works.
        $origStatus           = NoFileCache::$enabled;
        NoFileCache::$enabled = true;

        $resultFirstRun  = TextStrings::getStripEmbeds($input);
        $isCached        = NoFileCache::isCached($methodName, \md5($input));
        $resultSecondRun = TextStrings::getStripEmbeds($input);

        if ($origStatus === false) {
            NoFileCache::clear();
        }
        NoFileCache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'NoFileCache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }
}
