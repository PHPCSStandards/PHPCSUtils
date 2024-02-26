<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Context;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Context;

/**
 * Tests for the \PHPCSUtils\Utils\Context::inAttribute() method.
 *
 * @covers \PHPCSUtils\Utils\Context::inAttribute
 *
 * @since 1.0.0
 */
final class InAttributeTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(Context::inAttribute(self::$phpcsFile, 10000));
    }

    /**
     * Test correctly identifying that an arbitrary token is NOT within an attribute.
     *
     * @dataProvider dataNotInAttribute
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType The token type(s) to look for.
     *
     * @return void
     */
    public function testNotInAttribute($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $this->assertFalse(Context::inAttribute(self::$phpcsFile, $target));
    }

    /**
     * Data provider.
     *
     * @see testInAttribute()
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataNotInAttribute()
    {
        return [
            'code nowhere near an attribute [1]' => [
                'testMarker' => '/* testNotAttribute */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
            ],
            'code nowhere near an attribute [2]' => [
                'testMarker' => '/* testNotAttribute */',
                'targetType' => \T_SELF,
            ],

            'code directly before an attribute (same line)' => [
                'testMarker' => '/* testAttribute */',
                'targetType' => \T_VARIABLE,
            ],
            'attribute opener' => [
                'testMarker' => '/* testAttribute */',
                'targetType' => \T_ATTRIBUTE,
            ],
            'attribute closer' => [
                'testMarker' => '/* testAttribute */',
                'targetType' => \T_ATTRIBUTE_END,
            ],
            'code directly after an attribute (same line)' => [
                'testMarker' => '/* testAttribute */',
                'targetType' => \T_FN,
            ],

            'code directly after an attribute (different line)' => [
                'testMarker' => '/* testMultiLineAttributeWithVars */',
                'targetType' => \T_FUNCTION,
            ],

            'code in an unclosed attribute (parse error)' => [
                'testMarker' => '/* testParseError */',
                'targetType' => \T_STRING,
            ],
        ];
    }

    /**
     * Test correctly identifying that an arbitrary token IS within an attribute.
     *
     * @dataProvider dataInAttribute
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType The token type(s) to look for.
     *
     * @return void
     */
    public function testInAttribute($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $this->assertTrue(Context::inAttribute(self::$phpcsFile, $target));
    }

    /**
     * Data provider.
     *
     * @see testInAttribute()
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataInAttribute()
    {
        return [
            'single line attribute - attribute name' => [
                'testMarker' => '/* testAttribute */',
                'targetType' => \T_STRING,
            ],
            'single line attribute - attribute param [1]' => [
                'testMarker' => '/* testAttribute */',
                'targetType' => \T_LNUMBER,
            ],
            'single line attribute - comma in attribute param sequence' => [
                'testMarker' => '/* testAttribute */',
                'targetType' => \T_COMMA,
            ],
            'single line attribute - attribute param [2]' => [
                'testMarker' => '/* testAttribute */',
                'targetType' => \T_SELF,
            ],
            'single line attribute - close parenthesis' => [
                'testMarker' => '/* testAttribute */',
                'targetType' => \T_CLOSE_PARENTHESIS,
            ],

            'multi line attribute - attribute name' => [
                'testMarker' => '/* testMultiLineAttributeWithVars */',
                'targetType' => \T_STRING,
            ],
            'multi line attribute - attribute param [1]' => [
                'testMarker' => '/* testMultiLineAttributeWithVars */',
                'targetType' => \T_OPEN_SHORT_ARRAY,
            ],
            'multi line attribute - attribute param [2]' => [
                'testMarker' => '/* testMultiLineAttributeWithVars */',
                'targetType' => \T_VARIABLE,
            ],
            'multi line attribute - close parenthesis' => [
                'testMarker' => '/* testMultiLineAttributeWithVars */',
                'targetType' => \T_CLOSE_PARENTHESIS,
            ],
        ];
    }
}
