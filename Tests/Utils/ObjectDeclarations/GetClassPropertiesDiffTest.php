<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\ObjectDeclarations;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
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
class GetClassPropertiesDiffTest extends UtilityMethodTestCase
{

    /**
     * Test retrieving the properties for a class declaration.
     *
     * @dataProvider dataGetClassProperties
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   Expected function output.
     *
     * @return void
     */
    public function testGetClassProperties($testMarker, $expected)
    {
        $class  = $this->getTargetToken($testMarker, \T_CLASS);
        $result = ObjectDeclarations::getClassProperties(self::$phpcsFile, $class);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetClassProperties() For the array format.
     *
     * @return array
     */
    public function dataGetClassProperties()
    {
        return [
            'phpcs-annotation' => [
                '/* testPHPCSAnnotations */',
                [
                    'is_abstract' => false,
                    'is_final'    => true,
                ],
            ],
            'unorthodox-docblock-placement' => [
                '/* testWithDocblockWithWeirdlyPlacedProperty */',
                [
                    'is_abstract' => false,
                    'is_final'    => true,
                ],
            ],
            'abstract-final-parse-error' => [
                '/* testParseErrorAbstractFinal */',
                [
                    'is_abstract' => true,
                    'is_final'    => false,
                ],
            ],
        ];
    }
}
