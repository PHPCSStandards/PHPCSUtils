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
 * Tests for the \PHPCSUtils\Utils\ObjectDeclarations::getClassProperties() method.
 *
 * The tests in this class cover the differences between the PHPCS native method and the PHPCSUtils
 * version. These tests would fail when using the BCFile `getClassProperties()` method.
 *
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getClassProperties
 *
 * @group objectdeclarations
 *
 * @since 1.0.0
 */
final class GetClassPropertiesDiffTest extends PolyfilledTestCase
{

    /**
     * Test passing a non-integer token pointer.
     *
     * @return void
     */
    public function testNonIntegerToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type integer, string given');

        ObjectDeclarations::getClassProperties(self::$phpcsFile, 'string');
    }

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object'
        );

        ObjectDeclarations::getClassProperties(self::$phpcsFile, 10000);
    }

    /**
     * Test retrieving the properties for a class declaration.
     *
     * @dataProvider dataGetClassProperties
     *
     * @param string                  $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, bool|int> $expected   Expected function output.
     *
     * @return void
     */
    public function testGetClassProperties($testMarker, $expected)
    {
        $class = $this->getTargetToken($testMarker, \T_CLASS);

        // Translate offsets to absolute token positions.
        if (\is_int($expected['abstract_token']) === true) {
            $expected['abstract_token'] += $class;
        }
        if (\is_int($expected['final_token']) === true) {
            $expected['final_token'] += $class;
        }
        if (\is_int($expected['readonly_token']) === true) {
            $expected['readonly_token'] += $class;
        }

        $result = ObjectDeclarations::getClassProperties(self::$phpcsFile, $class);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetClassProperties() For the array format.
     *
     * @return array<string, array<string, string|array<string, bool|int>>>
     */
    public static function dataGetClassProperties()
    {
        return [
            'phpcs-annotation' => [
                'testMarker' => '/* testPHPCSAnnotations */',
                'expected'   => [
                    'is_abstract'    => false,
                    'abstract_token' => false,
                    'is_final'       => true,
                    'final_token'    => -5,
                    'is_readonly'    => false,
                    'readonly_token' => false,
                ],
            ],
            'unorthodox-docblock-placement' => [
                'testMarker' => '/* testWithDocblockWithWeirdlyPlacedModifier */',
                'expected'   => [
                    'is_abstract'    => false,
                    'abstract_token' => false,
                    'is_final'       => true,
                    'final_token'    => -31,
                    'is_readonly'    => false,
                    'readonly_token' => false,
                ],
            ],
        ];
    }
}
