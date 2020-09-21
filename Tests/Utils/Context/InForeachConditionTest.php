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
 * @group context
 *
 * @since 1.0.0
 */
class InForeachConditionTest extends UtilityMethodTestCase
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
     * @return array
     */
    public function dataNotInForeach()
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
     * @param string           $testMarker    The comment which prefaces the target token in the test file.
     * @param string           $expected      The expected function return value.
     * @param int|string|array $targetType    Optional. The token type of the target token.
     *                                        Defaults to T_VARIABLE.
     * @param string           $targetContent Optional. The token content of the target token.
     *                                        Defaults to `$target` for `T_VARIABLE`.
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
     * @return array
     */
    public function dataInForeachCondition()
    {
        return [
            'before' => [
                '/* testForeachValue */',
                'beforeAs',
                \T_VARIABLE,
                '$array',
            ],
            'as' => [
                '/* testForeachValue */',
                'as',
                \T_AS,
            ],
            'after' => [
                '/* testForeachValue */',
                'afterAs',
                \T_VARIABLE,
                '$value',
            ],
            'as-caps' => [
                '/* testForeachKeyValue */',
                'as',
                \T_AS,
            ],
            'after-key' => [
                '/* testForeachKeyValue */',
                'afterAs',
                \T_VARIABLE,
                '$key',
            ],
            'before-in-long-array' => [
                '/* testForeachBeforeLongArrayMinimalWhiteSpace */',
                'beforeAs',
                \T_LNUMBER,
                '2',
            ],
            'before-function-call' => [
                '/* testForeachBeforeFunctionCall */',
                'beforeAs',
                \T_STRING,
            ],
            'before-array-nested-in-function-call' => [
                '/* testForeachBeforeFunctionCall */',
                'beforeAs',
                \T_ARRAY,
            ],
            'before-var-nested-in-array-nested-in-function-call' => [
                '/* testForeachBeforeFunctionCall */',
                'beforeAs',
            ],
            'after-list' => [
                '/* testForeachVarAfterAsList */',
                'afterAs',
                \T_LIST,
            ],
            'after-variable-in-list' => [
                '/* testForeachVarAfterAsList */',
                'afterAs',
            ],
            'after-variable-in-short-list' => [
                '/* testForeachVarAfterAsList */',
                'afterAs',
            ],
            'after-variable-in-list-value' => [
                '/* testForeachVarAfterAsKeyedList */',
                'afterAs',
            ],
            'after-variable-in-list-key' => [
                '/* testForeachVarAfterAsKeyedList */',
                'afterAs',
                \T_CONSTANT_ENCAPSED_STRING,
                "'name'",
            ],
            'after-foreach-nested-in-closure' => [
                '/* testNestedForeach */',
                'afterAs',
            ],
        ];
    }
}
