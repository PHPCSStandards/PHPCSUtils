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
 * Tests for the \PHPCSUtils\Utils\ControlStructures::getDeclareScopeOpenClose() method.
 *
 * @covers \PHPCSUtils\Utils\ControlStructures::getDeclareScopeOpenClose
 *
 * @group controlstructures
 *
 * @since 1.0.0
 */
class GetDeclareScopeOpenCloseTest extends UtilityMethodTestCase
{

    /**
     * Test that false is returned when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(ControlStructures::getDeclareScopeOpenClose(self::$phpcsFile, 10000));
    }

    /**
     * Test that false is returned when a token other than `T_DECLARE` is passed.
     *
     * @return void
     */
    public function testNotDeclare()
    {
        $target = $this->getTargetToken('/* testNotDeclare */', \T_ECHO);
        $this->assertFalse(ControlStructures::getDeclareScopeOpenClose(self::$phpcsFile, $target));
    }

    /**
     * Test retrieving the scope open/close tokens for a `declare` statement.
     *
     * @dataProvider dataGetDeclareScopeOpenClose
     *
     * @param string      $testMarker The comment which prefaces the target token in the test file.
     * @param array|false $expected   The expected return value.
     *
     * @return void
     */
    public function testGetDeclareScopeOpenClose($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_DECLARE);

        // Translate offsets to absolute token positions.
        if (isset($expected['opener'], $expected['closer']) === true) {
            $expected['opener'] += $stackPtr;
            $expected['closer'] += $stackPtr;
        }

        $result = ControlStructures::getDeclareScopeOpenClose(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetDeclareScopeOpenClose() For the array format.
     *
     * @return array
     */
    public function dataGetDeclareScopeOpenClose()
    {
        return [
            'file-scope' => [
                '/* testFileScope */',
                false,
            ],

            'curlies' => [
                '/* testCurlies */',
                [
                    'opener' => 7,
                    'closer' => 11,
                ],
            ],
            'nested-curlies-outside' => [
                '/* testNestedCurliesOutside */',
                [
                    'opener' => 7,
                    'closer' => 32,
                ],
            ],
            'nested-curlies-inside' => [
                '/* testNestedCurliesInside */',
                [
                    'opener' => 12,
                    'closer' => 17,
                ],
            ],

            'alternative-syntax' => [
                '/* testAlternativeSyntax */',
                [
                    'opener' => 7,
                    'closer' => 11,
                ],
            ],
            'alternative-syntax-nested-level-1' => [
                '/* testAlternativeSyntaxNestedLevel1 */',
                [
                    'opener' => 7,
                    'closer' => 50,
                ],
            ],
            'alternative-syntax-nested-level-2' => [
                '/* testAlternativeSyntaxNestedLevel2 */',
                [
                    'opener' => 12,
                    'closer' => 34,
                ],
            ],
            'alternative-syntax-nested-level-3' => [
                '/* testAlternativeSyntaxNestedLevel3 */',
                [
                    'opener' => 7,
                    'closer' => 12,
                ],
            ],

            'mixed-nested-level-1' => [
                '/* testMixedNestedLevel1 */',
                [
                    'opener' => 7,
                    'closer' => 61,
                ],
            ],
            'mixed-nested-level-2' => [
                '/* testMixedNestedLevel2 */',
                [
                    'opener' => 12,
                    'closer' => 46,
                ],
            ],
            'mixed-nested-level-3' => [
                '/* testMixedNestedLevel3 */',
                [
                    'opener' => 7,
                    'closer' => 24,
                ],
            ],
            'mixed-nested-level-4' => [
                '/* testMixedNestedLevel4 */',
                false,
            ],

            'live-coding' => [
                '/* testLiveCoding */',
                false,
            ],
        ];
    }
}
