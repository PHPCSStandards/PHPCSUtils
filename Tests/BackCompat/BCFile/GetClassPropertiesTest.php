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

use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\Tests\PolyfilledTestCase;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::getClassProperties() method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::getClassProperties
 *
 * @group objectdeclarations
 *
 * @since 1.0.0
 */
class GetClassPropertiesTest extends PolyfilledTestCase
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
     * Test receiving an expected exception when a non class token is passed.
     *
     * @dataProvider dataNotAClassException
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $tokenType  The type of token to look for after the marker.
     *
     * @return void
     */
    public function testNotAClassException($testMarker, $tokenType)
    {
        $this->expectPhpcsException('$stackPtr must be of type T_CLASS');

        $testClass = static::TEST_CLASS;
        $target    = $this->getTargetToken($testMarker, $tokenType);
        $testClass::getClassProperties(self::$phpcsFile, $target);
    }

    /**
     * Data provider.
     *
     * @see testNotAClassException() For the array format.
     *
     * @return array<string, array<string, string|int>>
     */
    public static function dataNotAClassException()
    {
        return [
            'interface'  => [
                'testMarker' => '/* testNotAClass */',
                'tokenType'  => \T_INTERFACE,
            ],
            'anon-class' => [
                'testMarker' => '/* testAnonClass */',
                'tokenType'  => \T_ANON_CLASS,
            ],
            'enum' => [
                'testMarker' => '/* testEnum */',
                'tokenType'  => \T_ENUM,
            ],
        ];
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
        // Remove keys which will only exist in the PHPCSUtils version of this method.
        unset($expected['abstract_token'], $expected['final_token'], $expected['readonly_token']);

        $class  = $this->getTargetToken($testMarker, \T_CLASS);
        $result = BCFile::getClassProperties(self::$phpcsFile, $class);
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
            'no-properties' => [
                'testMarker' => '/* testClassWithoutProperties */',
                'expected'   => [
                    'is_abstract'    => false,
                    'abstract_token' => false,
                    'is_final'       => false,
                    'final_token'    => false,
                    'is_readonly'    => false,
                    'readonly_token' => false,
                ],
            ],
            'abstract' => [
                'testMarker' => '/* testAbstractClass */',
                'expected'   => [
                    'is_abstract'    => true,
                    'abstract_token' => -2,
                    'is_final'       => false,
                    'final_token'    => false,
                    'is_readonly'    => false,
                    'readonly_token' => false,
                ],
            ],
            'final' => [
                'testMarker' => '/* testFinalClass */',
                'expected'   => [
                    'is_abstract'    => false,
                    'abstract_token' => false,
                    'is_final'       => true,
                    'final_token'    => -2,
                    'is_readonly'    => false,
                    'readonly_token' => false,
                ],
            ],
            'readonly' => [
                'testMarker' => '/* testReadonlyClass */',
                'expected'   => [
                    'is_abstract'    => false,
                    'abstract_token' => false,
                    'is_final'       => false,
                    'final_token'    => false,
                    'is_readonly'    => true,
                    'readonly_token' => -2,
                ],
            ],
            'final-readonly' => [
                'testMarker' => '/* testFinalReadonlyClass */',
                'expected'   => [
                    'is_abstract'    => false,
                    'abstract_token' => false,
                    'is_final'       => true,
                    'final_token'    => -4,
                    'is_readonly'    => true,
                    'readonly_token' => -2,
                ],
            ],
            'readonly-final' => [
                'testMarker' => '/* testReadonlyFinalClass */',
                'expected'   => [
                    'is_abstract'    => false,
                    'abstract_token' => false,
                    'is_final'       => true,
                    'final_token'    => -2,
                    'is_readonly'    => true,
                    'readonly_token' => -6,
                ],
            ],
            'abstract-readonly' => [
                'testMarker' => '/* testAbstractReadonlyClass */',
                'expected'   => [
                    'is_abstract'    => true,
                    'abstract_token' => -4,
                    'is_final'       => false,
                    'final_token'    => false,
                    'is_readonly'    => true,
                    'readonly_token' => -2,
                ],
            ],
            'readonly-abstract' => [
                'testMarker' => '/* testReadonlyAbstractClass */',
                'expected'   => [
                    'is_abstract'    => true,
                    'abstract_token' => -2,
                    'is_final'       => false,
                    'final_token'    => false,
                    'is_readonly'    => true,
                    'readonly_token' => -4,
                ],
            ],
            'comments-and-new-lines' => [
                'testMarker' => '/* testWithCommentsAndNewLines */',
                'expected'   => [
                    'is_abstract'    => true,
                    'abstract_token' => -6,
                    'is_final'       => false,
                    'final_token'    => false,
                    'is_readonly'    => false,
                    'readonly_token' => false,
                ],
            ],
            'no-properties-with-docblock' => [
                'testMarker' => '/* testWithDocblockWithoutProperties */',
                'expected'   => [
                    'is_abstract'    => false,
                    'abstract_token' => false,
                    'is_final'       => false,
                    'final_token'    => false,
                    'is_readonly'    => false,
                    'readonly_token' => false,
                ],
            ],
            'abstract-final-parse-error' => [
                'testMarker' => '/* testParseErrorAbstractFinal */',
                'expected'   => [
                    'is_abstract'    => true,
                    'abstract_token' => -5,
                    'is_final'       => true,
                    'final_token'    => -11,
                    'is_readonly'    => false,
                    'readonly_token' => false,
                ],
            ],
        ];
    }
}
