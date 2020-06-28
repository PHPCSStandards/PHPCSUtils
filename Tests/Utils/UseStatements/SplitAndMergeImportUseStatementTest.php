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
 * Tests for the \PHPCSUtils\Utils\UseStatements::splitAndMergeImportUseStatement() method.
 *
 * @covers \PHPCSUtils\Utils\UseStatements::splitAndMergeImportUseStatement
 *
 * @group usestatements
 *
 * @since 1.0.0
 */
class SplitAndMergeImportUseStatementTest extends UtilityMethodTestCase
{

    /**
     * Test correctly splitting and merging a import `use` statements.
     *
     * @dataProvider dataSplitAndMergeImportUseStatement
     *
     * @param string $testMarker  The comment which prefaces the target token in the test file.
     * @param array  $expected    The expected return value of the function.
     * @param array  $previousUse Previous use statement parameter to pass to the method.
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
     * @return array
     */
    public function dataSplitAndMergeImportUseStatement()
    {
        $data = [
            'name-plain' => [
                '/* testUseNamePlainAliased */',
                [
                    'name'     => ['ClassAlias' => 'MyNamespace\YourClass'],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'function-plain' => [
                '/* testUseFunctionPlain */',
                [
                    'name'     => ['ClassAlias' => 'MyNamespace\YourClass'],
                    'function' => ['myFunction' => 'MyNamespace\myFunction'],
                    'const'    => [],
                ],
            ],
            'const-plain' => [
                '/* testUseConstPlain */',
                [
                    'name'     => ['ClassAlias' => 'MyNamespace\YourClass'],
                    'function' => ['myFunction' => 'MyNamespace\myFunction'],
                    'const'    => ['MY_CONST' => 'MyNamespace\MY_CONST'],
                ],
            ],
            'group-mixed' => [
                '/* testGroupUseMixed */',
                [
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
                '/* testTraitUse */',
                // Same as previous.
                [
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

        $previousUse = [];
        foreach ($data as $key => $value) {
            $data[$key][] = $previousUse;
            $previousUse  = $value[1];
        }

        return $data;
    }
}
