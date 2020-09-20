<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Parentheses;

use PHPCSUtils\BackCompat\BCTokens;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\Parentheses;

/**
 * Tests for the \PHPCSUtils\Utils\Parentheses class.
 *
 * @covers \PHPCSUtils\Utils\Parentheses
 *
 * @group parentheses
 *
 * @since 1.0.0
 */
class ParenthesesTest extends UtilityMethodTestCase
{

    /**
     * List of all the test markers with their target token info in the test case file.
     *
     * @var array
     */
    public static $testTargets = [
        'testIfWithArray-$a' => [
            'marker'  => '/* testIfWithArray */',
            'code'    => \T_VARIABLE,
            'content' => '$a',
        ],
        'testIfWithArray-array' => [
            'marker' => '/* testIfWithArray */',
            'code'   => \T_ARRAY,
        ],
        'testIfWithArray-$c' => [
            'marker'  => '/* testIfWithArray */',
            'code'    => \T_VARIABLE,
            'content' => '$c',
        ],
        'testElseIfWithClosure-$a' => [
            'marker'  => '/* testElseIfWithClosure */',
            'code'    => \T_VARIABLE,
            'content' => '$a',
        ],
        'testElseIfWithClosure-closure' => [
            'marker' => '/* testElseIfWithClosure */',
            'code'   => \T_CLOSURE,
        ],
        'testElseIfWithClosure-$array' => [
            'marker'  => '/* testElseIfWithClosure */',
            'code'    => \T_VARIABLE,
            'content' => '$array',
        ],
        'testForeach-45' => [
            'marker'  => '/* testForeach */',
            'code'    => \T_LNUMBER,
            'content' => '45',
        ],
        'testForeach-$a' => [
            'marker'  => '/* testForeach */',
            'code'    => \T_VARIABLE,
            'content' => '$a',
        ],
        'testForeach-$c' => [
            'marker'  => '/* testForeach */',
            'code'    => \T_VARIABLE,
            'content' => '$c',
        ],
        'testFunctionwithArray-$param' => [
            'marker'  => '/* testFunctionwithArray */',
            'code'    => \T_VARIABLE,
            'content' => '$param',
        ],
        'testFunctionwithArray-2' => [
            'marker'  => '/* testFunctionwithArray */',
            'code'    => \T_LNUMBER,
            'content' => '2',
        ],
        'testForWithTernary-$a' => [
            'marker'  => '/* testForWithTernary */',
            'code'    => \T_VARIABLE,
            'content' => '$a',
        ],
        'testForWithTernary-$c' => [
            'marker'  => '/* testForWithTernary */',
            'code'    => \T_VARIABLE,
            'content' => '$c',
        ],
        'testForWithTernary-$array' => [
            'marker'  => '/* testForWithTernary */',
            'code'    => \T_VARIABLE,
            'content' => '$array',
        ],
        'testWhileWithClosure-$a' => [
            'marker'  => '/* testWhileWithClosure */',
            'code'    => \T_VARIABLE,
            'content' => '$a',
        ],
        'testWhileWithClosure-$p' => [
            'marker'  => '/* testWhileWithClosure */',
            'code'    => \T_VARIABLE,
            'content' => '$p',
        ],
        'testWhileWithClosure-$result' => [
            'marker'  => '/* testWhileWithClosure */',
            'code'    => \T_VARIABLE,
            'content' => '$result',
        ],
        'testAnonClass-implements' => [
            'marker' => '/* testAnonClass */',
            'code'   => \T_IMPLEMENTS,
        ],
        'testAnonClass-$param' => [
            'marker'  => '/* testAnonClass */',
            'code'    => \T_VARIABLE,
            'content' => '$param',
        ],
        'testAnonClass-$e' => [
            'marker'  => '/* testAnonClass */',
            'code'    => \T_VARIABLE,
            'content' => '$e',
        ],
        'testAnonClass-$a' => [
            'marker'  => '/* testAnonClass */',
            'code'    => \T_VARIABLE,
            'content' => '$a',
        ],
        'testArrowFunction-$param' => [
            'marker'  => '/* testArrayFunctionCallWithArrowFunctionParam */',
            'code'    => \T_VARIABLE,
            'content' => '$param',
        ],
        'testArrowFunction-get' => [
            'marker'  => '/* testArrayFunctionCallWithArrowFunctionParam */',
            'code'    => \T_STRING,
            'content' => 'get',
        ],
        'testMethodCalledFn-true' => [
            'marker'  => '/* testFunctionCallFnPHPCS353-354 */',
            'code'    => \T_TRUE,
        ],
        'testArrowFunctionReturnByRef' => [
            'marker'  => '/* testArrowFunctionByReference */',
            'code'    => \T_VARIABLE,
            'content' => '$x',
        ],
        'testIfIsset-$b' => [
            'marker'  => '/* testIfWithIssetEmpty */',
            'code'    => \T_VARIABLE,
            'content' => '$b',
        ],
        'testIfEmpty-$c' => [
            'marker'  => '/* testIfWithIssetEmpty */',
            'code'    => \T_VARIABLE,
            'content' => '$c',
        ],
        'testUnset-->' => [
            'marker'  => '/* testUnset */',
            'code'    => \T_OBJECT_OPERATOR,
        ],
        'testUnsetParenthesis' => [
            'marker'  => '/* testUnset */',
            'code'    => \T_OPEN_PARENTHESIS,
        ],
        'testEval-concat' => [
            'marker'  => '/* testEval */',
            'code'    => \T_STRING_CONCAT,
        ],
        'testIfExitDie-boolean-or' => [
            'marker'  => '/* testExit */',
            'code'    => \T_BOOLEAN_OR,
        ],
        'testIfExitDie-message' => [
            'marker'  => '/* testExit */',
            'code'    => \T_CONSTANT_ENCAPSED_STRING,
            'content' => "'message'",
        ],
        'testParseError-1' => [
            'marker'  => '/* testParseError */',
            'code'    => \T_LNUMBER,
            'content' => '1',
        ],
    ];

