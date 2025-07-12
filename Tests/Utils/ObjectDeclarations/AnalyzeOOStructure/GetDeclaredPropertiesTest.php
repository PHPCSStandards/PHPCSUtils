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
 * Tests for the \PHPCSUtils\Utils\ObjectDeclarations::getDeclaredProperties() method.
 *
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getDeclaredProperties
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::analyzeOOStructure
 *
 * @since 1.1.0
 */
final class GetDeclaredPropertiesTest extends PolyfilledTestCase
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

        ObjectDeclarations::getDeclaredProperties(self::$phpcsFile, false);
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

        ObjectDeclarations::getDeclaredProperties(self::$phpcsFile, 100000);
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
        ObjectDeclarations::getDeclaredProperties(self::$phpcsFile, $stackPtr);
    }

    /**
     * Test retrieving the properties declared in an OO structure.
     *
     * @dataProvider dataGetDeclaredProperties
     *
     * @param string                $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, string> $expected   Expected function return value.
     *
     * @return void
     */
    public function testGetDeclaredProperties($testMarker, $expected)
    {
        // Translate the method marker to token pointers.
        foreach ($expected as $name => $marker) {
            $expected[$name] = $this->getTargetToken($marker, [\T_VARIABLE]);
        }

        $stackPtr = $this->getTargetToken($testMarker, Tokens::$ooScopeTokens);
        $result   = ObjectDeclarations::getDeclaredProperties(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetDeclaredProperties() For the array format.
     *
     * @return array<string, array<string, string|array<string, string>>>
     */
    public static function dataGetDeclaredProperties()
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
                    '$prop'      => '/* markerClassProperty1 */',
                    '$property'  => '/* markerClassProperty2 */',
                    '$promotion' => '/* markerClassProperty3 */',
                ],
            ],
            'anonymous class with properties' => [
                'testMarker' => '/* testAnonClass */',
                'expected'   => [
                    '$sink' => '/* markerAnonNestedProperty1 */',
                ],
            ],
            'interface with constants and methods' => [
                'testMarker' => '/* testInterface */',
                'expected'   => [
                    '$invalid' => '/* markerInterfaceIllegalProperty */',
                ],
            ],
            'trait with constants, properties and methods' => [
                'testMarker' => '/* testTrait */',
                'expected'   => [
                    '$fa'  => '/* markerTraitProperty1 */',
                    '$sol' => '/* markerTraitProperty2 */',
                    '$LA'  => '/* markerTraitProperty3 */',
                ],
            ],
            'anon class with constants, properties and methods in unconventional order' => [
                'testMarker' => '/* testAnonClassUnconventionalOrder */',
                'expected'   => [
                    '$goingDownTheOnlyRoad' => '/* markerAnonOrderProperty1 */',
                    '$inNeedOfRescue'       => '/* markerAnonOrderProperty2 */',
                ],
            ],
            'enum with constants, cases and methods' => [
                'testMarker' => '/* testEnum */',
                'expected'   => [
                    '$invalid' => '/* markerEnumIllegalProperty */',
                ],
            ],
            'class with constructor, no properties, no params' => [
                'testMarker' => '/* testClassConstructorNoParams */',
                'expected'   => [],
            ],
            'interface with constructor, no properties, has params' => [
                'testMarker' => '/* testInterfaceConstructorWithParamsNotProperties */',
                'expected'   => [],
            ],
            'class with constructor, with properties, no params' => [
                'testMarker' => '/* testClassConstructorWithProperties */',
                'expected'   => [
                    '$propA' => '/* markerCPP3_Property1 */',
                    '$propB' => '/* markerCPP3_Property2 */',
                    '$propC' => '/* markerCPP3_Property3 */',
                    '$propD' => '/* markerCPP3_Property4 */',
                ],
            ],
            'trait with constructor, with properties, with params' => [
                'testMarker' => '/* testTraitConstructorWithParamsAndProperties */',
                'expected'   => [
                    '$declared' => '/* markerCPP4_Property1 */',
                    '$instance' => '/* markerCPP4_Property2 */',
                    '$propA'    => '/* markerCPP4_Property3 */',
                    '$propB'    => '/* markerCPP4_Property4 */',
                    '$propC'    => '/* markerCPP4_Property5 */',
                ],
            ],
            'class with multi-property declaration' => [
                'testMarker' => '/* testMultiPropertyDeclarations */',
                'expected'   => [
                    '$propA' => '/* markerClassMultiProperty1 */',
                    '$propB' => '/* markerClassMultiProperty2 */',
                    '$propC' => '/* markerClassMultiProperty3 */',
                    '$propD' => '/* markerClassMultiProperty4 */',
                    '$propE' => '/* markerClassMultiProperty5 */',
                ],
            ],
        ];
    }

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @return void
     */
    public function testGetDeclaredPropertiesResultIsCached()
    {
        // The test case used is specifically selected as the raw and the clean param values will be the same.
        $methodName = 'PHPCSUtils\\Utils\\ObjectDeclarations::analyzeOOStructure';
        $cases      = $this->dataGetDeclaredProperties();
        $testMarker = $cases['class with constants, properties, methods and everything else']['testMarker'];
        $expected   = $cases['class with constants, properties, methods and everything else']['expected'];

        // Translate the property markers to token pointers.
        foreach ($expected as $name => $marker) {
            $expected[$name] = $this->getTargetToken($marker, [\T_VARIABLE]);
        }

        $stackPtr = $this->getTargetToken($testMarker, Tokens::$ooScopeTokens);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = ObjectDeclarations::getDeclaredProperties(self::$phpcsFile, $stackPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, $stackPtr);
        $resultSecondRun = ObjectDeclarations::getDeclaredProperties(self::$phpcsFile, $stackPtr);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }
}
