<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Internal\IsShortArrayOrList;

use PHPCSUtils\Internal\IsShortArrayOrList;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests to make sure that *real* square brackets aren't recognized as short array/short lists.
 *
 * @covers \PHPCSUtils\Internal\IsShortArrayOrList::isSquareBracket
 *
 * @since 1.0.0
 */
final class IsSquareBracketTest extends UtilityMethodTestCase
{

    /**
     * Test that a real short array is not disregarded as if it were square brackets.
     * (testing the `return false for `IsShortArrayOrList::isSquareBracket()`)
     *
     * @return void
     */
    public function testShortArrayBrackets()
    {
        $stackPtr = $this->getTargetToken('/* testShortArray */', \T_OPEN_SHORT_ARRAY);
        $solver   = new IsShortArrayOrList(self::$phpcsFile, $stackPtr);
        $type     = $solver->solve();

        $this->assertNotSame(IsShortArrayOrList::SQUARE_BRACKETS, $type);
    }

    /**
     * Test that real square brackets are recognized as such.
     *
     * @dataProvider dataSquareBrackets
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @return void
     */
    public function testSquareBrackets($testMarker)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_OPEN_SQUARE_BRACKET);
        $solver   = new IsShortArrayOrList(self::$phpcsFile, $stackPtr);
        $type     = $solver->solve();

        $this->assertSame(IsShortArrayOrList::SQUARE_BRACKETS, $type);
    }

    /**
     * Data provider.
     *
     * @see testSquareBrackets() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataSquareBrackets()
    {
        return [
            'array-assignment-no-key' => [
                'testMarker'  => '/* testArrayAssignmentEmpty */',
            ],
            'array-assignment-string-key' => [
                'testMarker' => '/* testArrayAssignmentStringKey */',
            ],
            'array-assignment-int-key' => [
                'testMarker' => '/* testArrayAssignmentIntKey */',
            ],
            'array-assignment-var-key' => [
                'testMarker' => '/* testArrayAssignmentVarKey */',
            ],
            'array-access-string-key' => [
                'testMarker'  => '/* testArrayAccessStringKey */',
            ],
            'array-access-int-key-1' => [
                'testMarker' => '/* testArrayAccessIntKey1 */',
            ],
            'array-access-int-key-2' => [
                'testMarker' => '/* testArrayAccessIntKey2 */',
            ],
            'array-access-function-call' => [
                'testMarker' => '/* testArrayAccessFunctionCall */',
            ],
            'array-access-constant' => [
                'testMarker' => '/* testArrayAccessConstant */',
            ],
            'array-access-magic-constant' => [
                'testMarker' => '/* testArrayAccessMagicConstant */',
            ],
            'array-access-nullsafe-method-call' => [
                'testMarker' => '/* testNullsafeMethodCallDereferencing */',
            ],
            'array-access-for-short-list-key' => [
                'testMarker' => '/* testArrayAccessForShortListKey */',
            ],
            'list-assignment-to-array-with-string-key' => [
                'testMarker' => '/* testListAssignmentToArrayStringKey */',
            ],
            'list-assignment-to-array-without-key' => [
                'testMarker' => '/* testListAssignmentToArrayEmptyKey */',
            ],
            'array-access-for-short-list-key-with-hardcoded-array' => [
                'testMarker' => '/* testArrayDerefOfShortArrayInShortListAsKey */',
            ],

            'live-coding' => [
                'testMarker' => '/* testLiveCoding */',
            ],
        ];
    }
}