    /**
     * Cache for the test token stack pointers.
     *
     * @var array <string> => <int>
     */
    private static $testTokens = [];

    /**
     * Base array with all the tokens which are assigned parenthesis owners.
     *
     * This array is merged with expected result arrays for various unit tests
     * to make sure all possible parentheses owners are tested.
     *
     * This array should be kept in sync with the Tokens::$parenthesisOpeners array
     * + the extra tokens the Parentheses class allows for.
     *
     * This array isn't auto-generated based on the array in Tokens as for these
     * tests we want to have access to the token constant names, not just their values.
     *
     * @var array <string> => <bool>
     */
    private $ownerDefaults = [
        'T_ARRAY'      => false,
        'T_LIST'       => false,
        'T_FUNCTION'   => false,
        'T_CLOSURE'    => false,
        'T_ANON_CLASS' => false,
        'T_WHILE'      => false,
        'T_FOR'        => false,
        'T_FOREACH'    => false,
        'T_SWITCH'     => false,
        'T_IF'         => false,
        'T_ELSEIF'     => false,
        'T_CATCH'      => false,
        'T_DECLARE'    => false,
        'T_FN'         => false,

        // Extra tokens.
        'T_ISSET'      => false,
        'T_UNSET'      => false,
        'T_EMPTY'      => false,
        'T_EXIT'       => false,
        'T_EVAL'       => false,
    ];

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
            foreach (self::$testTargets as $testName => $targetDetails) {
                if (isset($targetDetails['content']) === true) {
                    self::$testTokens[$testName] = $this->getTargetToken(
                        $targetDetails['marker'],
                        $targetDetails['code'],
                        $targetDetails['content']
                    );
                } else {
                    self::$testTokens[$testName] = $this->getTargetToken(
                        $targetDetails['marker'],
                        $targetDetails['code']
                    );
                }
            }
        }
    }

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $result = Parentheses::getOwner(self::$phpcsFile, 100000);
        $this->assertFalse($result);

        $result = Parentheses::isOwnerIn(self::$phpcsFile, 100000, \T_FUNCTION);
        $this->assertFalse($result);

        $result = Parentheses::hasOwner(self::$phpcsFile, 100000, \T_FOR);
        $this->assertFalse($result);
    }

    /**
     * Test passing a token which isn't in parentheses.
     *
     * @return void
     */
    public function testNoParentheses()
    {
        $stackPtr = $this->getTargetToken('/* testNoParentheses */', \T_VARIABLE);

        $result = Parentheses::getOwner(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result);

        $result = Parentheses::isOwnerIn(self::$phpcsFile, $stackPtr, \T_IF);
        $this->assertFalse($result);

        $result = Parentheses::hasOwner(self::$phpcsFile, $stackPtr, \T_FOREACH);
        $this->assertFalse($result);

        $result = Parentheses::getFirstOpener(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result);

        $result = Parentheses::getFirstCloser(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result);

        $result = Parentheses::getFirstOwner(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result);

        $result = Parentheses::firstOwnerIn(self::$phpcsFile, $stackPtr, [\T_FUNCTION, \T_CLOSURE]);
        $this->assertFalse($result);

        $result = Parentheses::getLastOpener(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result);

        $result = Parentheses::getLastCloser(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result);

        $result = Parentheses::getLastOwner(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result);

        $result = Parentheses::lastOwnerIn(self::$phpcsFile, $stackPtr, [\T_FUNCTION, \T_CLOSURE]);
        $this->assertFalse($result);
    }

    /**
     * Test passing a non-parenthesis token to methods which expect to receive an open/close parenthesis.
     *
     * @return void
     */
    public function testPassingNonParenthesisTokenToMethodsWhichExpectParenthesis()
    {
        $stackPtr = self::$testTokens['testIfWithArray-$a'];

        $result = Parentheses::getOwner(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result);

        $result = Parentheses::isOwnerIn(self::$phpcsFile, $stackPtr, \T_IF);
        $this->assertFalse($result);
    }

    /**
     * Test passing an open parenthesis token to methods which expect to receive an open/close parenthesis.
     *
     * @return void
     */
    public function testPassingParenthesisTokenToMethodsWhichExpectParenthesisOpen()
    {
        $stackPtr = (self::$testTokens['testIfWithArray-$c'] - 1);

        $result = Parentheses::getOwner(self::$phpcsFile, $stackPtr);
        $this->assertSame(($stackPtr - 1), $result);

        $result = Parentheses::isOwnerIn(self::$phpcsFile, $stackPtr, \T_IF);
        $this->assertFalse($result);

        $result = Parentheses::isOwnerIn(self::$phpcsFile, $stackPtr, \T_ARRAY);
        $this->assertTrue($result);
    }

    /**
     * Test passing a close parenthesis token to methods which expect to receive an open/close parenthesis.
     *
     * @return void
     */
    public function testPassingParenthesisTokenToMethodsWhichExpectParenthesisClose()
    {
        $stackPtr = (self::$testTokens['testForeach-$c'] + 1);

        $result = Parentheses::getOwner(self::$phpcsFile, $stackPtr);
        $this->assertSame(($stackPtr - 6), $result);

        $result = Parentheses::isOwnerIn(self::$phpcsFile, $stackPtr, \T_IF);
        $this->assertFalse($result);

        $result = Parentheses::isOwnerIn(self::$phpcsFile, $stackPtr, \T_LIST);
        $this->assertTrue($result);
    }

    /**
     * Test passing a close parenthesis token to methods which expect to receive an open/close parenthesis.
     *
     * This specifically tests the BC-layer for lists and anon classes.
     *
     * @return void
     */
    public function testPassingParenthesisCloseHandlingInBCLayer()
    {
        $stackPtr = $this->getTargetToken('/* testListOnCloseParens */', \T_CLOSE_PARENTHESIS);

        $result = Parentheses::getOwner(self::$phpcsFile, $stackPtr);
        $this->assertSame(($stackPtr - 6), $result);

        $result = Parentheses::isOwnerIn(self::$phpcsFile, $stackPtr, \T_LIST);
        $this->assertTrue($result);

        $result = Parentheses::isOwnerIn(self::$phpcsFile, $stackPtr, \T_IF);
        $this->assertFalse($result);

        $stackPtr = $this->getTargetToken('/* testNoOwnerOnCloseParens */', \T_CLOSE_PARENTHESIS);

        $result = Parentheses::getOwner(self::$phpcsFile, $stackPtr);
        $this->assertFalse($result);

        $result = Parentheses::isOwnerIn(self::$phpcsFile, $stackPtr, BCTokens::scopeOpeners());
        $this->assertFalse($result);
    }

    /**
     * Test that a function named fn sees the T_FUNCTION token as owner, not the T_FN token.
     *
     * This specifically tests the BC-layer for arrow functions.
     *
     * @return void
     */
    public function testFunctionNamedFnKeywordNotParenthesesOwner()
    {
        $stackPtr = $this->getTargetToken('/* testFunctionNamedFn */', \T_OPEN_PARENTHESIS);

        $result = Parentheses::getOwner(self::$phpcsFile, $stackPtr);
        $this->assertSame(($stackPtr - 3), $result);
    }

    /**
     * Test correctly retrieving the first parenthesis opener for an arbitrary token.
     *
     * @dataProvider dataWalkParentheses
     *
     * @param string $testName        The name of this test as set in the cached $testTokens array.
     * @param array  $expectedResults Expected function output for the various functions.
     *
     * @return void
     */
    public function testGetFirstOpener($testName, $expectedResults)
    {
        $stackPtr = self::$testTokens[$testName];

        $result   = Parentheses::getFirstOpener(self::$phpcsFile, $stackPtr);
        $expected = $expectedResults['firstOpener'];
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $this->assertSame($expected, $result, 'Assertion without owners failed');

        $result   = Parentheses::getFirstOpener(self::$phpcsFile, $stackPtr, BCTokens::scopeOpeners());
        $expected = $expectedResults['firstScopeOwnerOpener'];
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $this->assertSame($expected, $result, 'Assertion with $validOwners failed');
    }

    /**
     * Test correctly retrieving the first parenthesis closer for an arbitrary token.
     *
     * @dataProvider dataWalkParentheses
     *
     * @param string $testName        The name of this test as set in the cached $testTokens array.
     * @param array  $expectedResults Expected function output for the various functions.
     *
     * @return void
     */
    public function testGetFirstCloser($testName, $expectedResults)
    {
        $stackPtr = self::$testTokens[$testName];

        $result   = Parentheses::getFirstCloser(self::$phpcsFile, $stackPtr);
        $expected = $expectedResults['firstCloser'];
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $this->assertSame($expected, $result, 'Assertion without owners failed');

        $result   = Parentheses::getFirstCloser(self::$phpcsFile, $stackPtr, BCTokens::scopeOpeners());
        $expected = $expectedResults['firstScopeOwnerCloser'];
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $this->assertSame($expected, $result, 'Assertion with $validOwners failed');
    }

    /**
     * Test correctly retrieving the first parenthesis owner for an arbitrary token.
     *
     * @dataProvider dataWalkParentheses
     *
     * @param string $testName        The name of this test as set in the cached $testTokens array.
     * @param array  $expectedResults Expected function output for the various functions.
     *
     * @return void
     */
    public function testGetFirstOwner($testName, $expectedResults)
    {
        $stackPtr = self::$testTokens[$testName];

        $result   = Parentheses::getFirstOwner(self::$phpcsFile, $stackPtr);
        $expected = $expectedResults['firstOwner'];
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $this->assertSame($expected, $result, 'Assertion without owners failed');

        $result   = Parentheses::getFirstOwner(self::$phpcsFile, $stackPtr, BCTokens::scopeOpeners());
        $expected = $expectedResults['firstScopeOwnerOwner'];
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $this->assertSame($expected, $result, 'Assertion with $validOwners failed');
    }

    /**
     * Test correctly retrieving the last parenthesis opener for an arbitrary token.
     *
     * @dataProvider dataWalkParentheses
     *
     * @param string $testName        The name of this test as set in the cached $testTokens array.
     * @param array  $expectedResults Expected function output for the various functions.
     *
     * @return void
     */
    public function testGetLastOpener($testName, $expectedResults)
    {
        $stackPtr = self::$testTokens[$testName];

        $result   = Parentheses::getLastOpener(self::$phpcsFile, $stackPtr);
        $expected = $expectedResults['lastOpener'];
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $this->assertSame($expected, $result, 'Assertion without owners failed');

        $result   = Parentheses::getLastOpener(self::$phpcsFile, $stackPtr, [\T_ARRAY]);
        $expected = $expectedResults['lastArrayOpener'];
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $this->assertSame($expected, $result, 'Assertion with $validOwners failed');
    }

    /**
     * Test correctly retrieving the last parenthesis closer for an arbitrary token.
     *
     * @dataProvider dataWalkParentheses
     *
     * @param string $testName        The name of this test as set in the cached $testTokens array.
     * @param array  $expectedResults Expected function output for the various functions.
     *
     * @return void
     */
    public function testGetLastCloser($testName, $expectedResults)
    {
        $stackPtr = self::$testTokens[$testName];

        $result   = Parentheses::getLastCloser(self::$phpcsFile, $stackPtr);
        $expected = $expectedResults['lastCloser'];
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $this->assertSame($expected, $result, 'Assertion without owners failed');

        $result   = Parentheses::getLastCloser(self::$phpcsFile, $stackPtr, [\T_FUNCTION, \T_CLOSURE]);
        $expected = $expectedResults['lastFunctionCloser'];
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $this->assertSame($expected, $result, 'Assertion with $validOwners failed');
    }

    /**
     * Test correctly retrieving the last parenthesis owner for an arbitrary token.
     *
     * @dataProvider dataWalkParentheses
     *
     * @param string $testName        The name of this test as set in the cached $testTokens array.
     * @param array  $expectedResults Expected function output for the various functions.
     *
     * @return void
     */
    public function testGetLastOwner($testName, $expectedResults)
    {
        $stackPtr = self::$testTokens[$testName];

        $result   = Parentheses::getLastOwner(self::$phpcsFile, $stackPtr);
        $expected = $expectedResults['lastOwner'];
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $this->assertSame($expected, $result, 'Assertion without owners failed');

        $result   = Parentheses::getLastOwner(self::$phpcsFile, $stackPtr, [\T_IF, \T_ELSEIF, \T_ELSE]);
        $expected = $expectedResults['lastIfElseOwner'];
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $this->assertSame($expected, $result, 'Assertion with $validOwners failed');
    }

    /**
     * Data provider.
     *
     * @see testGetFirstOpener()  For the array format.
     * @see testGetFirstCloser(() For the array format.
     * @see testGetFirstOwner()   For the array format.
     * @see testGetLastOpener()   For the array format.
     * @see testGetLastCloser()   For the array format.
     * @see testGetLastOwner()    For the array format.
     *
     * @return array
     */
    public function dataWalkParentheses()
    {
        $data = [
            'testIfWithArray-$a' => [
                'testIfWithArray-$a',
                [
                    'firstOpener'           => -2,
                    'firstCloser'           => 19,
                    'firstOwner'            => -4,
                    'firstScopeOwnerOpener' => -2,
                    'firstScopeOwnerCloser' => 19,
                    'firstScopeOwnerOwner'  => -4,
                    'lastOpener'            => -1,
                    'lastCloser'            => 5,
                    'lastOwner'             => false,
                    'lastArrayOpener'       => false,
                    'lastFunctionCloser'    => false,
                    'lastIfElseOwner'       => -4,
                ],
            ],
            'testIfWithArray-array' => [
                'testIfWithArray-array',
                [
                    'firstOpener'           => -13,
                    'firstCloser'           => 8,
                    'firstOwner'            => -15,
                    'firstScopeOwnerOpener' => -13,
                    'firstScopeOwnerCloser' => 8,
                    'firstScopeOwnerOwner'  => -15,
                    'lastOpener'            => -1,
                    'lastCloser'            => 7,
                    'lastOwner'             => false,
                    'lastArrayOpener'       => false,
                    'lastFunctionCloser'    => false,
                    'lastIfElseOwner'       => -15,
                ],
            ],
            'testIfWithArray-$c' => [
                'testIfWithArray-$c',
                [
                    'firstOpener'           => -15,
                    'firstCloser'           => 6,
                    'firstOwner'            => -17,
                    'firstScopeOwnerOpener' => -15,
                    'firstScopeOwnerCloser' => 6,
                    'firstScopeOwnerOwner'  => -17,
                    'lastOpener'            => -1,
                    'lastCloser'            => 4,
                    'lastOwner'             => -2,
                    'lastArrayOpener'       => -1,
                    'lastFunctionCloser'    => false,
                    'lastIfElseOwner'       => -17,
                ],
            ],
            'testWhileWithClosure-$a' => [
                'testWhileWithClosure-$a',
                [
                    'firstOpener'           => -9,
                    'firstCloser'           => 30,
                    'firstOwner'            => -11,
                    'firstScopeOwnerOpener' => -9,
                    'firstScopeOwnerCloser' => 30,
                    'firstScopeOwnerOwner'  => -11,
                    'lastOpener'            => -2,
                    'lastCloser'            => 2,
                    'lastOwner'             => false,
                    'lastArrayOpener'       => false,
                    'lastFunctionCloser'    => false,
                    'lastIfElseOwner'       => false,
                ],
            ],
            'testWhileWithClosure-$p' => [
                'testWhileWithClosure-$p',
                [
                    'firstOpener'           => -24,
                    'firstCloser'           => 15,
                    'firstOwner'            => -26,
                    'firstScopeOwnerOpener' => -24,
                    'firstScopeOwnerCloser' => 15,
                    'firstScopeOwnerOwner'  => -26,
                    'lastOpener'            => -1,
                    'lastCloser'            => 1,
                    'lastOwner'             => -2,
                    'lastArrayOpener'       => false,
                    'lastFunctionCloser'    => 1,
                    'lastIfElseOwner'       => false,
                ],
            ],
            'testWhileWithClosure-$result' => [
                'testWhileWithClosure-$result',
                [
                    'firstOpener'           => -2,
                    'firstCloser'           => 37,
                    'firstOwner'            => -4,
                    'firstScopeOwnerOpener' => -2,
                    'firstScopeOwnerCloser' => 37,
                    'firstScopeOwnerOwner'  => -4,
                    'lastOpener'            => -1,
                    'lastCloser'            => 10,
                    'lastOwner'             => false,
                    'lastArrayOpener'       => false,
                    'lastFunctionCloser'    => false,
                    'lastIfElseOwner'       => false,
                ],
            ],
            'testArrowFunction-$param' => [
                'testArrowFunction-$param',
                [
                    'firstOpener'           => -10,
                    'firstCloser'           => 11,
                    'firstOwner'            => -11,
                    'firstScopeOwnerOpener' => false,
                    'firstScopeOwnerCloser' => false,
                    'firstScopeOwnerOwner'  => false,
                    'lastOpener'            => -1,
                    'lastCloser'            => 1,
                    'lastOwner'             => -2,
                    'lastArrayOpener'       => -10,
                    'lastFunctionCloser'    => false,
                    'lastIfElseOwner'       => false,
                ],
            ],
            'testArrowFunction-get' => [
                'testArrowFunction-get',
                [
                    'firstOpener'           => -17,
                    'firstCloser'           => 4,
                    'firstOwner'            => -18,
                    'firstScopeOwnerOpener' => false,
                    'firstScopeOwnerCloser' => false,
                    'firstScopeOwnerOwner'  => false,
                    'lastOpener'            => -13,
                    'lastCloser'            => 3,
                    'lastOwner'             => false,
                    'lastArrayOpener'       => -17,
                    'lastFunctionCloser'    => false,
                    'lastIfElseOwner'       => false,
                ],
            ],
            'testMethodCalledFn-true' => [
                'testMethodCalledFn-true',
                [
                    'firstOpener'           => -1,
                    'firstCloser'           => 1,
                    'firstOwner'            => false,
                    'firstScopeOwnerOpener' => false,
                    'firstScopeOwnerCloser' => false,
                    'firstScopeOwnerOwner'  => false,
                    'lastOpener'            => -1,
                    'lastCloser'            => 1,
                    'lastOwner'             => false,
                    'lastArrayOpener'       => false,
                    'lastFunctionCloser'    => false,
                    'lastIfElseOwner'       => false,
                ],
            ],
            'testArrowFunctionReturnByRef' => [
                'testArrowFunctionReturnByRef',
                [
                    'firstOpener'           => -1,
                    'firstCloser'           => 1,
                    'firstOwner'            => -4,
                    'firstScopeOwnerOpener' => false,
                    'firstScopeOwnerCloser' => false,
                    'firstScopeOwnerOwner'  => false,
                    'lastOpener'            => -1,
                    'lastCloser'            => 1,
                    'lastOwner'             => -4,
                    'lastArrayOpener'       => false,
                    'lastFunctionCloser'    => false,
                    'lastIfElseOwner'       => false,
                ],
            ],
            'testIfIsset-$b' => [
                'testIfIsset-$b',
                [
                    'firstOpener'           => -8,
                    'firstCloser'           => 14,
                    'firstOwner'            => -10,
                    'firstScopeOwnerOpener' => -8,
                    'firstScopeOwnerCloser' => 14,
                    'firstScopeOwnerOwner'  => -10,
                    'lastOpener'            => -5,
                    'lastCloser'            => 2,
                    'lastOwner'             => -6,
                    'lastArrayOpener'       => false,
                    'lastFunctionCloser'    => false,
                    'lastIfElseOwner'       => -10,
                ],
            ],
            'testIfEmpty-$c' => [
                'testIfEmpty-$c',
                [
                    'firstOpener'           => -19,
                    'firstCloser'           => 3,
                    'firstOwner'            => -21,
                    'firstScopeOwnerOpener' => -19,
                    'firstScopeOwnerCloser' => 3,
                    'firstScopeOwnerOwner'  => -21,
                    'lastOpener'            => -1,
                    'lastCloser'            => 1,
                    'lastOwner'             => -3,
                    'lastArrayOpener'       => false,
                    'lastFunctionCloser'    => false,
                    'lastIfElseOwner'       => -21,
                ],
            ],
            'testUnset-->' => [
                'testUnset-->',
                [
                    'firstOpener'           => -8,
                    'firstCloser'           => 2,
                    'firstOwner'            => -9,
                    'firstScopeOwnerOpener' => false,
                    'firstScopeOwnerCloser' => false,
                    'firstScopeOwnerOwner'  => false,
                    'lastOpener'            => -8,
                    'lastCloser'            => 2,
                    'lastOwner'             => -9,
                    'lastArrayOpener'       => false,
                    'lastFunctionCloser'    => false,
                    'lastIfElseOwner'       => false,
                ],
            ],
            'testUnsetParenthesis' => [
                'testUnsetParenthesis',
                [
                    'firstOpener'           => false,
                    'firstCloser'           => false,
                    'firstOwner'            => false,
                    'firstScopeOwnerOpener' => false,
                    'firstScopeOwnerCloser' => false,
                    'firstScopeOwnerOwner'  => false,
                    'lastOpener'            => false,
                    'lastCloser'            => false,
                    'lastOwner'             => false,
                    'lastArrayOpener'       => false,
                    'lastFunctionCloser'    => false,
                    'lastIfElseOwner'       => false,
                ],
            ],
            'testEval-concat' => [
                'testEval-concat',
                [
                    'firstOpener'           => -3,
                    'firstCloser'           => 8,
                    'firstOwner'            => -4,
                    'firstScopeOwnerOpener' => false,
                    'firstScopeOwnerCloser' => false,
                    'firstScopeOwnerOwner'  => false,
                    'lastOpener'            => -3,
                    'lastCloser'            => 8,
                    'lastOwner'             => -4,
                    'lastArrayOpener'       => false,
                    'lastFunctionCloser'    => false,
                    'lastIfElseOwner'       => false,
                ],
            ],
            'testIfExitDie-boolean-or' => [
                'testIfExitDie-boolean-or',
                [
                    'firstOpener'           => -6,
                    'firstCloser'           => 6,
                    'firstOwner'            => -8,
                    'firstScopeOwnerOpener' => -6,
                    'firstScopeOwnerCloser' => 6,
                    'firstScopeOwnerOwner'  => -8,
                    'lastOpener'            => -6,
                    'lastCloser'            => 6,
                    'lastOwner'             => -8,
                    'lastArrayOpener'       => false,
                    'lastFunctionCloser'    => false,
                    'lastIfElseOwner'       => -8,
                ],
            ],
            'testIfExitDie-message' => [
                'testIfExitDie-message',
                [
                    'firstOpener'           => -10,
                    'firstCloser'           => 2,
                    'firstOwner'            => -12,
                    'firstScopeOwnerOpener' => -10,
                    'firstScopeOwnerCloser' => 2,
                    'firstScopeOwnerOwner'  => -12,
                    'lastOpener'            => -1,
                    'lastCloser'            => 1,
                    'lastOwner'             => -2,
                    'lastArrayOpener'       => false,
                    'lastFunctionCloser'    => false,
                    'lastIfElseOwner'       => -12,
                ],
            ],
            'testParseError-1' => [
                'testParseError-1',
                [
                    'firstOpener'           => false,
                    'firstCloser'           => false,
                    'firstOwner'            => false,
                    'firstScopeOwnerOpener' => false,
                    'firstScopeOwnerCloser' => false,
                    'firstScopeOwnerOwner'  => false,
                    'lastOpener'            => false,
                    'lastCloser'            => false,
                    'lastOwner'             => false,
                    'lastArrayOpener'       => false,
                    'lastFunctionCloser'    => false,
                    'lastIfElseOwner'       => false,
                ],
            ],
        ];

        return $data;
    }

    /**
     * Test correctly determining whether a token has an owner of a certain type.
     *
     * @dataProvider dataHasOwner
     *
     * @param string $testName        The name of this test as set in the cached $testTokens array.
     * @param array  $expectedResults Array with the owner token type to search for as key
     *                                and the expected result as a value.
     *
     * @return void
     */
    public function testHasOwner($testName, $expectedResults)
    {
        $stackPtr = self::$testTokens[$testName];

        // Add expected results for all owner types not listed in the data provider.
        $expectedResults += $this->ownerDefaults;

        if (\defined('T_FN') === false) {
            $expectedResults['T_STRING'] = $expectedResults['T_FN'];
            unset($expectedResults['T_FN']);
        }

        foreach ($expectedResults as $ownerType => $expected) {
            $result = Parentheses::hasOwner(self::$phpcsFile, $stackPtr, \constant($ownerType));
            $this->assertSame(
                $expected,
                $result,
                "Assertion failed for test marker '{$testName}' with owner {$ownerType}"
            );
        }
    }

    /**
     * Data Provider.
     *
     * Only list the "true" owners in the $results array.
     * All other potential owners will automatically also be tested
     * and will expect "false" as a result.
     *
     * @see testHasOwner() For the array format.
     *
     * @return array
     */
    public function dataHasOwner()
    {
        return [
            'testIfWithArray-$a' => [
                'testIfWithArray-$a',
                ['T_IF' => true],
            ],

            'testIfWithArray-array' => [
                'testIfWithArray-array',
                ['T_IF' => true],
            ],
            'testIfWithArray-$c' => [
                'testIfWithArray-$c',
                [
                    'T_ARRAY' => true,
                    'T_IF'    => true,
                ],
            ],
            'testElseIfWithClosure-$a' => [
                'testElseIfWithClosure-$a',
                [
                    'T_CLOSURE' => true,
                    'T_ELSEIF'  => true,
                ],
            ],
            'testElseIfWithClosure-closure' => [
                'testElseIfWithClosure-closure',
                ['T_ELSEIF' => true],
            ],
            'testElseIfWithClosure-$array' => [
                'testElseIfWithClosure-$array',
                ['T_ELSEIF' => true],
            ],
            'testForeach-45' => [
                'testForeach-45',
                [
                    'T_ARRAY'   => true,
                    'T_FOREACH' => true,
                ],
            ],
            'testForeach-$a' => [
                'testForeach-$a',
                [
                    'T_LIST'    => true,
                    'T_FOREACH' => true,
                ],
            ],
            'testForeach-$c' => [
                'testForeach-$c',
                [
                    'T_LIST'    => true,
                    'T_FOREACH' => true,
                ],
            ],
            'testFunctionwithArray-$param' => [
                'testFunctionwithArray-$param',
                ['T_FUNCTION' => true],
            ],
            'testFunctionwithArray-2' => [
                'testFunctionwithArray-2',
                [
                    'T_ARRAY'    => true,
                    'T_FUNCTION' => true,
                ],
            ],
            'testForWithTernary-$a' => [
                'testForWithTernary-$a',
                ['T_FOR' => true],
            ],
            'testForWithTernary-$c' => [
                'testForWithTernary-$c',
                ['T_FOR' => true],
            ],
            'testForWithTernary-$array' => [
                'testForWithTernary-$array',
                ['T_FOR' => true],
            ],
            'testWhileWithClosure-$a' => [
                'testWhileWithClosure-$a',
                ['T_WHILE' => true],
            ],
            'testWhileWithClosure-$p' => [
                'testWhileWithClosure-$p',
                [
                    'T_CLOSURE' => true,
                    'T_WHILE'   => true,
                ],
            ],
            'testWhileWithClosure-$result' => [
                'testWhileWithClosure-$result',
                ['T_WHILE' => true],
            ],
            'testAnonClass-implements' => [
                'testAnonClass-implements',
                ['T_ANON_CLASS' => true],
            ],
            'testAnonClass-$param' => [
                'testAnonClass-$param',
                [
                    'T_ANON_CLASS' => true,
                    'T_FUNCTION'   => true,
                ],
            ],
            'testAnonClass-$e' => [
                'testAnonClass-$e',
                [
                    'T_ANON_CLASS' => true,
                    'T_CATCH'      => true,
                ],
            ],
            'testAnonClass-$a' => [
                'testAnonClass-$a',
                [
                    'T_ANON_CLASS' => true,
                    'T_WHILE'      => true,
                ],
            ],
            'testArrowFunction-$param' => [
                'testArrowFunction-$param',
                [
                    'T_ARRAY' => true,
                    'T_FN'    => true,
                ],
            ],
            'testMethodCalledFn-true' => [
                'testMethodCalledFn-true',
                [],
            ],
            'testArrowFunctionReturnByRef' => [
                'testArrowFunctionReturnByRef',
                ['T_FN' => true],
            ],
            'testIfIsset-$b' => [
                'testIfIsset-$b',
                [
                    'T_IF'    => true,
                    'T_ISSET' => true,
                ],
            ],
            'testIfEmpty-$c' => [
                'testIfEmpty-$c',
                [
                    'T_IF'    => true,
                    'T_EMPTY' => true,
                ],
            ],
            'testUnset-->' => [
                'testUnset-->',
                ['T_UNSET' => true],
            ],
            'testUnsetParenthesis' => [
                'testUnsetParenthesis',
                [],
            ],
            'testEval-concat' => [
                'testEval-concat',
                ['T_EVAL' => true],
            ],
            'testIfExitDie-boolean-or' => [
                'testIfExitDie-boolean-or',
                ['T_IF' => true],
            ],
            'testIfExitDie-message' => [
                'testIfExitDie-message',
                [
                    'T_IF'   => true,
                    'T_EXIT' => true,
                ],
            ],
            'testParseError-1' => [
                'testParseError-1',
                [],
            ],
        ];
    }

    /**
     * Test correctly determining whether a token is nested in parentheses with an owner
     * of a certain type, with multiple allowed possibilities.
     *
     * @return void
     */
    public function testHasOwnerMultipleTypes()
    {
        $stackPtr = self::$testTokens['testElseIfWithClosure-$array'];

        $result = Parentheses::hasOwner(self::$phpcsFile, $stackPtr, [\T_FUNCTION, \T_CLOSURE]);
        $this->assertFalse(
            $result,
            'Failed asserting that $array in "testElseIfWithClosure" does not have a "function" nor a "closure" owner'
        );

        $result = Parentheses::hasOwner(self::$phpcsFile, $stackPtr, [\T_IF, \T_ELSEIF, \T_ELSE]);
        $this->assertTrue(
            $result,
            'Failed asserting that $array in "testElseIfWithClosure" has an "if", "elseif" or "else" owner'
        );

        $stackPtr = self::$testTokens['testForWithTernary-$array'];

        $result = Parentheses::hasOwner(self::$phpcsFile, $stackPtr, [\T_ARRAY, \T_LIST]);
        $this->assertFalse(
            $result,
            'Failed asserting that $array in "testForWithTernary" does not have an array or list condition'
        );

        $result = Parentheses::hasOwner(self::$phpcsFile, $stackPtr, BCTokens::scopeOpeners());
        $this->assertTrue(
            $result,
            'Failed asserting that $array in "testForWithTernary" has an owner which is also a scope opener'
        );
    }

    /**
     * Test correctly determining whether the first set of parenthesis around an arbitrary token
     * has an owner of a certain type.
     *
     * @dataProvider dataFirstOwnerIn
     *
     * @param string    $testName    The name of this test as set in the cached $testTokens array.
     * @param array     $validOwners Valid owners to test against.
     * @param int|false $expected    Expected function output
     *
     * @return void
     */
    public function testFirstOwnerIn($testName, $validOwners, $expected)
    {
        $stackPtr = self::$testTokens[$testName];
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $result = Parentheses::firstOwnerIn(self::$phpcsFile, $stackPtr, $validOwners);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testFirstOwnerIn() For the array format.
     *
     * @return array
     */
    public function dataFirstOwnerIn()
    {
        return [
            'testElseIfWithClosure-$a-elseif' => [
                'testElseIfWithClosure-$a',
                [\T_ELSEIF],
                -10,
            ],
            'testElseIfWithClosure-$a-array' => [
                'testElseIfWithClosure-$a',
                [\T_ARRAY],
                false,
            ],
            'testForeach-45-foreach-for' => [
                'testForeach-45',
                [\T_FOREACH, \T_FOR],
                -27,
            ],
            'testForeach-45-array' => [
                'testForeach-45',
                [\T_ARRAY],
                false,
            ],
            'testForeach-$a-foreach-for' => [
                'testForeach-$a',
                [\T_FOREACH, \T_FOR],
                -43,
            ],
            'testForeach-$a-list' => [
                'testForeach-$a',
                [\T_LIST],
                false,
            ],
            'testFunctionwithArray-$param-function-closure' => [
                'testFunctionwithArray-$param',
                [\T_FUNCTION, \T_CLOSURE],
                -4,
            ],
            'testFunctionwithArray-$param-if-elseif-else' => [
                'testFunctionwithArray-$param',
                [\T_IF, \T_ELSEIF, \T_ELSE],
                false,
            ],
            'testAnonClass-implements-anon-class' => [
                'testAnonClass-implements',
                [\T_ANON_CLASS],
                -8,
            ],
            'testAnonClass-$e-function' => [
                'testAnonClass-$e',
                [\T_FUNCTION],
                false,
            ],
            'testAnonClass-$e-catch' => [
                'testAnonClass-$e',
                [\T_CATCH],
                false,
            ],
        ];
    }

    /**
     * Test correctly determining whether the last set of parenthesis around an arbitrary token
     * has an owner of a certain type.
     *
     * @dataProvider dataLastOwnerIn
     *
     * @param string    $testName    The name of this test as set in the cached $testTokens array.
     * @param array     $validOwners Valid owners to test against.
     * @param int|false $expected    Expected function output
     *
     * @return void
     */
    public function testLastOwnerIn($testName, $validOwners, $expected)
    {
        $stackPtr = self::$testTokens[$testName];
        if ($expected !== false) {
            $expected += $stackPtr;
        }

        $result = Parentheses::lastOwnerIn(self::$phpcsFile, $stackPtr, $validOwners);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testLastOwnerIn() For the array format.
     *
     * @return array
     */
    public function dataLastOwnerIn()
    {
        $arrowFunctionOwners = Collections::arrowFunctionTokensBC();

        return [
            'testElseIfWithClosure-$a-closure' => [
                'testElseIfWithClosure-$a',
                [\T_CLOSURE],
                -3,
            ],
            'testElseIfWithClosure-$a-array' => [
                'testElseIfWithClosure-$a',
                [\T_ARRAY],
                false,
            ],
            'testForeach-45-array' => [
                'testForeach-45',
                [\T_ARRAY],
                -2,
            ],
            'testForeach-45-foreach-for' => [
                'testForeach-45',
                [\T_FOREACH, \T_FOR],
                false,
            ],
            'testForeach-$a-foreach-for' => [
                'testForeach-$a',
                [\T_FOREACH, \T_FOR],
                false,
            ],
            'testForeach-$a-list' => [
                'testForeach-$a',
                [\T_LIST],
                -6,
            ],
            'testFunctionwithArray-$param-function-closure' => [
                'testFunctionwithArray-$param',
                [\T_FUNCTION, \T_CLOSURE],
                -4,
            ],
            'testFunctionwithArray-$param-if-elseif-else' => [
                'testFunctionwithArray-$param',
                [\T_IF, \T_ELSEIF, \T_ELSE],
                false,
            ],
            'testAnonClass-implements-anon-class' => [
                'testAnonClass-implements',
                [\T_ANON_CLASS],
                -8,
            ],
            'testAnonClass-$e-function' => [
                'testAnonClass-$e',
                [\T_FUNCTION],
                false,
            ],
            'testArrowFunction-$param' => [
                'testArrowFunction-$param',
                $arrowFunctionOwners,
                -2,
            ],

            'testAnonClass-$e-catch' => [
                'testAnonClass-$e',
                [\T_CATCH],
                -5,
            ],
            'testArrowFunctionReturnByRef' => [
                'testArrowFunctionReturnByRef',
                $arrowFunctionOwners,
                -4,
            ],
            'testIfEmpty-$c-unset' => [
                'testIfEmpty-$c',
                [\T_UNSET],
                false,
            ],
            'testIfEmpty-$c-isset-empty' => [
                'testIfEmpty-$c',
                [\T_ISSET, \T_EMPTY],
                -3,
            ],
        ];
    }
}
