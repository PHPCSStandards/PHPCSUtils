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
 * Tests for the \PHPCSUtils\Utils\ObjectDeclarations::findExtendedInterfaceNames() method.
 *
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::findExtendedInterfaceNames
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::findNames
 *
 * @group objectdeclarations
 *
 * @since 1.0.0
 */
class FindExtendedInterfaceNamesTest extends UtilityMethodTestCase
{

    /**
     * Test getting a `false` result when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $result = ObjectDeclarations::findExtendedInterfaceNames(self::$phpcsFile, 100000);
        $this->assertFalse($result);
    }

    /**
     * Test getting a `false` result when a token other than one of the supported tokens is passed.
     *
     * @return void
     */
    public function testNotAnInterface()
    {
        $token  = $this->getTargetToken('/* testNotAnInterface */', [\T_FUNCTION]);
        $result = ObjectDeclarations::findExtendedInterfaceNames(self::$phpcsFile, $token);
        $this->assertFalse($result);
    }

    /**
     * Test retrieving the names of the interfaces being extended by another interface.
     *
     * @dataProvider dataFindExtendedInterfaceNames
     *
     * @param string      $testMarker The comment which prefaces the target token in the test file.
     * @param array|false $expected   Expected function output.
     *
     * @return void
     */
    public function testFindExtendedInterfaceNames($testMarker, $expected)
    {
        $interface = $this->getTargetToken($testMarker, [\T_INTERFACE]);
        $result    = ObjectDeclarations::findExtendedInterfaceNames(self::$phpcsFile, $interface);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testFindExtendedInterfaceNames() For the array format.
     *
     * @return array
     */
    public function dataFindExtendedInterfaceNames()
    {
        return [
            'not-extended' => [
                '/* testInterface */',
                false,
            ],
            'extends-one' => [
                '/* testExtendedInterface */',
                ['testInterface'],
            ],
            'extends-two' => [
                '/* testMultiExtendedInterface */',
                [
                    'testInterfaceA',
                    'testInterfaceB',
                ],
            ],
            'extends-one-namespaced' => [
                '/* testExtendedNamespacedInterface */',
                ['\PHPCSUtils\Tests\ObjectDeclarations\testInterface'],
            ],
            'extends-two-namespaced' => [
                '/* testMultiExtendedNamespacedInterface */',
                [
                    '\PHPCSUtils\Tests\ObjectDeclarations\testInterfaceA',
                    '\PHPCSUtils\Tests\ObjectDeclarations\testFEINInterfaceB',
                ],
            ],
            'extends-with-comments' => [
                '/* testMultiExtendedInterfaceWithComments */',
                [
                    'testInterfaceA',
                    '\PHPCSUtils\Tests\Some\Declarations\testInterfaceB',
                    '\testInterfaceC',
                ],
            ],
            'parse-error' => [
                '/* testParseError */',
                false,
            ],
        ];
    }
}
