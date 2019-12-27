<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Conditions;

use PHPCSUtils\Tests\BackCompat\BCFile\GetConditionTest as BCFile_GetConditionTest;
use PHPCSUtils\Utils\Conditions;

/**
 * Tests for various methods in the \PHPCSUtils\Utils\Conditions class.
 *
 * @covers \PHPCSUtils\Utils\Conditions
 *
 * @group conditions
 *
 * @since 1.0.0
 */
class GetConditionTest extends BCFile_GetConditionTest
{

    /**
     * The fully qualified name of the class being tested.
     *
     * This allows for the same unit tests to be run for both the BCFile functions
     * as well as for the related PHPCSUtils functions.
     *
     * @var string
     */
    const TEST_CLASS = '\PHPCSUtils\Utils\Conditions';

    /**
     * Full path to the test case file associated with this test class.
     *
     * @var string
     */
    protected static $caseFile = '';

    /**
     * Initialize PHPCS & tokenize the test case file.
     *
     * Overloaded to re-use the `$caseFile` from the BCFile test.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = \dirname(\dirname(__DIR__)) . '/BackCompat/BCFile/GetConditionTest.inc';
        parent::setUpTestFile();
    }

    /**
     * Test retrieving a specific condition from a tokens "conditions" array.
     *
     * @dataProvider dataGetConditionReversed
     *
     * @param string $testMarker      The comment which prefaces the target token in the test file.
     * @param array  $expectedResults Array with the condition token type to search for as key
     *                                and the marker for the expected stack pointer result as a value.
     *
     * @return void
     */
    public function testGetConditionReversed($testMarker, $expectedResults)
    {
        $stackPtr = self::$testTokens[$testMarker];

        // Add expected results for all test markers not listed in the data provider.
        $expectedResults += $this->conditionDefaults;

        foreach ($expectedResults as $conditionType => $expected) {
            if ($expected !== false) {
                $expected = self::$markerTokens[$expected];
            }

            $result = Conditions::getCondition(self::$phpcsFile, $stackPtr, \constant($conditionType), true);
            $this->assertSame(
                $expected,
                $result,
                "Assertion failed for test marker '{$testMarker}' with condition {$conditionType} (reversed)"
            );
        }
    }

    /**
     * Data provider.
     *
     * Only the conditions which are expected to be *found* need to be listed here.
     * All other potential conditions will automatically also be tested and will expect
     * `false` as a result.
     *
     * @see testGetConditionReversed() For the array format.
     *
     * @return array
     */
    public static function dataGetConditionReversed()
    {
        $data = self::dataGetCondition();

        // Set up the data for the reversed results.
        $data['testSeriouslyNestedMethod'][1]['T_IF'] = '/* condition 4: if */';

        $data['testDeepestNested'][1]['T_FUNCTION'] = '/* condition 12: nested anonymous class method */';
        $data['testDeepestNested'][1]['T_IF']       = '/* condition 10-1: if */';

        $data['testInException'][1]['T_FUNCTION'] = '/* condition 6: class method */';
        $data['testInException'][1]['T_IF']       = '/* condition 4: if */';

        $data['testInDefault'][1]['T_FUNCTION'] = '/* condition 6: class method */';
        $data['testInDefault'][1]['T_IF']       = '/* condition 4: if */';

        return $data;
    }

    /**
     * Test retrieving a specific condition from a token's "conditions" array,
     * with multiple allowed possibilities.
     *
     * @return void
     */
    public function testGetConditionMultipleTypes()
    {
        $stackPtr = self::$testTokens['/* testInException */'];

        $result = Conditions::getCondition(self::$phpcsFile, $stackPtr, [\T_DO, \T_FOR]);
        $this->assertFalse(
            $result,
            'Failed asserting that "testInException" does not have a "do" nor a "for" condition'
        );

        $result = Conditions::getCondition(self::$phpcsFile, $stackPtr, [\T_DO, \T_FOR, \T_FOREACH]);
        $this->assertSame(
            self::$markerTokens['/* condition 10-3: foreach */'],
            $result,
            'Failed asserting that "testInException" has a condition based on the types "do", "for" and "foreach"'
        );

        $stackPtr = self::$testTokens['/* testDeepestNested */'];

        $result = Conditions::getCondition(self::$phpcsFile, $stackPtr, [\T_INTERFACE, \T_TRAIT]);
        $this->assertFalse(
            $result,
            'Failed asserting that "testDeepestNested" does not have an interface nor a trait condition'
        );

        $result = Conditions::getCondition(self::$phpcsFile, $stackPtr, $this->ooScopeTokens);
        $this->assertSame(
            self::$markerTokens['/* condition 5: nested class */'],
            $result,
            'Failed asserting that "testDeepestNested" has a class condition based on the OO Scope token types'
        );
    }

