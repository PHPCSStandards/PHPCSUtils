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

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\ObjectDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\ObjectDeclarations::findExtendedClassName() method.
 *
 * The tests in this class cover the differences between the PHPCS native method and the PHPCSUtils
 * version. These tests would fail when using the BCFile `findExtendedClassName()` method.
 *
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::findExtendedClassName
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::findNames
 *
 * @group objectdeclarations
 *
 * @since 1.0.0
 */
final class FindExtendedClassNameDiffTest extends UtilityMethodTestCase
{

    /**
     * Test retrieving the name of the class being extended by another class (or interface).
     *
     * @dataProvider dataFindExtendedClassName
     *
     * @param string       $testMarker The comment which prefaces the target token in the test file.
     * @param string|false $expected   Expected function output.
     *
     * @return void
     */
    public function testFindExtendedClassName($testMarker, $expected)
    {
        $OOToken = $this->getTargetToken($testMarker, [\T_CLASS, \T_ANON_CLASS, \T_INTERFACE]);
        $result  = ObjectDeclarations::findExtendedClassName(self::$phpcsFile, $OOToken);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testFindExtendedClassName() For the array format.
     *
     * @return array<string, array<string, string|false>>
     */
    public static function dataFindExtendedClassName()
    {
        return [
            'phpcs-annotation-and-comments' => [
                'testMarker' => '/* testDeclarationWithComments */',
                'expected'   => '\Package\SubDir\SomeClass',
            ],
            'parse-error-stray-comma' => [
                'testMarker' => '/* testExtendedClassStrayComma */',
                'expected'   => 'testClass',
            ],
        ];
    }
}
