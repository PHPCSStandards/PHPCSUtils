<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\UseStatements;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\UseStatements;

/**
 * Tests for the \PHPCSUtils\Utils\UseStatements::isImportUse(),
 * \PHPCSUtils\Utils\UseStatements::isTraitUse(),
 * \PHPCSUtils\Utils\UseStatements::isClosureUse()
 * and \PHPCSUtils\Utils\UseStatements::getType() methods.
 *
 * @covers \PHPCSUtils\Utils\UseStatements::isTraitUse
 * @covers \PHPCSUtils\Utils\UseStatements::isImportUse
 * @covers \PHPCSUtils\Utils\UseStatements::isClosureUse
 * @covers \PHPCSUtils\Utils\UseStatements::getType
 *
 * @group usestatements
 *
 * @since 1.0.0
 */
class UseTypeTest extends UtilityMethodTestCase
{

    /**
     * Test receiving an expected exception when passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_USE');

        UseStatements::getType(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an expected exception when passing a non T_USE token.
     *
     * @return void
     */
    public function testNonUseToken()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_USE');

        $result = UseStatements::getType(self::$phpcsFile, 0);
    }

    /**
     * Test correctly identifying whether a T_USE token is used as a closure use statement.
     *
     * @dataProvider dataUseType
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected return values for the various functions.
     *
     * @return void
     */
    public function testIsClosureUse($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_USE);

        $result = UseStatements::isClosureUse(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['closure'], $result);
    }

    /**
     * Test correctly identifying whether a T_USE token is used as an import use statement.
     *
     * @dataProvider dataUseType
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected return values for the various functions.
     *
     * @return void
     */
    public function testIsImportUse($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_USE);

        $result = UseStatements::isImportUse(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['import'], $result);
    }

    /**
     * Test correctly identifying whether a T_USE token is used as a trait import use statement.
     *
     * @dataProvider dataUseType
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected return values for the various functions.
     *
     * @return void
     */
    public function testIsTraitUse($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_USE);

        $result = UseStatements::isTraitUse(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['trait'], $result, 'isTraitUseStatement() test failed');
    }

    /**
     * Data provider.
     *
     * @see testIsClosureUse() For the array format.
     * @see testIsImportUse()  For the array format.
     * @see testIsTraitUse()   For the array format.
     *
     * @return array
     */
    public function dataUseType()
    {
        return [
            'import-1' => [
                '/* testUseImport1 */',
                [
                    'closure' => false,
                    'import'  => true,
                    'trait'   => false,
                ],
            ],
            'import-2' => [
                '/* testUseImport2 */',
                [
                    'closure' => false,
                    'import'  => true,
                    'trait'   => false,
                ],
            ],
            'import-3' => [
                '/* testUseImport3 */',
                [
                    'closure' => false,
                    'import'  => true,
                    'trait'   => false,
                ],
            ],
            'import-4' => [
                '/* testUseImport4 */',
                [
                    'closure' => false,
                    'import'  => true,
                    'trait'   => false,
                ],
            ],
            'closure' => [
                '/* testClosureUse */',
                [
                    'closure' => true,
                    'import'  => false,
                    'trait'   => false,
                ],
            ],
            'trait' => [
                '/* testUseTrait */',
                [
                    'closure' => false,
                    'import'  => false,
                    'trait'   => true,
                ],
            ],
            'closure-in-nested-class' => [
                '/* testClosureUseNestedInClass */',
                [
                    'closure' => true,
                    'import'  => false,
                    'trait'   => false,
                ],
            ],
            'trait-in-nested-anon-class' => [
                '/* testUseTraitInNestedAnonClass */',
                [
                    'closure' => false,
                    'import'  => false,
                    'trait'   => true,
                ],
            ],
            'trait-in-trait' => [
                '/* testUseTraitInTrait */',
                [
                    'closure' => false,
                    'import'  => false,
                    'trait'   => true,
                ],
            ],
            'closure-nested-in-trait' => [
                '/* testClosureUseNestedInTrait */',
                [
                    'closure' => true,
                    'import'  => false,
                    'trait'   => false,
                ],
            ],
            'trait-in-interface' => [
                '/* testUseTraitInInterface */',
                [
                    'closure' => false,
                    'import'  => false,
                    'trait'   => false,
                ],
            ],
            'live-coding' => [
                '/* testLiveCoding */',
                [
                    'closure' => false,
                    'import'  => false,
                    'trait'   => false,
                ],
            ],
        ];
    }
}
