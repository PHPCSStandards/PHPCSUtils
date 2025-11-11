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
use PHPCSUtils\Utils\Constants;

/**
 * Tests for the \PHPCSUtils\Utils\Constants::getAttributeOpeners method.
 *
 * @covers \PHPCSUtils\Utils\Constants::getAttributeOpeners
 * @covers \PHPCSUtils\Internal\AttributeHelper::getOpeners
 *
 * @group attributes
 * @group constants
 *
 * @since 1.2.0
 */
final class GetOpenersForConstantsTest extends PolyfilledTestCase
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

        Constants::getAttributeOpeners(self::$phpcsFile, false);
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

        Constants::getAttributeOpeners(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an expected exception when a non T_CONST token is passed.
     *
     * @return void
     */
    public function testNotAcceptedTypeException()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type T_CONST;');

        $targetPtr = $this->getTargetToken('/* testNotAConstToken */', \T_STRING);
        Constants::getAttributeOpeners(self::$phpcsFile, $targetPtr);
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
        $targetPtr = $this->getTargetToken($identifier, \T_CONST);
        $expected  = $this->updateExpectedTokenPositions($targetPtr, $expected);
        $result    = Constants::getAttributeOpeners(self::$phpcsFile, $targetPtr);

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * Note: token positions are offsets in relation to the position of the T_CONST token!
     *
     * @see testGetAttributeOpeners()
     *
     * @return array<string, array<string, string|array<int>>>
     */
    public static function dataGetAttributeOpeners()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'global const, with 1 attribute block' => [
                'identifier' => '/* testGlobalConstantWithAttribute */',
                'expected'   => [-12],
            ],
            'global const, no attributes' => [
                'identifier' => '/* testGlobalConstantNoAttribute */',
                'expected'   => [],
            ],

            'class const, no modifiers, 1 attribute block' => [
                'identifier' => '/* testInClassNoModifiersWithAttribute */',
                'expected'   => [-5],
            ],
            'class const, final protected, no attributes' => [
                'identifier' => '/* testInClassConstAsNameNoAttribute */',
                'expected'   => [],
            ],
            'class const, private, 1 attribute block' => [
                'identifier' => '/* testInClass */',
                'expected'   => [-18],
            ],

            'enum const, final, 3 attribute blocks' => [
                'identifier' => '/* testInEnum */',
                'expected'   => [
                    -24,
                    -21,
                    -15,
                ],
            ],
            'trait const, final public, 3 attribute blocks' => [
                'identifier' => '/* testInTrait */',
                'expected'   => [
                    ($php8Names === true ? -49 : -50),
                    ($php8Names === true ? -36 : -37),
                    -13,
                ],
            ],
            'interface const, private, 1 multi-line attribute block' => [
                'identifier' => '/* testInInterface */',
                'expected'   => [-18],
            ],
            'anon class const, protected final, 1 attribute block, same line, nested in ternary' => [
                'identifier' => '/* testInAnonClass */',
                'expected'   => [-10],
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
        $identifier = $cases['class const, private, 1 attribute block']['identifier'];
        $expected   = $cases['class const, private, 1 attribute block']['expected'];

        $targetPtr = $this->getTargetToken($identifier, \T_CONST);
        $expected  = $this->updateExpectedTokenPositions($targetPtr, $expected);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = Constants::getAttributeOpeners(self::$phpcsFile, $targetPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, "{$targetPtr}-constant");
        $resultSecondRun = Constants::getAttributeOpeners(self::$phpcsFile, $targetPtr);

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
