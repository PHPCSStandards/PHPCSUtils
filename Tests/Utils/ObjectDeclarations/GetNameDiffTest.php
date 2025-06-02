<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\ObjectDeclarations;

use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\ObjectDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\ObjectDeclarations::getName() method.
 *
 * The tests in this class cover the differences between the PHPCS native method and the PHPCSUtils
 * version. These tests would fail when using the BCFile `getDeclarationName()` method.
 *
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getName
 *
 * @group objectdeclarations
 *
 * @since 1.0.0
 */
final class GetNameDiffTest extends PolyfilledTestCase
{

    /**
     * Test receiving an expected exception when a non-integer token is passed.
     *
     * @return void
     */
    public function testNonIntegerTokenPassed()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type integer, boolean given');

        ObjectDeclarations::getName(self::$phpcsFile, false);
    }

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $result = ObjectDeclarations::getName(self::$phpcsFile, 10000);
        $this->assertNull($result);
    }

    /**
     * Test receiving "null" when passed an anonymous construct or in case of a parse error.
     *
     * @dataProvider dataGetNameNull
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType Token type of the token to get as stackPtr.
     *
     * @return void
     */
    public function testGetNameNull($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $result = ObjectDeclarations::getName(self::$phpcsFile, $target);
        $this->assertNull($result);
    }

    /**
     * Data provider.
     *
     * @see testGetNameNull() For the array format.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataGetNameNull()
    {
        return [
            'live-coding' => [
                'testMarker' => '/* testLiveCoding */',
                'targetType' => \T_CLASS,
            ],
        ];
    }

    /**
     * Test retrieving the name of a function or OO structure.
     *
     * @dataProvider dataGetName
     *
     * @param string          $testMarker The comment which prefaces the target token in the test file.
     * @param string          $expected   Expected function output.
     * @param int|string|null $targetType Optional. Token type of the token to get as stackPtr.
     *
     * @return void
     */
    public function testGetName($testMarker, $expected, $targetType = null)
    {
        if (isset($targetType) === false) {
            $targetType = [\T_CLASS, \T_INTERFACE, \T_TRAIT, \T_ENUM, \T_FUNCTION];
        }

        $target = $this->getTargetToken($testMarker, $targetType);
        $result = ObjectDeclarations::getName(self::$phpcsFile, $target);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetName() For the array format.
     *
     * @return array<string, array<string, string>>
     */
    public static function dataGetName()
    {
        return [
            'trait-name-starts-with-number' => [
                'testMarker' => '/* testTraitStartingWithNumber */',
                'expected'   => '5InvalidNameStartingWithNumber',
            ],
            'interface-fully-numeric-name' => [
                'testMarker' => '/* testInterfaceFullyNumeric */',
                'expected'   => '12345',
            ],
            'using-reserved-keyword-as-name' => [
                'testMarker' => '/* testInvalidInterfaceName */',
                'expected'   => 'switch',
            ],
        ];
    }
}
