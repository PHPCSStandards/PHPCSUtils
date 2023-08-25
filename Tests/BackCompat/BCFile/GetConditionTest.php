<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\BackCompat\BCFile;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\BackCompat\BCTokens;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::getCondition() and
 * \PHPCSUtils\BackCompat\BCFile::hasCondition() methods.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::getCondition
 * @covers \PHPCSUtils\BackCompat\BCFile::hasCondition
 *
 * @group conditions
 *
 * @since 1.0.0
 */
class GetConditionTest extends UtilityMethodTestCase
{

    /**
     * The fully qualified name of the class being tested.
     *
     * This allows for the same unit tests to be run for both the BCFile functions
     * as well as for the related PHPCSUtils functions.
     *
     * @var string
     */
    const TEST_CLASS = '\PHPCSUtils\BackCompat\BCFile';

    /**
     * List of all the test markers with their target token in the test case file.
     *
     * - The startPoint token is left out as it is tested separately.
     * - The key is the type of token to look for after the test marker.
     *
     * @var array<int|string, string>
     */
    protected static $testTargets = [
        \T_VARIABLE                 => '/* testSeriouslyNestedMethod */',
        \T_RETURN                   => '/* testDeepestNested */',
        \T_ECHO                     => '/* testInException */',
        \T_CONSTANT_ENCAPSED_STRING => '/* testInDefault */',
    ];

    /**
     * List of all the condition markers in the test case file.
     *
     * @var string[]
     */
    protected $conditionMarkers = [
        '/* condition 0: namespace */',
        '/* condition 1: if */',
        '/* condition 2: function */',
        '/* condition 3-1: if */',
        '/* condition 3-2: else */',
        '/* condition 4: if */',
        '/* condition 5: nested class */',
        '/* condition 6: class method */',
        '/* condition 7: switch */',
        '/* condition 8a: case */',
        '/* condition 9: while */',
        '/* condition 10-1: if */',
        '/* condition 11-1: nested anonymous class */',
        '/* condition 12: nested anonymous class method */',
        '/* condition 13: closure */',
        '/* condition 10-2: elseif */',
        '/* condition 10-3: foreach */',
        '/* condition 11-2: try */',
        '/* condition 11-3: catch */',
        '/* condition 11-4: finally */',
        '/* condition 8b: default */',
    ];

    /**
     * Base array with all the scope opening tokens.
     *
     * This array is merged with expected result arrays for various unit tests
     * to make sure all possible conditions are tested.
     *
     * This array should be kept in sync with the Tokens::$scopeOpeners array.
     * This array isn't auto-generated based on the array in Tokens as for these
     * tests we want to have access to the token constant names, not just their values.
     *
     * @var array<string, bool>
     */
    protected $conditionDefaults = [
        'T_CLASS'      => false,
        'T_ANON_CLASS' => false,
        'T_INTERFACE'  => false,
        'T_TRAIT'      => false,
        'T_NAMESPACE'  => false,
        'T_FUNCTION'   => false,
        'T_CLOSURE'    => false,
        'T_IF'         => false,
        'T_SWITCH'     => false,
        'T_CASE'       => false,
        'T_DECLARE'    => false,
        'T_DEFAULT'    => false,
        'T_WHILE'      => false,
        'T_ELSE'       => false,
        'T_ELSEIF'     => false,
        'T_FOR'        => false,
        'T_FOREACH'    => false,
        'T_DO'         => false,
        'T_TRY'        => false,
        'T_CATCH'      => false,
        'T_FINALLY'    => false,
        'T_PROPERTY'   => false,
        'T_OBJECT'     => false,
        'T_USE'        => false,
    ];

    /**
     * Cache for the test token stack pointers.
     *
     * @var array<string, int>
     */
    protected static $testTokens = [];

    /**
     * Cache for the marker token stack pointers.
     *
     * @var array<string, int>
     */
    protected static $markerTokens = [];

    /**
     * OO scope tokens array.
     *
     * @var array<int|string, int>
     */
    protected $ooScopeTokens = [];

    /**
     * Set up the token position caches for the tests.
     *
     * Retrieves the test tokens and marker token stack pointer positions
     * only once and caches them as they won't change between the tests anyway.
     *
     * @before
     *
     * @return void
     */
    protected function setUpCaches()
    {
        if (empty(self::$testTokens) === true) {
            foreach (self::$testTargets as $targetToken => $marker) {
                self::$testTokens[$marker] = $this->getTargetToken($marker, $targetToken);
            }
        }

        if (empty(self::$markerTokens) === true) {
            foreach ($this->conditionMarkers as $marker) {
                self::$markerTokens[$marker] = $this->getTargetToken($marker, Tokens::$scopeOpeners);
            }
        }

        $this->ooScopeTokens = BCTokens::ooScopeTokens();
    }

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $testClass = static::TEST_CLASS;

