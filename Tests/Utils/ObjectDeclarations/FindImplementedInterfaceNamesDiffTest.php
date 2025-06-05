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
 * Tests for the \PHPCSUtils\Utils\ObjectDeclarations::findImplementedInterfaceNames() method.
 *
 * The tests in this class cover the differences between the PHPCS native method and the PHPCSUtils
 * version. These tests would fail when using the BCFile `findImplementedInterfaceNames()` method.
 *
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::findImplementedInterfaceNames
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::findNames
 *
 * @group objectdeclarations
 *
 * @since 1.0.0
 */
final class FindImplementedInterfaceNamesDiffTest extends UtilityMethodTestCase
{

    /**
     * Test retrieving the name(s) of the interfaces being implemented by a class.
     *
     * @dataProvider dataFindImplementedInterfaceNames
     *
     * @param string              $testMarker The comment which prefaces the target token in the test file.
     * @param array<string>|false $expected   Expected function output.
     *
     * @return void
     */
    public function testFindImplementedInterfaceNames($testMarker, $expected)
    {
        $OOToken = $this->getTargetToken($testMarker, [\T_CLASS, \T_ANON_CLASS, \T_INTERFACE, \T_ENUM]);
        $result  = ObjectDeclarations::findImplementedInterfaceNames(self::$phpcsFile, $OOToken);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testFindImplementedInterfaceNames() For the array format.
     *
     * @return array<string, array<string, string|array<string>|false>>
     */
    public static function dataFindImplementedInterfaceNames()
    {
        return [
            'phpcs-annotation-and-comments' => [
                'testMarker' => '/* testDeclarationWithComments */',
                'expected'   => [
                    '\Vendor\Package\Core\SubDir\SomeInterface',
                    'InterfaceB',
                ],
            ],
            'parse-error-stray-comma' => [
                'testMarker' => '/* testMultiImplementedStrayComma */',
                'expected'   => [
                    0 => 'testInterfaceA',
                    1 => 'testInterfaceB',
                ],
            ],
        ];
    }
}
