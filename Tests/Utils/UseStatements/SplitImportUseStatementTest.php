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

use PHPCSUtils\Internal\Cache;
use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\UseStatements;

/**
 * Tests for the \PHPCSUtils\Utils\UseStatements::splitImportUseStatement() method.
 *
 * @covers \PHPCSUtils\Utils\UseStatements::splitImportUseStatement
 *
 * @since 1.0.0
 */
final class SplitImportUseStatementTest extends PolyfilledTestCase
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

        UseStatements::splitImportUseStatement(self::$phpcsFile, null);
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
            'Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object, 10000 given'
        );

        UseStatements::splitImportUseStatement(self::$phpcsFile, 10000);
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
        $this->expectException('PHPCSUtils\Exceptions\ValueError');
        $this->expectExceptionMessage('The value of argument #2 ($stackPtr) must be the pointer to an import use statement');

        $stackPtr = $this->getTargetToken($testMarker, \T_USE);
        UseStatements::splitImportUseStatement(self::$phpcsFile, $stackPtr);
    }

    /**
     * Data provider.
     *
     * @see testSplitImportUseStatement() For the array format.
     *
     * @return array<string, array<string>>
     */
    public static function dataNonImportUseTokenPassed()
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
     * @param string                               $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, array<string, string>> $expected   The expected return value of the function.
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
     * @return array<string, array<string, string|array<string, array<string, string>>>>
     */
    public static function dataSplitImportUseStatement()
    {
        return [
            'plain' => [
                'testMarker' => '/* testUsePlain */',
                'expected'   => [
                    'name'     => ['MyClass' => 'MyNamespace\MyClass'],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'plain-aliased' => [
                'testMarker' => '/* testUsePlainAliased */',
                'expected'   => [
                    'name'     => ['ClassAlias' => 'MyNamespace\YourClass'],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'plain-with-leading-backslash' => [
                'testMarker' => '/* testUsePlainLeadingBackslash */',
                'expected'   => [
                    'name'     => ['TheirClass' => 'MyNamespace\TheirClass'],
                    'function' => [],
                    'const'    => [],
                ],
            ],

            'multiple-with-comments' => [
                'testMarker' => '/* testUseMultipleWithComments */',
                'expected'   => [
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
                'testMarker' => '/* testUseFunctionPlainEndsOnCloseTag */',
                'expected'   => [
                    'name'     => [],
                    'function' => ['myFunction' => 'MyNamespace\myFunction'],
                    'const'    => [],
                ],
            ],
            'function-plain-aliased' => [
                'testMarker' => '/* testUseFunctionPlainAliased */',
                'expected'   => [
                    'name'     => [],
                    'function' => ['FunctionAlias' => 'Vendor\YourNamespace\yourFunction'],
                    'const'    => [],
                ],
            ],
            'function-multiple' => [
                'testMarker' => '/* testUseFunctionMultiple */',
                'expected'   => [
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
                'testMarker' => '/* testUseConstPlainUppercaseConstKeyword */',
                'expected'   => [
                    'name'     => [],
                    'function' => [],
                    'const'    => ['MY_CONST' => 'MyNamespace\MY_CONST'],
                ],
            ],
            'const-plain-aliased' => [
                'testMarker' => '/* testUseConstPlainAliased */',
                'expected'   => [
                    'name'     => [],
                    'function' => [],
                    'const'    => ['CONST_ALIAS' => 'MyNamespace\YOUR_CONST'],
                ],
            ],
            'const-multiple' => [
                'testMarker' => '/* testUseConstMultiple */',
                'expected'   => [
                    'name'     => [],
                    'function' => [],
                    'const'    => [
                        'PI'          => 'foo\math\PI',
                        'MATH_GOLDEN' => 'foo\math\GOLDEN_RATIO',
                    ],
                ],
            ],
            'group' => [
                'testMarker' => '/* testGroupUse */',
                'expected'   => [
                    'name'     => [
                        'SomeClassA' => 'some\namespacing\SomeClassA',
                        'SomeClassB' => 'some\namespacing\deeper\level\SomeClassB',
                        'C'          => 'some\namespacing\another\level\SomeClassC',
                    ],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'group-with-leading-backslash' => [
                'testMarker' => '/* testGroupUseLeadingBackslash */',
                'expected'   => [
                    'name'     => [
                        'SomeClassA' => 'world\namespacing\SomeClassA',
                        'SomeClassB' => 'world\namespacing\deeper\level\SomeClassB',
                    ],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'group-function-trailing-comma' => [
                'testMarker' => '/* testGroupUseFunctionTrailingComma */',
                'expected'   => [
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
                'testMarker' => '/* testGroupUseConst */',
                'expected'   => [
                    'name'     => [],
                    'function' => [],
                    'const'    => [
                        'BAR_GAMMA'     => 'bar\math\BGAMMA',
                        'BGOLDEN_RATIO' => 'bar\math\BGOLDEN_RATIO',
                    ],
                ],
            ],
            'group-mixed' => [
                'testMarker' => '/* testGroupUseMixed */',
                'expected'   => [
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

            'parse-error-plain-reserved-keyword' => [
                'testMarker' => '/* testUsePlainReservedKeyword */',
                'expected'   => [
                    'name'     => ['ClassName' => 'Vendor\break\ClassName'],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'parse-error-function-plain-reserved-keyword' => [
                'testMarker' => '/* testUseFunctionPlainReservedKeyword */',
                'expected'   => [
                    'name'     => [],
                    'function' => ['yourFunction' => 'Vendor\YourNamespace\switch\yourFunction'],
                    'const'    => [],
                ],
            ],
            'parse-error-const-plain-reserved-keyword' => [
                'testMarker' => '/* testUseConstPlainReservedKeyword */',
                'expected'   => [
                    'name'     => [],
                    'function' => [],
                    'const'    => ['yourConst' => 'Vendor\YourNamespace\function\yourConst'],
                ],
            ],
            'parse-error-plain-alias-reserved-keyword' => [
                'testMarker' => '/* testUsePlainAliasReservedKeyword */',
                'expected'   => [
                    'name'     => ['class' => 'Vendor\YourNamespace\ClassName'],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'parse-error-plain-alias-reserved-keyword-function' => [
                'testMarker' => '/* testUsePlainAliasReservedKeywordFunction */',
                'expected'   => [
                    'name'     => ['function' => 'Vendor\YourNamespace\ClassName'],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'parse-error-plain-alias-reserved-keyword-const' => [
                'testMarker' => '/* testUsePlainAliasReservedKeywordConst */',
                'expected'   => [
                    'name'     => ['const' => 'Vendor\YourNamespace\ClassName'],
                    'function' => [],
                    'const'    => [],
                ],
            ],
            'parse-error' => [
                'testMarker' => '/* testParseError */',
                'expected'   => [
                    'name'     => [],
                    'function' => [],
                    'const'    => [],
                ],
            ],
        ];
    }

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @return void
     */
    public function testResultIsCached()
    {
        $methodName = 'PHPCSUtils\\Utils\\UseStatements::splitImportUseStatement';
        $cases      = $this->dataSplitImportUseStatement();
        $testMarker = $cases['multiple-with-comments']['testMarker'];
        $expected   = $cases['multiple-with-comments']['expected'];

        $stackPtr = $this->getTargetToken($testMarker, \T_USE);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = UseStatements::splitImportUseStatement(self::$phpcsFile, $stackPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, $stackPtr);
        $resultSecondRun = UseStatements::splitImportUseStatement(self::$phpcsFile, $stackPtr);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }

    /**
     * Verify that the build-in caching is used when caching is enabled and a parse error is encountered.
     *
     * @return void
     */
    public function testResultIsCachedForParseError()
    {
        $methodName = 'PHPCSUtils\\Utils\\UseStatements::splitImportUseStatement';
        $cases      = $this->dataSplitImportUseStatement();
        $testMarker = $cases['parse-error']['testMarker'];
        $expected   = $cases['parse-error']['expected'];

        $stackPtr = $this->getTargetToken($testMarker, \T_USE);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = UseStatements::splitImportUseStatement(self::$phpcsFile, $stackPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, $stackPtr);
        $resultSecondRun = UseStatements::splitImportUseStatement(self::$phpcsFile, $stackPtr);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }
}
