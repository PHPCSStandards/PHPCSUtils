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
class FindImplementedInterfaceNamesDiffTest extends UtilityMethodTestCase
{

    /**
     * Test retrieving the name(s) of the interfaces being implemented by a class.
     *
     * @dataProvider dataFindImplementedInterfaceNames
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $expected   Expected function output.
     *
     * @return void
     */
    public function testFindImplementedInterfaceNames($testMarker, $expected)
    {
        $OOToken = $this->getTargetToken($testMarker, [\T_CLASS, \T_ANON_CLASS, \T_INTERFACE]);
        $result  = ObjectDeclarations::findImplementedInterfaceNames(self::$phpcsFile, $OOToken);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testFindImplementedInterfaceNames() For the array format.
     *
     * @return array
     */
    public function dataFindImplementedInterfaceNames()
    {
        return [
            'phpcs-annotation-and-comments' => [
                '/* testDeclarationWithComments */',
                [
                    '//phpcs:ignore Standard.Cat.Sniff -- For reasons
        \Vendor
        /* comment */
        \Package\Core
        //phpcs:disable Standard.Cat.Sniff -- For reasons
        \SubDir         \         SomeInterface',
                    '// comment
        InterfaceB',
                ],
            ],
            'parse-error-stray-comma' => [
                '/* testMultiImplementedStrayComma */',
                [
                    0 => 'testInterfaceA',
                    1 => 'testInterfaceB',
                ],
            ],
        ];
    }
}
