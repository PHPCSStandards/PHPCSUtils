<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\UseStatements;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\UseStatements;

/**
 * Tests for the \PHPCSUtils\Utils\UseStatements::splitImportUseStatement() method.
 *
 * @covers \PHPCSUtils\Utils\UseStatements::splitImportUseStatement
 *
 * @group usestatements
 *
 * @since 1.0.0
 */
class SplitImportUseStatementTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_USE');

        UseStatements::splitImportUseStatement(self::$phpcsFile, 10000);
    }

    /**
     * Test receiving an expected exception when a non-supported token is passed.
     *
     * @return void
     */
    public function testInvalidTokenPassed()
    {
        $this->expectPhpcsException('$stackPtr must be of type T_USE');

        // 0 = PHP open tag.
        UseStatements::splitImportUseStatement(self::$phpcsFile, 0);
    }

    /**
     * Test receiving an expected exception when a non-import use statement token is passed.
     *
     * @dataProvider dataNonImportUseTokenPassed
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @return void
     */
    public function testNonImportUseTokenPassed($testMarker)
    {
        $this->expectPhpcsException('$stackPtr must be an import use statement');

        $stackPtr = $this->getTargetToken($testMarker, \T_USE);
        UseStatements::splitImportUseStatement(self::$phpcsFile, $stackPtr);
    }

    /**
     * Data provider.
     *
     * @see testSplitImportUseStatement() For the array format.
     *
     * @return array
     */
    public function dataNonImportUseTokenPassed()
    {
        return [
            'closure-use' => ['/* testClosureUse */'],
            'trait-use'   => ['/* testTraitUse */'],
        ];
    }

    /**
     * Test correctly splitting a T_USE statement into individual statements.
     *
     * @dataProvider dataSplitImportUseStatement
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   The expected return value of the function.
     *
     * @return void
     */
    public function testSplitImportUseStatement($testMarker, $expected)
    {
        $stackPtr = $this->getTargetToken($testMarker, \T_USE);
        $result   = UseStatements::splitImportUseStatement(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testSplitImportUseStatement() For the array format.
     *
     * @return array
     */
    public function dataSplitImportUseStatement()
    {
        return [
            'plain' => [
                '/* testUsePlain */',
                [
                    'name'     => ['MyClass' => 'MyNamespace\MyClass'],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'plain-aliased' => [
                '/* testUsePlainAliased */',
                [
                    'name'     => ['ClassAlias' => 'MyNamespace\YourClass'],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'multiple-with-comments' => [
                '/* testUseMultipleWithComments */',
                [
                    'name'     => [
                        'ClassABC'   => 'Vendor\Foo\ClassA',
                        'InterfaceB' => 'Vendor\Bar\InterfaceB',
                        'ClassC'     => 'Vendor\Baz\ClassC',
                    ],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'function-plain-ends-on-close-tag' => [
                '/* testUseFunctionPlainEndsOnCloseTag */',
                [
                    'name'     => [],
                    'function' => ['myFunction' => 'MyNamespace\myFunction'],
                    'const'    => [],
                ],
            ],
            'function-plain-aliased' => [
                '/* testUseFunctionPlainAliased */',
                [
                    'name'     => [],
                    'function' => ['FunctionAlias' => 'Vendor\YourNamespace\yourFunction'],
                    'const'    => [],
                ],
            ],
            'function-multiple' => [
                '/* testUseFunctionMultiple */',
                [
                    'name'     => [],
                    'function' => [
                        'sin'    => 'foo\math\sin',
                        'FooCos' => 'foo\math\cos',
                        'cosh'   => 'foo\math\cosh',
                    ],
                    'const'    => [],
                ],
            ],
            'const-plain-uppercase-const-keyword' => [
                '/* testUseConstPlainUppercaseConstKeyword */',
                [
                    'name'     => [],
                    'function' => [],
                    'const'    => ['MY_CONST' => 'MyNamespace\MY_CONST'],
                ],
            ],
            'const-plain-aliased' => [
                '/* testUseConstPlainAliased */',
                [
                    'name'     => [],
                    'function' => [],
                    'const'    => ['CONST_ALIAS' => 'MyNamespace\YOUR_CONST'],
                ],
            ],
            'const-multiple' => [
                '/* testUseConstMultiple */',
                [
                    'name'     => [],
                    'function' => [],
                    'const'    => [
                        'PI'          => 'foo\math\PI',
                        'MATH_GOLDEN' => 'foo\math\GOLDEN_RATIO',
                    ],
                ],
            ],
            'group' => [
                '/* testGroupUse */',
                [
                    'name'     => [
                        'SomeClassA' => 'some\namespacing\SomeClassA',
                        'SomeClassB' => 'some\namespacing\deeper\level\SomeClassB',
                        'C'          => 'some\namespacing\another\level\SomeClassC',
                    ],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'group-function-trailing-comma' => [
                '/* testGroupUseFunctionTrailingComma */',
                [
                    'name'     => [],
                    'function' => [
                        'Msin'   => 'bar\math\Msin',
                        'BarCos' => 'bar\math\level\Mcos',
                        'Mcosh'  => 'bar\math\Mcosh',
                    ],
                    'const'    => [],
                ],
            ],
            'group-const' => [
                '/* testGroupUseConst */',
                [
                    'name'     => [],
                    'function' => [],
                    'const'    => [
                        'BAR_GAMMA'     => 'bar\math\BGAMMA',
                        'BGOLDEN_RATIO' => 'bar\math\BGOLDEN_RATIO',
                    ],
                ],
            ],
            'group-mixed' => [
                '/* testGroupUseMixed */',
                [
                    'name'     => [
                        'ClassName'    => 'Some\NS\ClassName',
                        'AnotherLevel' => 'Some\NS\AnotherLevel',
                    ],
                    'function' => [
                        'functionName' => 'Some\NS\SubLevel\functionName',
                        'AnotherName'  => 'Some\NS\SubLevel\AnotherName',
                    ],
                    'const'    => ['SOME_CONSTANT' => 'Some\NS\Constants\CONSTANT_NAME'],
                ],
            ],
            'parse-error-function-plain-reserved-keyword' => [
                '/* testUseFunctionPlainReservedKeyword */',
                [
                    'name'     => [],
                    'function' => ['yourFunction' => 'Vendor\YourNamespace\switch\yourFunction'],
                    'const'    => [],
                ],
            ],
            'parse-error-const-plain-reserved-keyword' => [
                '/* testUseConstPlainReservedKeyword */',
                [
                    'name'     => [],
                    'function' => [],
                    'const'    => ['yourConst' => 'Vendor\YourNamespace\function\yourConst'],
                ],
            ],
            'parse-error-plain-alias-reserved-keyword' => [
                '/* testUsePlainAliasReservedKeyword */',
                [
                    'name'     => ['class' => 'Vendor\YourNamespace\ClassName'],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'parse-error-plain-alias-reserved-keyword-function' => [
                '/* testUsePlainAliasReservedKeywordFunction */',
                [
                    'name'     => ['function' => 'Vendor\YourNamespace\ClassName'],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'parse-error-plain-alias-reserved-keyword-const' => [
                '/* testUsePlainAliasReservedKeywordConst */',
                [
                    'name'     => [],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'parse-error' => [
                '/* testParseError */',
                [
                    'name'     => [],
                    'function' => [],
                    'const'    => [],
                ],
            ],
        ];
    }
}
