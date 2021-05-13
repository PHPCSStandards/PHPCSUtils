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

use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::getClassProperties() method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::getClassProperties
 *
 * @group objectdeclarations
 *
 * @since 1.0.0
 */
class GetClassPropertiesTest extends UtilityMethodTestCase
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
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $tokenType  The type of token to look for after the marker.
     *
     * @return void
     */
    public function testNotAClassException($testMarker, $tokenType)
    {
        $this->expectPhpcsException('$stackPtr must be of type T_CLASS');

        $testClass = static::TEST_CLASS;
        $interface = $this->getTargetToken($testMarker, $tokenType);
        $testClass::getClassProperties(self::$phpcsFile, $interface);
    }

    /**
     * Data provider.
     *
     * @see testNotAClassException() For the array format.
     *
     * @return array
     */
    public function dataNotAClassException()
    {
        return [
            'interface'  => [
                '/* testNotAClass */',
                \T_INTERFACE,
            ],
            'anon-class' => [
                '/* testAnonClass */',
                \T_ANON_CLASS,
            ],
        ];
    }

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
        $testClass = static::TEST_CLASS;

        $class  = $this->getTargetToken($testMarker, \T_CLASS);
        $result = $testClass::getClassProperties(self::$phpcsFile, $class);
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
            'no-properties' => [
                '/* testClassWithoutProperties */',
                [
                    'is_abstract' => false,
                    'is_final'    => false,
                ],
            ],
            'abstract' => [
                '/* testAbstractClass */',
                [
                    'is_abstract' => true,
                    'is_final'    => false,
                ],
            ],
            'final' => [
                '/* testFinalClass */',
                [
                    'is_abstract' => false,
                    'is_final'    => true,
                ],
            ],
            'comments-and-new-lines' => [
                '/* testWithCommentsAndNewLines */',
                [
                    'is_abstract' => true,
                    'is_final'    => false,
                ],
            ],
            'no-properties-with-docblock' => [
                '/* testWithDocblockWithoutProperties */',
                [
                    'is_abstract' => false,
                    'is_final'    => false,
                ],
            ],
            'abstract-final-parse-error' => [
                '/* testParseErrorAbstractFinal */',
                [
                    'is_abstract' => true,
                    'is_final'    => true,
                ],
            ],
        ];
    }
}
