<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\ControlStructures;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\ControlStructures;

/**
 * Tests for the \PHPCSUtils\Utils\ControlStructures::isElseIf() method.
 *
 * @covers \PHPCSUtils\Utils\ControlStructures::isElseIf
 *
 * @group controlstructures
 *
 * @since 1.0.0
 */
class IsElseIfTest extends UtilityMethodTestCase
{

    /**
     * Test that false is returned when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(ControlStructures::isElseIf(self::$phpcsFile, 10000));
    }

    /**
     * Test that false is returned when a token other than `T_IF`, `T_ELSE`, `T_ELSEIF` is passed.
     *
     * @return void
     */
    public function testNotIfElseifOrElse()
    {
        $target = $this->getTargetToken('/* testNotIfElseifOrElse */', \T_ECHO);
        $this->assertFalse(ControlStructures::isElseIf(self::$phpcsFile, $target));
    }

    /**
     * Test whether a T_IF or T_ELSE token is correctly identified as either elseif or not.
     *
     * @dataProvider dataIsElseIf
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   The expected boolean return value.
     *
     * @return void
     */
    public function testIsElseIf($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, [\T_IF, \T_ELSEIF, \T_ELSE]);
        $result   = ControlStructures::isElseIf(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testIsElseIf() For the array format.
     *
     * @return array
     */
    public function dataIsElseIf()
    {
        return [
            'if' => [
                '/* testIf */',
                false,
            ],
            'elseif' => [
                '/* testElseIf */',
                true,
            ],
            'else-if' => [
                '/* testElseSpaceIf */',
                true,
            ],
            'else-if-with-comment-else' => [
                '/* testElseCommentIfElse */',
                true,
            ],
            'else-if-with-comment-if' => [
                '/* testElseCommentIfIf */',
                true,
            ],
            'else' => [
                '/* testElse */',
                false,
            ],

            'alternative-syntax-if' => [
                '/* testAlternativeIf */',
                false,
            ],
            'alternative-syntax-elseif' => [
                '/* testAlternativeElseIf */',
                true,
            ],
            'alternative-syntax-else' => [
                '/* testAlternativeElse */',
                false,
            ],

            'inline-if' => [
                '/* testAlternativeIf */',
                false,
            ],
            'inline-elseif' => [
                '/* testAlternativeElseIf */',
                true,
            ],
            'inline-else' => [
                '/* testAlternativeElse */',
                false,
            ],

            'live-coding' => [
                '/* testLiveCoding */',
                false,
            ],
        ];
    }
}
