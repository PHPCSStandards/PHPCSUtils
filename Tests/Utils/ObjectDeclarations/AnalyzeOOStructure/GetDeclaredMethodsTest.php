<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2024 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\ObjectDeclarations\AnalyzeOOStructure;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\Internal\Cache;
use PHPCSUtils\Tests\PolyfilledTestCase;
use PHPCSUtils\Utils\ObjectDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\ObjectDeclarations::getDeclaredMethods() method.
 *
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getDeclaredMethods
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::analyzeOOStructure
 *
 * @since 1.1.0
 */
final class GetDeclaredMethodsTest extends PolyfilledTestCase
{

    /**
     * Full path to the test case file associated with this test class.
     *
     * @var string
     */
    protected static $caseFile = '';

    /**
     * Initialize PHPCS & tokenize the test case file.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = __DIR__ . '/AnalyzeOOStructureTest.inc';
        parent::setUpTestFile();
    }

    /**
     * Test receiving an expected exception when a non-integer token is passed.
     *
     * @return void
     */
    public function testNonIntegerToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type integer, boolean given');

        ObjectDeclarations::getDeclaredMethods(self::$phpcsFile, false);
    }

    /**
     * Test receiving an expected exception when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object, 100000 given'
        );

        ObjectDeclarations::getDeclaredMethods(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an expected exception when a non-OO token is passed.
     *
     * @return void
     */
    public function testNotTargetToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be of type T_CLASS, T_ANON_CLASS, T_INTERFACE, T_TRAIT or T_ENUM;'
        );

        $stackPtr = $this->getTargetToken('/* testUnacceptableToken */', \T_FUNCTION);
        ObjectDeclarations::getDeclaredMethods(self::$phpcsFile, $stackPtr);
    }

    /**
     * Test retrieving the methods declared in an OO structure.
     *
     * @dataProvider dataGetDeclaredMethods
     *
     * @param string                $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, string> $expected   Expected function return value.
     *
     * @return void
     */
    public function testGetDeclaredMethods($testMarker, $expected)
    {
        // Translate the method markers to token pointers.
        foreach ($expected as $name => $marker) {
            $expected[$name] = $this->getTargetToken($marker, [\T_FUNCTION]);
        }

        $stackPtr = $this->getTargetToken($testMarker, Tokens::$ooScopeTokens);
        $result   = ObjectDeclarations::getDeclaredMethods(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetDeclaredMethods() For the array format.
     *
     * @return array<string, array<string, string|array<string, string>>>
     */
    public static function dataGetDeclaredMethods()
    {
        return [
            'empty class' => [
                'testMarker' => '/* testEmptyClass */',
                'expected'   => [],
            ],
            'empty interface' => [
                'testMarker' => '/* testEmptyInterface */',
                'expected'   => [],
            ],
            'empty trait' => [
                'testMarker' => '/* testEmptyTrait */',
                'expected'   => [],
            ],
            'empty enum' => [
                'testMarker' => '/* testEmptyEnum */',
                'expected'   => [],
            ],
            'empty anonymous class' => [
                'testMarker' => '/* testEmptyAnonClass */',
                'expected'   => [],
            ],
            'class with constants, properties, methods and everything else' => [
                'testMarker' => '/* testClass */',
                'expected'   => [
                    'testDocblockSkipping'             => '/* markerClassMethod1 */',
                    'testAttributesSkipping'           => '/* markerClassMethod2 */',
                    'testDocblockAndAttributeSkipping' => '/* markerClassMethod3 */',
                    'testFunctionKeywordUsedInParams'  => '/* markerClassMethod4 */',
                    'function'                         => '/* markerClassMethod5A */',
                    'const'                            => '/* markerClassMethod5B */',
                    'case'                             => '/* markerClassMethod5C */',
                    'skipOverContentsOfFunctionA'      => '/* markerClassMethod6 */',
                    'skipOverContentsOfFunctionB'      => '/* markerClassMethod7 */',
                    'skipOverContentsOfFunctionC'      => '/* markerClassMethod8 */',
                    '__construct'                      => '/* markerClassMethod9 */',
                    'PrivateFunction'                  => '/* markerClassMethod10 */',
                    'FinalPublic'                      => '/* markerClassMethod11 */',
                    'implementMe'                      => '/* markerClassMethod12 */',
                ],
            ],
            'anon class with methods, nested within a method of another class' => [
                'testMarker' => '/* testAnonClass */',
                'expected'   => [
                    '__isset'     => '/* markerAnonNestedMethod1 */',
                    '__get'       => '/* markerAnonNestedMethod2 */',
                    '__set'       => '/* markerAnonNestedMethod3 */',
                    '__unset'     => '/* markerAnonNestedMethod4 */',
                    'doSomething' => '/* markerAnonNestedMethod5 */',
                ],
            ],
            'interface with constants and methods' => [
                'testMarker' => '/* testInterface */',
                'expected'   => [
                    'oh_oh'    => '/* markerInterfaceMethod1 */',
                    'im'       => '/* markerInterfaceMethod2 */',
                    'walking'  => '/* markerInterfaceMethod3 */',
                    'on'       => '/* markerInterfaceMethod4 */',
                    'sunshine' => '/* markerInterfaceMethod5 */',
                ],
            ],
            'trait with constants, properties and methods' => [
                'testMarker' => '/* testTrait */',
                'expected'   => [
                    'ti'        => '/* markerTraitMethod1 */',
                    'DO'        => '/* markerTraitMethod2 */',
                    'doReMiFa'  => '/* markerTraitMethod3 */',
                    'solLaTiDo' => '/* markerTraitMethod4 */',
                ],
            ],
            'anon class with constants, properties and methods in unconventional order' => [
                'testMarker' => '/* testAnonClassUnconventionalOrder */',
                'expected'   => [
                    'hereIGoAgain'     => '/* markerAnonOrderMethod1 */',
                    'LikeADrifter'     => '/* markerAnonOrderMethod2 */',
                    'Iaint'            => '/* markerAnonOrderMethod3 */',
                    'AndImGonnaHoldOn' => '/* markerAnonOrderMethod4 */',
                ],
            ],
            'enum with constants, cases and methods' => [
                'testMarker' => '/* testEnum */',
                'expected'   => [
                    'color'        => '/* markerEnumMethod1 */',
                    'offsetGet'    => '/* markerEnumMethod2 */',
                    'offsetExists' => '/* markerEnumMethod3 */',
                    'offsetSet'    => '/* markerEnumMethod4 */',
                    'offsetUnset'  => '/* markerEnumMethod5 */',
                ],
            ],
            'class with constructor, no properties, no params' => [
                'testMarker' => '/* testClassConstructorNoParams */',
                'expected'   => [
                    '__construct' => '/* markerCPP1_Constructor */',
                ],
            ],
            'interface with constructor, no properties, has params' => [
                'testMarker' => '/* testInterfaceConstructorWithParamsNotProperties */',
                'expected'   => [
                    '__Construct' => '/* markerCPP2_Constructor */',
                ],
            ],
            'class with constructor, with properties, no params' => [
                'testMarker' => '/* testClassConstructorWithProperties */',
                'expected'   => [
                    '__CONSTRUCT' => '/* markerCPP3_Constructor */',
                ],
            ],
            'trait with constructor, with properties, with params' => [
                'testMarker' => '/* testTraitConstructorWithParamsAndProperties */',
                'expected'   => [
                    '__construct' => '/* markerCPP4_Constructor */',
                ],
            ],
        ];
    }

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @return void
     */
    public function testGetDeclaredMethodsResultIsCached()
    {
        $methodName = 'PHPCSUtils\\Utils\\ObjectDeclarations::analyzeOOStructure';
        $cases      = $this->dataGetDeclaredMethods();
        $testMarker = $cases['class with constants, properties, methods and everything else']['testMarker'];
        $expected   = $cases['class with constants, properties, methods and everything else']['expected'];

        // Translate the method markers to token pointers.
        foreach ($expected as $name => $marker) {
            $expected[$name] = $this->getTargetToken($marker, [\T_FUNCTION]);
        }

        $stackPtr = $this->getTargetToken($testMarker, Tokens::$ooScopeTokens);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = ObjectDeclarations::getDeclaredMethods(self::$phpcsFile, $stackPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, $stackPtr);
        $resultSecondRun = ObjectDeclarations::getDeclaredMethods(self::$phpcsFile, $stackPtr);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }
}
