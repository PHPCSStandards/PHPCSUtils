<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2025 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Internal\AttributeHelper;

use PHPCSUtils\Internal\Cache;
use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\FunctionDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\FunctionDeclarations::getAttributeOpeners method.
 *
 * @covers \PHPCSUtils\Utils\FunctionDeclarations::getAttributeOpeners
 * @covers \PHPCSUtils\Internal\AttributeHelper::getOpeners
 *
 * @group attributes
 * @group functiondeclarations
 *
 * @since 1.2.0
 */
final class GetOpenersForFunctionsTest extends PolyfilledTestCase
{

    /**
     * Test receiving an exception when passing a non-integer token pointer.
     *
     * @return void
     */
    public function testNonIntegerToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type integer, boolean given');

        FunctionDeclarations::getAttributeOpeners(self::$phpcsFile, false);
    }

    /**
     * Test receiving an exception when passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object, 100000 given'
        );

        FunctionDeclarations::getAttributeOpeners(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an expected exception when a non function keyword token is passed.
     *
     * @return void
     */
    public function testNotAcceptedTypeException()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type T_FUNCTION, T_CLOSURE or T_FN;');

        $targetPtr = $this->getTargetToken('/* testNotAFunctionToken */', \T_ECHO);
        FunctionDeclarations::getAttributeOpeners(self::$phpcsFile, $targetPtr);
    }

    /**
     * Test the getAttributeOpeners() method.
     *
     * @dataProvider dataGetAttributeOpeners
     *
     * @param string     $identifier Comment which precedes the test case.
     * @param array<int> $expected   Expected function output.
     *
     * @return void
     */
    public function testGetAttributeOpeners($identifier, $expected)
    {
        $targetPtr = $this->getTargetToken($identifier, Collections::functionDeclarationTokens());
        $expected  = $this->updateExpectedTokenPositions($targetPtr, $expected);
        $result    = FunctionDeclarations::getAttributeOpeners(self::$phpcsFile, $targetPtr);

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * Note: token positions are offsets in relation to the position of the T_FUNCTION, T_CLOSURE or T_FN token!
     *
     * @see testGetAttributeOpeners()
     *
     * @return array<string, array<string, string|array<int>>>
     */
    public static function dataGetAttributeOpeners()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'global function, with 1 attribute block' => [
                'identifier' => '/* testGlobalFunctionWithAttribute */',
                'expected'   => [-12],
            ],
            'global function, no attributes' => [
                'identifier' => '/* testGlobalFunctionNoAttribute */',
                'expected'   => [],
            ],

            'closure, with 1 attribute block' => [
                'identifier' => '/* testClosureAttributeSameLine */',
                'expected'   => [-6],
            ],
            'closure, no attributes' => [
                'identifier' => '/* testClosureNoAttribute */',
                'expected'   => [],
            ],
            'closure, static, with 1 attribute block' => [
                'identifier' => '/* testStaticClosureAttribute */',
                'expected'   => [-9],
            ],

            'arrow function, with 1 attribute block' => [
                'identifier' => '/* testArrowFnAttributeSameLine */',
                'expected'   => [-6],
            ],
            'arrow function, no attributes' => [
                'identifier' => '/* testArrowFnNoAttribute */',
                'expected'   => [],
            ],
            'arrow function, static, with 1 attribute block' => [
                'identifier' => '/* testStaticArrowFnAttribute */',
                'expected'   => [-9],
            ],

            'class method, no modifiers, 1 attribute block' => [
                'identifier' => '/* testInClassNoModifiersWithAttribute */',
                'expected'   => [-5],
            ],
            'class method, final protected, no attributes' => [
                'identifier' => '/* testInClassNoAttribute */',
                'expected'   => [],
            ],
            'class method, private, 1 attribute block' => [
                'identifier' => '/* testInClass */',
                'expected'   => [-18],
            ],

            'enum method, final, 1 attribute block' => [
                'identifier' => '/* testInEnum */',
                'expected'   => [-9],
            ],
            'trait method, abstract static public, 3 attribute blocks' => [
                'identifier' => '/* testInTrait */',
                'expected'   => [
                    ($php8Names === true ? -51 : -53),
                    ($php8Names === true ? -38 : -40),
                    -15,
                ],
            ],
            'interface method, public, 1 multi-line attribute block' => [
                'identifier' => '/* testInInterface */',
                'expected'   => [-18],
            ],
            'anon class method, protected static, 3 attribute blocks, nested in ternary' => [
                'identifier' => '/* testInAnonClass */',
                'expected'   => [
                    -26,
                    -23,
                    -17,
                ],
            ],
        ];
    }

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @return void
     */
    public function testResultIsCached()
    {
        $methodName = 'PHPCSUtils\\Internal\\AttributeHelper::getOpeners';
        $cases      = self::dataGetAttributeOpeners();
        $identifier = $cases['arrow function, static, with 1 attribute block']['identifier'];
        $expected   = $cases['arrow function, static, with 1 attribute block']['expected'];

        $targetPtr = $this->getTargetToken($identifier, Collections::functionDeclarationTokens());
        $expected  = $this->updateExpectedTokenPositions($targetPtr, $expected);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = FunctionDeclarations::getAttributeOpeners(self::$phpcsFile, $targetPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, "{$targetPtr}-function");
        $resultSecondRun = FunctionDeclarations::getAttributeOpeners(self::$phpcsFile, $targetPtr);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }

    /**
     * Test helper to translate token offsets to absolute positions in an "expected" array.
     *
     * @param int        $targetPtr The token pointer to the target token from which
     *                              the offset is calculated.
     * @param array<int> $expected  The expected function output containing offsets.
     *
     * @return array<int>
     */
    private function updateExpectedTokenPositions($targetPtr, $expected)
    {
        foreach ($expected as $key => $value) {
            $expected[$key] += $targetPtr;
        }

        return $expected;
    }
}