        $result = $testClass::getCondition(self::$phpcsFile, 100000, $this->ooScopeTokens);
        $this->assertFalse($result);

        $result = $testClass::hasCondition(self::$phpcsFile, 100000, \T_IF);
        $this->assertFalse($result);
    }

    /**
     * Test passing a non conditional token.
     *
     * @return void
     */
    public function testNonConditionalToken()
    {
        $targetType = \T_STRING;
        if (parent::usesPhp8NameTokens() === true) {
            $targetType = \T_NAME_QUALIFIED;
        }

        $testClass = static::TEST_CLASS;
        $stackPtr  = $this->getTargetToken('/* testStartPoint */', $targetType);

        $result = $testClass::getCondition(self::$phpcsFile, $stackPtr, \T_IF);
        $this->assertFalse($result);

        $result = $testClass::hasCondition(self::$phpcsFile, $stackPtr, $this->ooScopeTokens);
        $this->assertFalse($result);
    }

    /**
     * Test retrieving a specific condition from a tokens "conditions" array.
     *
     * @dataProvider dataGetCondition
     *
     * @param string $testMarker      The comment which prefaces the target token in the test file.
     * @param array  $expectedResults Array with the condition token type to search for as key
     *                                and the marker for the expected stack pointer result as a value.
     *
     * @return void
     */
    public function testGetCondition($testMarker, $expectedResults)
    {
        $testClass = static::TEST_CLASS;
        $stackPtr  = self::$testTokens[$testMarker];

        // Add expected results for all test markers not listed in the data provider.
        $expectedResults += $this->conditionDefaults;

        foreach ($expectedResults as $conditionType => $expected) {
            if ($expected !== false) {
                $expected = self::$markerTokens[$expected];
            }

            $result = $testClass::getCondition(self::$phpcsFile, $stackPtr, \constant($conditionType));
            $this->assertSame(
                $expected,
                $result,
                "Assertion failed for test marker '{$testMarker}' with condition {$conditionType}"
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
     * @see testGetCondition() For the array format.
     *
     * @return array
     */
    public static function dataGetCondition()
    {
        return [
            'testSeriouslyNestedMethod' => [
                '/* testSeriouslyNestedMethod */',
                [
                    'T_CLASS'     => '/* condition 5: nested class */',
                    'T_NAMESPACE' => '/* condition 0: namespace */',
                    'T_FUNCTION'  => '/* condition 2: function */',
                    'T_IF'        => '/* condition 1: if */',
                    'T_ELSE'      => '/* condition 3-2: else */',
                ],
            ],
            'testDeepestNested' => [
                '/* testDeepestNested */',
                [
                    'T_CLASS'      => '/* condition 5: nested class */',
                    'T_ANON_CLASS' => '/* condition 11-1: nested anonymous class */',
                    'T_NAMESPACE'  => '/* condition 0: namespace */',
                    'T_FUNCTION'   => '/* condition 2: function */',
                    'T_CLOSURE'    => '/* condition 13: closure */',
                    'T_IF'         => '/* condition 1: if */',
                    'T_SWITCH'     => '/* condition 7: switch */',
                    'T_CASE'       => '/* condition 8a: case */',
                    'T_WHILE'      => '/* condition 9: while */',
                    'T_ELSE'       => '/* condition 3-2: else */',
                ],
            ],
            'testInException' => [
                '/* testInException */',
                [
                    'T_CLASS'     => '/* condition 5: nested class */',
                    'T_NAMESPACE' => '/* condition 0: namespace */',
                    'T_FUNCTION'  => '/* condition 2: function */',
                    'T_IF'        => '/* condition 1: if */',
                    'T_SWITCH'    => '/* condition 7: switch */',
                    'T_CASE'      => '/* condition 8a: case */',
                    'T_WHILE'     => '/* condition 9: while */',
                    'T_ELSE'      => '/* condition 3-2: else */',
                    'T_FOREACH'   => '/* condition 10-3: foreach */',
                    'T_CATCH'     => '/* condition 11-3: catch */',
                ],
            ],
            'testInDefault' => [
                '/* testInDefault */',
                [
                    'T_CLASS'     => '/* condition 5: nested class */',
                    'T_NAMESPACE' => '/* condition 0: namespace */',
                    'T_FUNCTION'  => '/* condition 2: function */',
                    'T_IF'        => '/* condition 1: if */',
                    'T_SWITCH'    => '/* condition 7: switch */',
                    'T_DEFAULT'   => '/* condition 8b: default */',
                    'T_ELSE'      => '/* condition 3-2: else */',
                ],
            ],
        ];
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
        $testClass = static::TEST_CLASS;
        $stackPtr  = self::$testTokens[$testMarker];

        // Add expected results for all test markers not listed in the data provider.
        $expectedResults += $this->conditionDefaults;

        foreach ($expectedResults as $conditionType => $expected) {
            if ($expected !== false) {
                $expected = self::$markerTokens[$expected];
            }

            $result = $testClass::getCondition(self::$phpcsFile, $stackPtr, \constant($conditionType), false);
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
     * Test whether a token has a condition of a certain type.
     *
     * @dataProvider dataHasCondition
     *
     * @param string $testMarker      The comment which prefaces the target token in the test file.
     * @param array  $expectedResults Array with the condition token type to search for as key
     *                                and the expected result as a value.
     *
     * @return void
     */
    public function testHasCondition($testMarker, $expectedResults)
    {
        $testClass = static::TEST_CLASS;
        $stackPtr  = self::$testTokens[$testMarker];

        // Add expected results for all test markers not listed in the data provider.
        $expectedResults += $this->conditionDefaults;

        foreach ($expectedResults as $conditionType => $expected) {
            $result = $testClass::hasCondition(self::$phpcsFile, $stackPtr, \constant($conditionType));
            $this->assertSame(
                $expected,
                $result,
                "Assertion failed for test marker '{$testMarker}' with condition {$conditionType}"
            );
        }
    }

    /**
     * Data Provider.
     *
     * Only list the "true" conditions in the $results array.
     * All other potential conditions will automatically also be tested
     * and will expect "false" as a result.
     *
     * @see testHasCondition() For the array format.
     *
     * @return array
     */
    public static function dataHasCondition()
    {
        return [
            'testSeriouslyNestedMethod' => [
                '/* testSeriouslyNestedMethod */',
                [
                    'T_CLASS'     => true,
                    'T_NAMESPACE' => true,
                    'T_FUNCTION'  => true,
                    'T_IF'        => true,
                    'T_ELSE'      => true,
                ],
            ],
            'testDeepestNested' => [
                '/* testDeepestNested */',
                [
                    'T_CLASS'      => true,
                    'T_ANON_CLASS' => true,
                    'T_NAMESPACE'  => true,
                    'T_FUNCTION'   => true,
                    'T_CLOSURE'    => true,
                    'T_IF'         => true,
                    'T_SWITCH'     => true,
                    'T_CASE'       => true,
                    'T_WHILE'      => true,
                    'T_ELSE'       => true,
                ],
            ],
            'testInException' => [
                '/* testInException */',
                [
                    'T_CLASS'     => true,
                    'T_NAMESPACE' => true,
                    'T_FUNCTION'  => true,
                    'T_IF'        => true,
                    'T_SWITCH'    => true,
                    'T_CASE'      => true,
                    'T_WHILE'     => true,
                    'T_ELSE'      => true,
                    'T_FOREACH'   => true,
                    'T_CATCH'     => true,
                ],
            ],
            'testInDefault' => [
                '/* testInDefault */',
                [
                    'T_CLASS'     => true,
                    'T_NAMESPACE' => true,
                    'T_FUNCTION'  => true,
                    'T_IF'        => true,
                    'T_SWITCH'    => true,
                    'T_DEFAULT'   => true,
                    'T_ELSE'      => true,
                ],
            ],
        ];
    }

    /**
     * Test whether a token has a condition of a certain type, with multiple allowed possibilities.
     *
     * @return void
     */
    public function testHasConditionMultipleTypes()
    {
        $testClass = static::TEST_CLASS;
        $stackPtr  = self::$testTokens['/* testInException */'];

        $result = $testClass::hasCondition(self::$phpcsFile, $stackPtr, [\T_TRY, \T_FINALLY]);
        $this->assertFalse(
            $result,
            'Failed asserting that "testInException" does not have a "try" nor a "finally" condition'
        );

        $result = $testClass::hasCondition(self::$phpcsFile, $stackPtr, [\T_TRY, \T_CATCH, \T_FINALLY]);
        $this->assertTrue(
            $result,
            'Failed asserting that "testInException" has a "try", "catch" or "finally" condition'
        );

        $stackPtr = self::$testTokens['/* testSeriouslyNestedMethod */'];

        $result = $testClass::hasCondition(self::$phpcsFile, $stackPtr, [\T_ANON_CLASS, \T_CLOSURE]);
        $this->assertFalse(
            $result,
            'Failed asserting that "testSeriouslyNestedMethod" does not have an anonymous class nor a closure condition'
        );

        $result = $testClass::hasCondition(self::$phpcsFile, $stackPtr, $this->ooScopeTokens);
        $this->assertTrue(
            $result,
            'Failed asserting that "testSeriouslyNestedMethod" has an OO Scope token condition'
        );
    }
}
