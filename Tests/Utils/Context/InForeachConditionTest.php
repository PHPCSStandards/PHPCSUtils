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
 * Tests for the \PHPCSUtils\Utils\Context::inForeachCondition() method.
 *
 * @covers \PHPCSUtils\Utils\Context::inForeachCondition
 *
 * @since 1.0.0
 */
final class InForeachConditionTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(Context::inForeachCondition(self::$phpcsFile, 10000));
    }

    /**
     * Test receiving `false` when the token passed is not in a foreach condition.
     *
     * @dataProvider dataNotInForeach
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @return void
     */
    public function testNotInForeach($testMarker)
    {
        $target = $this->getTargetToken($testMarker, \T_VARIABLE, '$target');
        $this->assertFalse(Context::inForeachCondition(self::$phpcsFile, $target));
    }

    /**
     * Data provider.
     *
     * @see testNotInForeach()
     *
     * @return array<string, array<string>>
     */
    public static function dataNotInForeach()
    {
        return [
            'no-parenthesis'                       => ['/* testNoParentheses */'],
            'no-parenthesis-owner'                 => ['/* testNoParenthesisOwner */'],
            'owner-not-foreach'                    => ['/* testOwnerNotForeach */'],
            'owner-not-foreach-nested-parentheses' => ['/* testOwnerNotForeachNestedParentheses */'],
            'method-called-foreach'                => ['/* testNotForeachMethodCall */'],
            'foreach-without-as'                   => ['/* testForeachWithoutAs */'],
            'parse-error'                          => ['/* testParseError */'],
        ];
    }

    /**
     * Test correctly identifying the position of a token in a foreach condition.
     *
     * @dataProvider dataInForeachCondition
     *
     * @param string     $testMarker    The comment which prefaces the target token in the test file.
     * @param string     $expected      The expected function return value.
     * @param int|string $targetType    Optional. The token type of the target token.
     *                                  Defaults to T_VARIABLE.
     * @param string     $targetContent Optional. The token content of the target token.
     *                                  Defaults to `$target` for `T_VARIABLE`.
     *
     * @return void
     */
    public function testInForeachCondition($testMarker, $expected, $targetType = \T_VARIABLE, $targetContent = null)
    {
        if ($targetType === \T_VARIABLE && $targetContent === null) {
            $targetContent = '$target';
        }

        $stackPtr = $this->getTargetToken($testMarker, $targetType, $targetContent);
        $result   = Context::inForeachCondition(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testInForeachCondition()
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataInForeachCondition()
    {
        return [
            'before' => [
                'testMarker'    => '/* testForeachValue */',
                'expected'      => 'beforeAs',
                'targetType'    => \T_VARIABLE,
                'targetContent' => '$array',
            ],
            'as' => [
                'testMarker' => '/* testForeachValue */',
                'expected'   => 'as',
                'targetType' => \T_AS,
            ],
            'after' => [
                'testMarker'    => '/* testForeachValue */',
                'expected'      => 'afterAs',
                'targetType'    => \T_VARIABLE,
                'targetContent' => '$value',
            ],
            'as-caps' => [
                'testMarker' => '/* testForeachKeyValue */',
                'expected'   => 'as',
                'targetType' => \T_AS,
            ],
            'after-key' => [
                'testMarker'    => '/* testForeachKeyValue */',
                'expected'      => 'afterAs',
                'targetType'    => \T_VARIABLE,
                'targetContent' => '$key',
            ],
            'before-in-long-array' => [
                'testMarker'    => '/* testForeachBeforeLongArrayMinimalWhiteSpace */',
                'expected'      => 'beforeAs',
                'targetType'    => \T_LNUMBER,
                'targetContent' => '2',
            ],
            'before-function-call' => [
                'testMarker' => '/* testForeachBeforeFunctionCall */',
                'expected'   => 'beforeAs',
                'targetType' => \T_STRING,
            ],
            'before-array-nested-in-function-call' => [
                'testMarker' => '/* testForeachBeforeFunctionCall */',
                'expected'   => 'beforeAs',
                'targetType' => \T_ARRAY,
            ],
            'before-var-nested-in-array-nested-in-function-call' => [
                'testMarker' => '/* testForeachBeforeFunctionCall */',
                'expected'   => 'beforeAs',
            ],
            'after-list' => [
                'testMarker' => '/* testForeachVarAfterAsList */',
                'expected'   => 'afterAs',
                'targetType' => \T_LIST,
            ],
            'after-variable-in-list' => [
                'testMarker' => '/* testForeachVarAfterAsList */',
                'expected'   => 'afterAs',
            ],
            'after-variable-in-short-list' => [
                'testMarker' => '/* testForeachVarAfterAsList */',
                'expected'   => 'afterAs',
            ],
            'after-variable-in-list-value' => [
                'testMarker' => '/* testForeachVarAfterAsKeyedList */',
                'expected'   => 'afterAs',
            ],
            'after-variable-in-list-key' => [
                'testMarker'    => '/* testForeachVarAfterAsKeyedList */',
                'expected'      => 'afterAs',
                'targetType'    => \T_CONSTANT_ENCAPSED_STRING,
                'targetContent' => "'name'",
            ],
            'after-foreach-nested-in-closure' => [
                'testMarker' => '/* testNestedForeach */',
                'expected'   => 'afterAs',
            ],
            'before-trait-use-as-in-anon-class-nested-in-long-array-as-token-1' => [
                'testMarker' => '/* testForeachBeforeContainsAsInLongArrayBefore1 */',
                'expected'   => 'beforeAs',
                'targetType' => \T_AS,
            ],
            'before-trait-use-in-anon-class-nested-in-long-array-after-as-token-1' => [
                'testMarker'    => '/* testForeachBeforeContainsAsInLongArrayBefore1 */',
                'expected'      => 'beforeAs',
                'targetType'    => \T_STRING,
                'targetContent' => 'talk',
            ],
            'before-trait-use-as-in-anon-class-nested-in-long-array-as-token-2' => [
                'testMarker' => '/* testForeachBeforeContainsAsInLongArrayBefore2 */',
                'expected'   => 'beforeAs',
                'targetType' => \T_AS,
            ],
            'before-trait-use-in-anon-class-nested-in-long-array-after-as-token-2' => [
                'testMarker' => '/* testForeachBeforeContainsAsInLongArrayBefore2 */',
                'expected'   => 'beforeAs',
                'targetType' => \T_PROTECTED,
            ],
            'as-with-as-used-in-long-array-before' => [
                'testMarker' => '/* testForeachBeforeContainsAsInLongArrayAs */',
                'expected'   => 'as',
                'targetType' => \T_AS,
            ],
            'before-trait-use-as-in-anon-class-nested-in-short-array-as-token-1' => [
                'testMarker' => '/* testForeachBeforeContainsAsInShortArrayBefore1 */',
                'expected'   => 'beforeAs',
                'targetType' => \T_AS,
            ],
            'before-trait-use-in-anon-class-nested-in-short-array-after-as-token-1' => [
                'testMarker'    => '/* testForeachBeforeContainsAsInShortArrayBefore1 */',
                'expected'      => 'beforeAs',
                'targetType'    => \T_STRING,
                'targetContent' => 'talk',
            ],
            'before-trait-use-as-in-anon-class-nested-in-short-array-as-token-2' => [
                'testMarker' => '/* testForeachBeforeContainsAsInShortArrayBefore2 */',
                'expected'   => 'beforeAs',
                'targetType' => \T_AS,
            ],
            'before-trait-use-in-anon-class-nested-in-short-array-after-as-token-2' => [
                'testMarker' => '/* testForeachBeforeContainsAsInShortArrayBefore2 */',
                'expected'   => 'beforeAs',
                'targetType' => \T_PROTECTED,
            ],
            'as-with-as-used-in-short-array-before' => [
                'testMarker' => '/* testForeachBeforeContainsAsInShortArrayAs */',
                'expected'   => 'as',
                'targetType' => \T_AS,
            ],
        ];
    }
}
