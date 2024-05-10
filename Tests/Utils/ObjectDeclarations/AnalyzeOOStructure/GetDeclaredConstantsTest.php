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
 * Tests for the \PHPCSUtils\Utils\ObjectDeclarations::getDeclaredConstants() method.
 *
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getDeclaredConstants
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::analyzeOOStructure
 *
 * @since 1.1.0
 */
final class GetDeclaredConstantsTest extends PolyfilledTestCase
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

        ObjectDeclarations::getDeclaredConstants(self::$phpcsFile, false);
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

        ObjectDeclarations::getDeclaredConstants(self::$phpcsFile, 100000);
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
        ObjectDeclarations::getDeclaredConstants(self::$phpcsFile, $stackPtr);
    }

    /**
     * Test retrieving the constants declared in an OO structure.
     *
     * @dataProvider dataGetDeclaredConstants
     *
     * @param string                $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, string> $expected   Expected function return value.
     *
     * @return void
     */
    public function testGetDeclaredConstants($testMarker, $expected)
    {
        // Translate the constant markers to token pointers.
        foreach ($expected as $name => $marker) {
            $expected[$name] = $this->getTargetToken($marker, [\T_CONST]);
        }

        $stackPtr = $this->getTargetToken($testMarker, Tokens::$ooScopeTokens);
        $result   = ObjectDeclarations::getDeclaredConstants(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetDeclaredConstants() For the array format.
     *
     * @return array<string, array<string, string|array<string, string>>>
     */
    public static function dataGetDeclaredConstants()
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
                    'CONST'       => '/* markerClassConst1 */',
                    'CASE'        => '/* markerClassConst2 */',
                    'FINAL'       => '/* markerClassConst3 */',
                    'FUNCTION'    => '/* markerClassConst4 */',
                    'UNION_TYPED' => '/* markerClassConst5 */',
                    'DNF_TYPED'   => '/* markerClassConst6 */',
                ],
            ],
            'anonymous class with constants' => [
                'testMarker' => '/* testAnonClass */',
                'expected'   => [],
            ],
            'interface with constants and methods' => [
                'testMarker' => '/* testInterface */',
                'expected'   => [
                    'IM'       => '/* markerInterfaceConst1 */',
                    'WALKING'  => '/* markerInterfaceConst2 */',
                    'ON'       => '/* markerInterfaceConst3 */',
                    'SUNSHINE' => '/* markerInterfaceConst4 */',
                ],
            ],
            'trait with constants, properties and methods' => [
                'testMarker' => '/* testTrait */',
                'expected'   => [
                    'DO' => '/* markerTraitConst1 */',
                    'RE' => '/* markerTraitConst2 */',
                    'mi' => '/* markerTraitConst3 */',
                ],
            ],
            'anon class with constants, properties and methods in unconventional order' => [
                'testMarker' => '/* testAnonClassUnconventionalOrder */',
                'expected'   => [
                    'AndIveMadeUp'     => '/* markerAnonOrderConst1 */',
                    'WAITING_ON_LOVES' => '/* markerAnonOrderConst2 */',
                ],
            ],
            'enum with constants, cases and methods' => [
                'testMarker' => '/* testEnum */',
                'expected'   => [
                    'SUIT'    => '/* markerEnumConst1 */',
                    'ANOTHER' => '/* markerEnumConst2 */',
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
                'expected'   => [],
            ],
            'trait with constructor, with properties, with params' => [
                'testMarker' => '/* testTraitConstructorWithParamsAndProperties */',
                'expected'   => [],
            ],
        ];
    }

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @return void
     */
    public function testGetDeclaredConstantsResultIsCached()
    {
        $methodName = 'PHPCSUtils\\Utils\\ObjectDeclarations::analyzeOOStructure';
        $cases      = $this->dataGetDeclaredConstants();
        $testMarker = $cases['class with constants, properties, methods and everything else']['testMarker'];
        $expected   = $cases['class with constants, properties, methods and everything else']['expected'];

        // Translate the constant markers to token pointers.
        foreach ($expected as $name => $marker) {
            $expected[$name] = $this->getTargetToken($marker, [\T_CONST]);
        }

        $stackPtr = $this->getTargetToken($testMarker, Tokens::$ooScopeTokens);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = ObjectDeclarations::getDeclaredConstants(self::$phpcsFile, $stackPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, $stackPtr);
        $resultSecondRun = ObjectDeclarations::getDeclaredConstants(self::$phpcsFile, $stackPtr);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }
}
