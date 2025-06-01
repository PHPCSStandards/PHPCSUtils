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

use PHPCSUtils\Tests\PolyfilledTestCase;
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
 * @since 1.0.0
 */
final class UseTypeTest extends PolyfilledTestCase
{

    /**
     * Test passing a non-integer token pointer.
     *
     * @return void
     */
    public function testNonIntegerToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type integer, NULL given');

        UseStatements::getType(self::$phpcsFile, null);
    }

    /**
     * Test receiving an expected exception when passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object, 100000 given'
        );

        UseStatements::getType(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an expected exception when passing a non T_USE token.
     *
     * @return void
     */
    public function testNonUseToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type T_USE;');

        UseStatements::getType(self::$phpcsFile, 0);
    }

    /**
     * Test correctly identifying whether a T_USE token is used as a closure use statement.
     *
     * @dataProvider dataUseType
     *
     * @param string              $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, bool> $expected   The expected return values for the various functions.
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
     * @param string              $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, bool> $expected   The expected return values for the various functions.
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
     * @param string              $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, bool> $expected   The expected return values for the various functions.
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
     * @return array<string, array<string, string|array<string, bool>>>
     */
    public static function dataUseType()
    {
        return [
            'import-1' => [
                'testMarker' => '/* testUseImport1 */',
                'expected'   => [
                    'closure' => false,
                    'import'  => true,
                    'trait'   => false,
                ],
            ],
            'import-2' => [
                'testMarker' => '/* testUseImport2 */',
                'expected'   => [
                    'closure' => false,
                    'import'  => true,
                    'trait'   => false,
                ],
            ],
            'import-3' => [
                'testMarker' => '/* testUseImport3 */',
                'expected'   => [
                    'closure' => false,
                    'import'  => true,
                    'trait'   => false,
                ],
            ],
            'import-4' => [
                'testMarker' => '/* testUseImport4 */',
                'expected'   => [
                    'closure' => false,
                    'import'  => true,
                    'trait'   => false,
                ],
            ],
            'closure' => [
                'testMarker' => '/* testClosureUse */',
                'expected'   => [
                    'closure' => true,
                    'import'  => false,
                    'trait'   => false,
                ],
            ],
            'trait' => [
                'testMarker' => '/* testUseTrait */',
                'expected'   => [
                    'closure' => false,
                    'import'  => false,
                    'trait'   => true,
                ],
            ],
            'closure-in-nested-class' => [
                'testMarker' => '/* testClosureUseNestedInClass */',
                'expected'   => [
                    'closure' => true,
                    'import'  => false,
                    'trait'   => false,
                ],
            ],
            'trait-in-nested-anon-class' => [
                'testMarker' => '/* testUseTraitInNestedAnonClass */',
                'expected'   => [
                    'closure' => false,
                    'import'  => false,
                    'trait'   => true,
                ],
            ],
            'trait-in-trait' => [
                'testMarker' => '/* testUseTraitInTrait */',
                'expected'   => [
                    'closure' => false,
                    'import'  => false,
                    'trait'   => true,
                ],
            ],
            'closure-nested-in-trait' => [
                'testMarker' => '/* testClosureUseNestedInTrait */',
                'expected'   => [
                    'closure' => true,
                    'import'  => false,
                    'trait'   => false,
                ],
            ],
            'trait-in-interface' => [
                'testMarker' => '/* testUseTraitInInterface */',
                'expected'   => [
                    'closure' => false,
                    'import'  => false,
                    'trait'   => false,
                ],
            ],

            // Tests related to a specific issue with scope setting in PHPCS 2.x.
            'parse-error-import-use-case-no-switch-1' => [
                'testMarker' => '/* testUseImportPHPCS2CaseNoSwitchA */',
                'expected'   => [
                    'closure' => false,
                    'import'  => true,
                    'trait'   => false,
                ],
            ],
            'parse-error-import-use-case-no-switch-2' => [
                'testMarker' => '/* testUseImportPHPCS2CaseNoSwitchB */',
                'expected'   => [
                    'closure' => false,
                    'import'  => true,
                    'trait'   => false,
                ],
            ],
            'parse-error-import-use-default-no-switch' => [
                'testMarker' => '/* testUseImportPHPCS2DefaultNoSwitchA */',
                'expected'   => [
                    'closure' => false,
                    'import'  => true,
                    'trait'   => false,
                ],
            ],
            'parse-error-trait-use-case-no-switch-1' => [
                'testMarker' => '/* testUseImportPHPCS2CaseNoSwitchC */',
                'expected'   => [
                    'closure' => false,
                    'import'  => false,
                    'trait'   => true,
                ],
            ],
            'parse-error-trait-use-case-no-switch-2' => [
                'testMarker' => '/* testUseImportPHPCS2CaseNoSwitchD */',
                'expected'   => [
                    'closure' => false,
                    'import'  => false,
                    'trait'   => true,
                ],
            ],
            'parse-error-trait-use-default-no-switch' => [
                'testMarker' => '/* testUseImportPHPCS2DefaultNoSwitchB */',
                'expected'   => [
                    'closure' => false,
                    'import'  => false,
                    'trait'   => true,
                ],
            ],
        ];
    }
}
