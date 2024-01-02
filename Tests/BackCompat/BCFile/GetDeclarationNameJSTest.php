<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\BackCompat\BCFile;

use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::getDeclarationName() method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::getDeclarationName
 *
 * @group objectdeclarations
 *
 * @since 1.0.0
 */
class GetDeclarationNameJSTest extends UtilityMethodTestCase
{

    /**
     * The file extension of the test case file (without leading dot).
     *
     * @var string
     */
    protected static $fileExtension = 'js';

    /**
     * Test receiving an expected exception when a non-supported token is passed.
     *
     * @return void
     */
    public function testInvalidTokenPassed()
    {
        $this->expectPhpcsException('Token type "T_STRING" is not T_FUNCTION, T_CLASS, T_INTERFACE, T_TRAIT or T_ENUM');

        $target = $this->getTargetToken('/* testInvalidTokenPassed */', \T_STRING);
        BCFile::getDeclarationName(self::$phpcsFile, $target);
    }

    /**
     * Test receiving "null" when passed an anonymous construct or in case of a parse error.
     *
     * @dataProvider dataGetDeclarationNameNull
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType Token type of the token to get as stackPtr.
     *
     * @return void
     */
    public function testGetDeclarationNameNull($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $result = BCFile::getDeclarationName(self::$phpcsFile, $target);
        $this->assertNull($result);
    }

    /**
     * Data provider.
     *
     * @see GetDeclarationNameTest::testGetDeclarationNameNull()
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataGetDeclarationNameNull()
    {
        return [
            'closure' => [
                'testMarker' => '/* testClosure */',
                'targetType' => \T_CLOSURE,
            ],
        ];
    }

    /**
     * Test retrieving the name of a function or OO structure.
     *
     * @dataProvider dataGetDeclarationName
     *
     * @param string                 $testMarker The comment which prefaces the target token in the test file.
     * @param string                 $expected   Expected function output.
     * @param array<int|string>|null $targetType Token type of the token to get as stackPtr.
     *
     * @return void
     */
    public function testGetDeclarationName($testMarker, $expected, $targetType = null)
    {
        if (isset($targetType) === false) {
            $targetType = [\T_CLASS, \T_INTERFACE, \T_TRAIT, \T_ENUM, \T_FUNCTION];
        }

        $target = $this->getTargetToken($testMarker, $targetType);
        $result = BCFile::getDeclarationName(self::$phpcsFile, $target);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see GetDeclarationNameTest::testGetDeclarationName()
     *
     * @return array<string, array<string, string|array<int|string>>>
     */
    public static function dataGetDeclarationName()
    {
        return [
            'function' => [
                'testMarker' => '/* testFunction */',
                'expected'   => 'functionName',
            ],
            'class' => [
                'testMarker' => '/* testClass */',
                'expected'   => 'ClassName',
                'targetType' => [\T_CLASS, \T_STRING],
            ],
            'function-unicode-name' => [
                'testMarker' => '/* testFunctionUnicode */',
                'expected'   => 'π',
            ],
        ];
    }

    /**
     * Test retrieving the name of JS ES6 class method.
     *
     * @return void
     */
    public function testGetDeclarationNameES6Method()
    {
        $target = $this->getTargetToken('/* testMethod */', [\T_CLASS, \T_INTERFACE, \T_TRAIT, \T_FUNCTION]);
        $result = BCFile::getDeclarationName(self::$phpcsFile, $target);
        $this->assertSame('methodName', $result);
    }
}
