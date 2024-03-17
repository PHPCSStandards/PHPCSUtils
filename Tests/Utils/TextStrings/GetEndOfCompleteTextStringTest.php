<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\TextStrings;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\GetTokensAsString;
use PHPCSUtils\Utils\TextStrings;

/**
 * Tests for the \PHPCSUtils\Utils\TextStrings::getEndOfCompleteTextString() method covering a specific tokenizer
 * issue as reported upstream in {@link https://github.com/squizlabs/PHP_CodeSniffer/pull/3604 PHPCS 3604}.
 *
 * {@internal The exceptions thrown by the method are tested in the `GetCompleteTextStringTest` class.}
 *
 * @covers \PHPCSUtils\Utils\TextStrings::getEndOfCompleteTextString
 *
 * @since 1.0.0
 */
final class GetEndOfCompleteTextStringTest extends UtilityMethodTestCase
{

    /**
     * Test correctly retrieving the contents of a double quoted text string with potentially problematic
     * embedded variables/expressions.
     *
     * @dataProvider dataGetEndOfDoubleQuotedString
     *
     * @param string $testMarker      The comment which prefaces the target token in the test file.
     * @param string $expectedContent The expected content of the double quoted string.
     *
     * @return void
     */
    public function testGetEndOfDoubleQuotedString($testMarker, $expectedContent)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_DOUBLE_QUOTED_STRING);
        $result   = TextStrings::getEndOfCompleteTextString(self::$phpcsFile, $stackPtr);

        $this->assertSame($expectedContent, GetTokensAsString::normal(self::$phpcsFile, $stackPtr, $result));
    }

    /**
     * Data provider.
     *
     * @see testGetEndOfDoubleQuotedString() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataGetEndOfDoubleQuotedString()
    {
        return [
            'Simple embedded variable 1' => [
                'testMarker'      => '/* testSimple1 */',
                'expectedContent' => '"$foo"',
            ],
            'Simple embedded variable 2' => [
                'testMarker'      => '/* testSimple2 */',
                'expectedContent' => '"{$foo}"',
            ],
            'Simple embedded variable 3' => [
                'testMarker'      => '/* testSimple3 */',
                'expectedContent' => '"${foo}"',
            ],
            'Embedded array access 1' => [
                'testMarker'      => '/* testDIM1 */',
                'expectedContent' => '"$foo[bar]"',
            ],
            'Embedded array access 2' => [
                'testMarker'      => '/* testDIM2 */',
                'expectedContent' => '"{$foo[\'bar\']}"',
            ],
            'Embedded array access 3' => [
                'testMarker'      => '/* testDIM3 */',
                'expectedContent' => '"${foo[\'bar\']}"',
            ],
            'Embedded property access 1' => [
                'testMarker'      => '/* testProperty1 */',
                'expectedContent' => '"$foo->bar"',
            ],
            'Embedded property access 2' => [
                'testMarker'      => '/* testProperty2 */',
                'expectedContent' => '"{$foo->bar}"',
            ],
            'Embedded method call 1' => [
                'testMarker'      => '/* testMethod1 */',
                'expectedContent' => '"{$foo->bar()}"',
            ],
            'Embedded closure call 1' => [
                'testMarker'      => '/* testClosure1 */',
                'expectedContent' => '"{$foo()}"',
            ],
            'Embedded chained array access -> method call -> call' => [
                'testMarker'      => '/* testChain1 */',
                'expectedContent' => '"{$foo[\'bar\']->baz()()}"',
            ],
            'Embedded variable variable 1' => [
                'testMarker'      => '/* testVariableVar1 */',
                'expectedContent' => '"${$bar}"',
            ],
            'Embedded variable variable 2' => [
                'testMarker'      => '/* testVariableVar2 */',
                'expectedContent' => '"${(foo)}"',
            ],
            'Embedded variable variable 3' => [
                'testMarker'      => '/* testVariableVar3 */',
                'expectedContent' => '"${foo->bar}"',
            ],
            'Embedded nested variable variable 1' => [
                'testMarker'      => '/* testNested1 */',
                'expectedContent' => '"${foo["${bar}"]}"',
            ],
            'Embedded nested variable variable 2' => [
                'testMarker'      => '/* testNested2 */',
                'expectedContent' => '"${foo["${bar[\'baz\']}"]}"',
            ],
            'Embedded nested variable variable 3' => [
                'testMarker'      => '/* testNested3 */',
                'expectedContent' => '"${foo->{$baz}}"',
            ],
            'Embedded nested variable variable 4' => [
                'testMarker'      => '/* testNested4 */',
                'expectedContent' => '"${foo->{${\'a\'}}}"',
            ],
            'Embedded nested variable variable 5' => [
                'testMarker'      => '/* testNested5 */',
                'expectedContent' => '"${foo->{"${\'a\'}"}}"',
            ],
            'Embedded nested variable variable 2 with curlies within plain token' => [
                'testMarker'      => '/* testNestedWithCurliesWithinPlainTokens */',
                'expectedContent' => '"${foo["${bar[\'b{a}z\']}"]}"',
            ],
            'Multiple problem embeds in single line text string' => [
                'testMarker'      => '/* testMultipleProblemEmbedsInSingleLineString */',
                'expectedContent' => '"My ${foo["${bar}"]} and ${foo["${bar[\'baz\']}"]} and ${foo->{"${\'a\'}"}}"',
            ],
            'Problem embed at end of line in multi-line text string' => [
                'testMarker'      => '/* testProblemEmbedAtEndOfLineInMultiLineString */',
                'expectedContent' => '"Testing ${foo["${bar[\'baz\']}"]}
and more testing"',
            ],
            'Multi-line problem embed in multi-line text string' => [
                'testMarker'      => '/* testMultilineProblemEmbedInMultiLineString */',
                'expectedContent' => '"Testing ${foo["${bar
  [\'baz\']
}"]} and more testing"',
            ],

            'Parse error at end of file' => [
                'testMarker'      => '/* testParseError */',
                'expectedContent' => '"${foo["${bar
',
            ],
        ];
    }
}