    /**
     * Test passing a non conditional token to getFirstCondition()/getLastCondition().
     *
     * @return void
     */
    public function testNonConditionalTokenGetFirstLast()
    {
        $stackPtr = $this->getTargetToken('/* testStartPoint */', \T_STRING);

        $result = Conditions::getFirstCondition(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result, 'Failed asserting that getFirstCondition() on non conditional token returns false');

        $result = Conditions::getLastCondition(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result, 'Failed asserting that getLastCondition() on non conditional token returns false');
    }

    /**
     * Test retrieving the first condition token pointer, in general and of specific types.
     *
     * @dataProvider dataGetFirstCondition
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @return void
     */
    public function testGetFirstCondition($testMarker)
    {
        $stackPtr = self::$testTokens[$testMarker];

        $result = Conditions::getFirstCondition(self::$phpcsFile, $stackPtr);
        $this->assertSame(self::$markerTokens['/* condition 0: namespace */'], $result);

        $result = Conditions::getFirstCondition(self::$phpcsFile, $stackPtr, \T_IF);
        $this->assertSame(self::$markerTokens['/* condition 1: if */'], $result);

        $result = Conditions::getFirstCondition(self::$phpcsFile, $stackPtr, $this->ooScopeTokens);
        $this->assertSame(self::$markerTokens['/* condition 5: nested class */'], $result);

        $result = Conditions::getFirstCondition(self::$phpcsFile, $stackPtr, [\T_ELSEIF]);
        $this->assertFalse($result);
    }

    /**
     * Data provider. Pass the markers for the test tokens on.
     *
     * @see testGetFirstCondition() For the array format.
     *
     * @return array
     */
    public function dataGetFirstCondition()
    {
        $data = [];
        foreach (self::$testTargets as $marker) {
            $data[\trim($marker, '/* ')] = [$marker];
        }

        return $data;
    }

    /**
     * Test retrieving the last condition token pointer, in general and of specific types.
     *
     * @dataProvider dataGetLastCondition
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The marker for the pointers to the expected condition
     *                           results for the pre-set tests.
     *
     * @return void
     */
    public function testGetLastCondition($testMarker, $expected)
    {
        $stackPtr = self::$testTokens[$testMarker];

        $result = Conditions::getLastCondition(self::$phpcsFile, $stackPtr);
        $this->assertSame(self::$markerTokens[$expected['no type']], $result);

        $result = Conditions::getLastCondition(self::$phpcsFile, $stackPtr, \T_IF);
        $this->assertSame(self::$markerTokens[$expected['T_IF']], $result);

        $result = Conditions::getLastCondition(self::$phpcsFile, $stackPtr, $this->ooScopeTokens);
        $this->assertSame(self::$markerTokens[$expected['OO tokens']], $result);

        $result = Conditions::getLastCondition(self::$phpcsFile, $stackPtr, [\T_FINALLY]);
        $this->assertFalse($result);
    }

    /**
     * Data provider.
     *
     * @see testGetLastCondition() For the array format.
     *
     * @return array
     */
    public function dataGetLastCondition()
    {
        return [
            'testSeriouslyNestedMethod' => [
                '/* testSeriouslyNestedMethod */',
                [
                    'no type'   => '/* condition 5: nested class */',
                    'T_IF'      => '/* condition 4: if */',
                    'OO tokens' => '/* condition 5: nested class */',
                ],
            ],
            'testDeepestNested' => [
                '/* testDeepestNested */',
                [
                    'no type'   => '/* condition 13: closure */',
                    'T_IF'      => '/* condition 10-1: if */',
                    'OO tokens' => '/* condition 11-1: nested anonymous class */',
                ],
            ],
            'testInException' => [
                '/* testInException */',
                [
                    'no type'   => '/* condition 11-3: catch */',
                    'T_IF'      => '/* condition 4: if */',
                    'OO tokens' => '/* condition 5: nested class */',
                ],
            ],
            'testInDefault' => [
                '/* testInDefault */',
                [
                    'no type'   => '/* condition 8b: default */',
                    'T_IF'      => '/* condition 4: if */',
                    'OO tokens' => '/* condition 5: nested class */',
                ],
            ],
        ];
    }
}
