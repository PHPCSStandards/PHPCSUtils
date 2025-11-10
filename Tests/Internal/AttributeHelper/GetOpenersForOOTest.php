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

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Internal\Cache;
use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\ObjectDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\ObjectDeclarations::getAttributeOpenerss() method.
 *
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getAttributeOpeners
 * @covers \PHPCSUtils\Internal\AttributeHelper::getOpeners
 *
 * @group attributes
 * @group objectdeclarations
 *
 * @since 1.2.0
 */
final class GetOpenersForOOTest extends PolyfilledTestCase
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

        ObjectDeclarations::getAttributeOpeners(self::$phpcsFile, false);
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

        ObjectDeclarations::getAttributeOpeners(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an expected exception when a non OO token is passed.
     *
     * @return void
     */
    public function testNotAcceptedTypeException()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be of type T_CLASS, T_ANON_CLASS, T_INTERFACE, T_TRAIT or T_ENUM;'
        );

        $targetPtr = $this->getTargetToken('/* testNotAnOOToken */', \T_CONSTANT_ENCAPSED_STRING);
        ObjectDeclarations::getAttributeOpeners(self::$phpcsFile, $targetPtr);
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
        $targetPtr = $this->getTargetToken($identifier, Tokens::$ooScopeTokens);
        $expected  = $this->updateExpectedTokenPositions($targetPtr, $expected);
        $result    = ObjectDeclarations::getAttributeOpeners(self::$phpcsFile, $targetPtr);

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * Note: token positions are offsets in relation to the position of the OO token!
     *
     * @see testGetAttributeOpeners()
     *
     * @return array<string, array<string, string|array<int>>>
     */
    public static function dataGetAttributeOpeners()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'class, no modifiers, 1 attribute block' => [
                'identifier' => '/* testClassNoModifiersWithAttribute */',
                'expected'   => [-6],
            ],
            'class, abstract readonly, 3 attribute blocks' => [
                'identifier' => '/* testClassTwoModifiersWithAttribute */',
                'expected'   => [
                    -25,
                    -22,
                    -16,
                ],
            ],
            'class, readonly final, no attributes' => [
                'identifier' => '/* testClassNoAttribute */',
                'expected'   => [],
            ],
            'class, readonly, 1 attribute block, same line' => [
                'identifier' => '/* testClassOneModifierWithAttributeSameLine */',
                'expected'   => [-17],
            ],

            'enum, 1 attribute block' => [
                'identifier' => '/* testEnumAttribute */',
                'expected'   => [-6],
            ],
            'trait, 3 attribute blocks' => [
                'identifier' => '/* testTraitAttribute */',
                'expected'   => [
                    ($php8Names === true ? -40 : -43),
                    ($php8Names === true ? -29 : -32),
                    -8,
                ],
            ],
            'interface, 1 multi-line attribute block' => [
                'identifier' => '/* testInterfaceAttribute */',
                'expected'   => [-14],
            ],

            'anon class, no modifiers, 1 attribute block, same line' => [
                'identifier' => '/* testAnonClassAttributeSameLine */',
                'expected'   => [-6],
            ],
            'anon class, no modifiers, no attributes' => [
                'identifier' => '/* testAnonClassNoAttribute */',
                'expected'   => [],
            ],
            'anon class, readonly, 1 attribute block, same line' => [
                'identifier' => '/* testReadonlyAnonClassAttributeSameLine */',
                'expected'   => [-8],
            ],
            'anon class, readonly, no attributes' => [
                'identifier' => '/* testReadonlyAnonClassNoAttributes */',
                'expected'   => [],
            ],
            'anon class, readonly, 1 attribute block, line before' => [
                'identifier' => '/* testReadonlyAnonClassAttribute */',
                'expected'   => [-9],
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
        $identifier = $cases['interface, 1 multi-line attribute block']['identifier'];
        $expected   = $cases['interface, 1 multi-line attribute block']['expected'];

        $targetPtr = $this->getTargetToken($identifier, Tokens::$ooScopeTokens);
        $expected  = $this->updateExpectedTokenPositions($targetPtr, $expected);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = ObjectDeclarations::getAttributeOpeners(self::$phpcsFile, $targetPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, "{$targetPtr}-OO");
        $resultSecondRun = ObjectDeclarations::getAttributeOpeners(self::$phpcsFile, $targetPtr);

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
