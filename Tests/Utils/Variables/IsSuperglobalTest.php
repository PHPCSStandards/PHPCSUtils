<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Variables;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Variables;

/**
 * Tests for the \PHPCSUtils\Utils\Variables::IsSuperglobal() and
 * \PHPCSUtils\Utils\Variables::IsSuperglobalName() methods.
 *
 * @covers \PHPCSUtils\Utils\Variables::isSuperglobal
 * @covers \PHPCSUtils\Utils\Variables::isSuperglobalName
 *
 * @group variables
 *
 * @since 1.0.0
 */
final class IsSuperglobalTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $result = Variables::isSuperglobal(self::$phpcsFile, 10000);
        $this->assertFalse($result);
    }

    /**
     * Test correctly detecting superglobal variables.
     *
     * @dataProvider dataIsSuperglobal
     *
     * @param string     $testMarker  The comment which prefaces the target token in the test file.
     * @param bool       $expected    The expected function return value.
     * @param int|string $targetType  Optional. The token type for the target token in the test file.
     * @param string     $targetValue Optional. The token content for the target token in the test file.
     *
     * @return void
     */
    public function testIsSuperglobal($testMarker, $expected, $targetType = \T_VARIABLE, $targetValue = null)
    {
        $stackPtr = $this->getTargetToken($testMarker, $targetType, $targetValue);
        $result   = Variables::isSuperglobal(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testIsSuperglobal() For the array format.
     *
     * @return array<string, array<string, int|string|bool>>
     */
    public static function dataIsSuperglobal()
    {
        return [
            'not-a-variable' => [
                'testMarker' => '/* testNotAVariable */',
                'expected'   => false,
                'targetType' => \T_RETURN,
            ],
            'not-a-reserved-var' => [
                'testMarker' => '/* testNotAReservedVar */',
                'expected'   => false,
            ],
            'reserved-var-not-superglobal' => [
                'testMarker' => '/* testReservedVarNotSuperglobal */',
                'expected'   => false,
            ],
            'reserved-var-superglobal' => [
                'testMarker' => '/* testReservedVarIsSuperglobal */',
                'expected'   => true,
            ],
            'array-key-not-a-reserved-var' => [
                'testMarker' => '/* testGLOBALSArrayKeyNotAReservedVar */',
                'expected'   => false,
                'targetType' => \T_CONSTANT_ENCAPSED_STRING,
            ],
            'array-key-variable' => [
                'testMarker'  => '/* testGLOBALSArrayKeyVar */',
                'expected'    => false,
                'targetType'  => \T_VARIABLE,
                'targetValue' => '$something',
            ],
            'array-key-reserved-var-not-superglobal' => [
                'testMarker'  => '/* testGLOBALSArrayKeyReservedVar */',
                'expected'    => false,
                'targetType'  => \T_VARIABLE,
                'targetValue' => '$php_errormsg',
            ],
            'array-key-var-superglobal' => [
                'testMarker'  => '/* testGLOBALSArrayKeySuperglobal */',
                'expected'    => true,
                'targetType'  => \T_VARIABLE,
                'targetValue' => '$_COOKIE',
            ],
            'array-key-not-single-string' => [
                'testMarker' => '/* testGLOBALSArrayKeyNotSingleString */',
                'expected'   => false,
                'targetType' => \T_CONSTANT_ENCAPSED_STRING,
            ],
            'array-key-interpolated-var' => [
                'testMarker' => '/* testGLOBALSArrayKeyInterpolatedVar */',
                'expected'   => false,
                'targetType' => \T_DOUBLE_QUOTED_STRING,
            ],
            'array-key-string-superglobal' => [
                'testMarker' => '/* testGLOBALSArrayKeySingleStringSuperglobal */',
                'expected'   => true,
                'targetType' => \T_CONSTANT_ENCAPSED_STRING,
            ],
            'array-key-var-superglobal-with-array-access' => [
                'testMarker'  => '/* testGLOBALSArrayKeySuperglobalWithKey */',
                'expected'    => true,
                'targetType'  => \T_VARIABLE,
                'targetValue' => '$_GET',
            ],
            'array-key-not-globals-array' => [
                'testMarker' => '/* testSuperglobalKeyNotGLOBALSArray */',
                'expected'   => false,
                'targetType' => \T_CONSTANT_ENCAPSED_STRING,
            ],
        ];
    }

    /**
     * Test valid PHP superglobal names.
     *
     * @dataProvider dataIsSuperglobalName
     *
     * @param string $name The variable name to test.
     *
     * @return void
     */
    public function testIsSuperglobalName($name)
    {
        $this->assertTrue(Variables::isSuperglobalName($name));
    }

    /**
     * Data provider.
     *
     * @see testIsSuperglobalName() For the array format.
     *
     * @return array<string, array<string>>
     */
    public static function dataIsSuperglobalName()
    {
        return [
            '$_SERVER'  => ['$_SERVER'],
            '$_GET'     => ['$_GET'],
            '$_POST'    => ['$_POST'],
            '$_REQUEST' => ['$_REQUEST'],
            '_SESSION'  => ['_SESSION'],
            '_ENV'      => ['_ENV'],
            '_COOKIE'   => ['_COOKIE'],
            '_FILES'    => ['_FILES'],
            'GLOBALS'   => ['GLOBALS'],
        ];
    }

    /**
     * Test non-superglobal variable names.
     *
     * @dataProvider dataIsSuperglobalNameFalse
     *
     * @param string $name The variable name to test.
     *
     * @return void
     */
    public function testIsSuperglobalNameFalse($name)
    {
        $this->assertFalse(Variables::isSuperglobalName($name));
    }

    /**
     * Data provider.
     *
     * @see testIsSuperglobalNameFalse() For the array format.
     *
     * @return array<string, array<string>>
     */
    public static function dataIsSuperglobalNameFalse()
    {
        return [
            'non-reserved-var'                    => ['$not_a_superglobal'],
            'php-reserved-var-not-superglobal-1'  => ['$http_response_header'],
            'php-reserved-var-not-superglobal-2'  => ['$argc'],
            'php-reserved-var-not-superglobal-3'  => ['$argv'],
            'php-reserved-var-not-superglobal-4'  => ['$HTTP_RAW_POST_DATA'],
            'php-reserved-var-not-superglobal-5'  => ['$php_errormsg'],
            'php-reserved-var-not-superglobal-6'  => ['HTTP_SERVER_VARS'],
            'php-reserved-var-not-superglobal-7'  => ['HTTP_GET_VARS'],
            'php-reserved-var-not-superglobal-8'  => ['HTTP_POST_VARS'],
            'php-reserved-var-not-superglobal-9'  => ['HTTP_SESSION_VARS'],
            'php-reserved-var-not-superglobal-10' => ['HTTP_ENV_VARS'],
            'php-reserved-var-not-superglobal-11' => ['HTTP_COOKIE_VARS'],
            'php-reserved-var-not-superglobal-12' => ['HTTP_POST_FILES'],
        ];
    }
}
