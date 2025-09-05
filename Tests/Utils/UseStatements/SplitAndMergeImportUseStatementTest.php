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
 * Tests for the \PHPCSUtils\Utils\UseStatements::splitAndMergeImportUseStatement()
 * and the \PHPCSUtils\Utils\UseStatements::mergeImportUseStatements() method.
 *
 * @covers \PHPCSUtils\Utils\UseStatements::splitAndMergeImportUseStatement
 * @covers \PHPCSUtils\Utils\UseStatements::mergeImportUseStatements
 *
 * @since 1.0.0
 */
final class SplitAndMergeImportUseStatementTest extends PolyfilledTestCase
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

        UseStatements::splitAndMergeImportUseStatement(self::$phpcsFile, null, []);
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
            'Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object, 100000 given'
        );

        UseStatements::splitAndMergeImportUseStatement(self::$phpcsFile, 100000, []);
    }

    /**
     * Test receiving an expected exception when a non-supported token is passed.
     *
     * @return void
     */
    public function testInvalidTokenPassed()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type T_USE;');

        // 0 = PHP open tag.
        UseStatements::splitAndMergeImportUseStatement(self::$phpcsFile, 0, []);
    }

    /**
     * Test correctly splitting and merging a import `use` statements.
     *
     * @dataProvider dataSplitAndMergeImportUseStatementNonImportUse
     * @dataProvider dataSplitAndMergeImportUseStatement
     *
     * @param string                               $testMarker  The comment which prefaces the target token in the test file.
     * @param array<string, array<string, string>> $expected    The expected return value of the function.
     * @param array<string, array<string, string>> $previousUse Previous use statement parameter to pass to the method.
     *
     * @return void
     */
    public function testSplitAndMergeImportUseStatement($testMarker, $expected, $previousUse)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_USE);
        $result   = UseStatements::splitAndMergeImportUseStatement(self::$phpcsFile, $stackPtr, $previousUse);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testSplitAndMergeImportUseStatement() For the array format.
     *
     * @return array<string, array<string, string|array<string, array<string, string>>|array<string, string>>>
     */
    public static function dataSplitAndMergeImportUseStatementNonImportUse()
    {
        return [
            'closure-use-previous-empty-array' => [
                'testMarker'  => '/* testClosureUse */',
                'expected'    => [],
                'previousUse' => [],
            ],
            // Documenting that a "previous" array is not cleaned of unexpected keys.
            'closure-use-previous-non-empty-array-unexpected-keys' => [
                'testMarker'  => '/* testClosureUse */',
                'expected'    => [
                    'something' => 'else',
                ],
                'previousUse' => [
                    'something' => 'else',
                ],
            ],
            'closure-use-previous-base-array' => [
                'testMarker'  => '/* testClosureUse */',
                'expected'    => [
                    'name'     => [],
                    'function' => [],
                    'const'    => [],
                ],
                'previousUse' => [
                    'name'     => [],
                    'function' => [],
                    'const'    => [],
                ],
            ],
        ];
    }

    /**
     * Data provider.
     *
     * @see testSplitAndMergeImportUseStatement() For the array format.
     *
     * @return array<string, array<string, string|array<string, array<string, string>>>>
     */
    public static function dataSplitAndMergeImportUseStatement()
    {
        $data = [
            'name-plain' => [
                'testMarker'  => '/* testUseNamePlainAliased */',
                'expected'    => [
                    'name'     => ['ClassAlias' => 'MyNamespace\YourClass'],
                    'function' => [],
                    'const'    => [],
                ],
                'previousUse' => [],
            ],
            'function-plain' => [
                'testMarker' => '/* testUseFunctionPlain */',
                'expected'   => [
                    'name'     => ['ClassAlias' => 'MyNamespace\YourClass'],
                    'function' => ['myFunction' => 'MyNamespace\myFunction'],
                    'const'    => [],
                ],
            ],
            'const-plain' => [
                'testMarker' => '/* testUseConstPlain */',
                'expected'   => [
                    'name'     => ['ClassAlias' => 'MyNamespace\YourClass'],
                    'function' => ['myFunction' => 'MyNamespace\myFunction'],
                    'const'    => ['MY_CONST' => 'MyNamespace\MY_CONST'],
                ],
            ],
            'group-mixed' => [
                'testMarker' => '/* testGroupUseMixed */',
                'expected'   => [
                    'name'     => [
                        'ClassAlias'   => 'MyNamespace\YourClass',
                        'ClassName'    => 'Some\NS\ClassName',
                        'AnotherLevel' => 'Some\NS\AnotherLevel',
                    ],
                    'function' => [
                        'myFunction'   => 'MyNamespace\myFunction',
                        'functionName' => 'Some\NS\SubLevel\functionName',
                        'AnotherName'  => 'Some\NS\SubLevel\AnotherName',
                    ],
                    'const'    => [
                        'MY_CONST'      => 'MyNamespace\MY_CONST',
                        'SOME_CONSTANT' => 'Some\NS\Constants\CONSTANT_NAME',
                    ],
                ],
            ],
            'trait-use' => [
                'testMarker' => '/* testTraitUse */',
                // Same as previous.
                'expected'   => [
                    'name'     => [
                        'ClassAlias'   => 'MyNamespace\YourClass',
                        'ClassName'    => 'Some\NS\ClassName',
                        'AnotherLevel' => 'Some\NS\AnotherLevel',
                    ],
                    'function' => [
                        'myFunction'   => 'MyNamespace\myFunction',
                        'functionName' => 'Some\NS\SubLevel\functionName',
                        'AnotherName'  => 'Some\NS\SubLevel\AnotherName',
                    ],
                    'const'    => [
                        'MY_CONST'      => 'MyNamespace\MY_CONST',
                        'SOME_CONSTANT' => 'Some\NS\Constants\CONSTANT_NAME',
                    ],
                ],
            ],
        ];

        $previousUse = [
            'name'     => [],
            'function' => [],
            'const'    => [],
        ];
        foreach ($data as $key => $value) {
            if (isset($value['previousUse']) === false) {
                $data[$key]['previousUse'] = $previousUse;
            }

            $previousUse = $value['expected'];
        }

        return $data;
    }
}
