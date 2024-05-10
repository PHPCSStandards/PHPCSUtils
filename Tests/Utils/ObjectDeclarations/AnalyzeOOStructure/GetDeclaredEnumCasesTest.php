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
 * Tests for the \PHPCSUtils\Utils\ObjectDeclarations::getDeclaredEnumCases() method.
 *
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getDeclaredEnumCases
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::analyzeOOStructure
 *
 * @since 1.1.0
 */
final class GetDeclaredEnumCasesTest extends PolyfilledTestCase
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

        ObjectDeclarations::getDeclaredEnumCases(self::$phpcsFile, false);
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

        ObjectDeclarations::getDeclaredEnumCases(self::$phpcsFile, 100000);
    }

    /**
     * Test receiving an expected exception when a non-OO token is passed.
     *
     * @return void
     */
    public function testNotTargetToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type T_ENUM;');

        $stackPtr = $this->getTargetToken('/* testUnacceptableToken */', \T_FUNCTION);
        ObjectDeclarations::getDeclaredEnumCases(self::$phpcsFile, $stackPtr);
    }

    /**
     * Test retrieving the cases declared in an enum.
     *
     * @dataProvider dataGetDeclaredEnumCases
     *
     * @param string                $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, string> $expected   Expected function return value.
     *
     * @return void
     */
    public function testGetDeclaredEnumCases($testMarker, $expected)
    {
        // Translate the case markers to token pointers.
        foreach ($expected as $name => $marker) {
            $expected[$name] = $this->getTargetToken($marker, [\T_ENUM_CASE]);
        }

        $stackPtr = $this->getTargetToken($testMarker, Tokens::$ooScopeTokens);
        $result   = ObjectDeclarations::getDeclaredEnumCases(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetDeclaredEnumCases() For the array format.
     *
     * @return array<string, array<string, string|array<string, string>>>
     */
    public static function dataGetDeclaredEnumCases()
    {
        return [
            'empty enum' => [
                'testMarker' => '/* testEmptyEnum */',
                'expected'   => [],
            ],
            'enum with constants, cases and methods' => [
                'testMarker' => '/* testEnum */',
                'expected'   => [
                    'Hearts'   => '/* markerEnumCase1 */',
                    'Diamonds' => '/* markerEnumCase2 */',
                    'Clubs'    => '/* markerEnumCase3 */',
                    'Spades'   => '/* markerEnumCase4 */',
                ],
            ],
        ];
    }

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @return void
     */
    public function testGetDeclaredEnumCasesResultIsCached()
    {
        $methodName = 'PHPCSUtils\\Utils\\ObjectDeclarations::analyzeOOStructure';
        $cases      = $this->dataGetDeclaredEnumCases();
        $testMarker = $cases['enum with constants, cases and methods']['testMarker'];
        $expected   = $cases['enum with constants, cases and methods']['expected'];

        // Translate the case markers to token pointers.
        foreach ($expected as $name => $marker) {
            $expected[$name] = $this->getTargetToken($marker, [\T_ENUM_CASE]);
        }

        $stackPtr = $this->getTargetToken($testMarker, Tokens::$ooScopeTokens);

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = ObjectDeclarations::getDeclaredEnumCases(self::$phpcsFile, $stackPtr);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, $stackPtr);
        $resultSecondRun = ObjectDeclarations::getDeclaredEnumCases(self::$phpcsFile, $stackPtr);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }
}
