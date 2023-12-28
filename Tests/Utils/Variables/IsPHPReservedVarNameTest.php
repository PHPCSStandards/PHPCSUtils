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

use PHPCSUtils\Utils\Variables;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHPCSUtils\Utils\Variables::isPHPReservedVarName() method.
 *
 * @covers \PHPCSUtils\Utils\Variables::isPHPReservedVarName
 *
 * @group variables
 *
 * @since 1.0.0
 */
final class IsPHPReservedVarNameTest extends TestCase
{

    /**
     * Test valid PHP reserved variable names.
     *
     * @dataProvider dataIsPHPReservedVarName
     *
     * @param string $name The variable name to test.
     *
     * @return void
     */
    public function testIsPHPReservedVarName($name)
    {
        $this->assertTrue(Variables::isPHPReservedVarName($name));
    }

    /**
     * Data provider.
     *
     * @see testIsPHPReservedVarName() For the array format.
     *
     * @return array<string, array<string>>
     */
    public static function dataIsPHPReservedVarName()
    {
        return [
            // With dollar sign.
            '$_SERVER'              => ['$_SERVER'],
            '$_GET'                 => ['$_GET'],
            '$_POST'                => ['$_POST'],
            '$_REQUEST'             => ['$_REQUEST'],
            '$_SESSION'             => ['$_SESSION'],
            '$_ENV'                 => ['$_ENV'],
            '$_COOKIE'              => ['$_COOKIE'],
            '$_FILES'               => ['$_FILES'],
            '$GLOBALS'              => ['$GLOBALS'],
            '$http_response_header' => ['$http_response_header'],
            '$argc'                 => ['$argc'],
            '$argv'                 => ['$argv'],
            '$php_errormsg'         => ['$php_errormsg'],
            '$HTTP_SERVER_VARS'     => ['$HTTP_SERVER_VARS'],
            '$HTTP_GET_VARS'        => ['$HTTP_GET_VARS'],
            '$HTTP_POST_VARS'       => ['$HTTP_POST_VARS'],
            '$HTTP_SESSION_VARS'    => ['$HTTP_SESSION_VARS'],
            '$HTTP_ENV_VARS'        => ['$HTTP_ENV_VARS'],
            '$HTTP_COOKIE_VARS'     => ['$HTTP_COOKIE_VARS'],
            '$HTTP_POST_FILES'      => ['$HTTP_POST_FILES'],
            '$HTTP_RAW_POST_DATA'   => ['$HTTP_RAW_POST_DATA'],

            // Without dollar sign.
            '_SERVER'               => ['_SERVER'],
            '_GET'                  => ['_GET'],
            '_POST'                 => ['_POST'],
            '_REQUEST'              => ['_REQUEST'],
            '_SESSION'              => ['_SESSION'],
            '_ENV'                  => ['_ENV'],
            '_COOKIE'               => ['_COOKIE'],
            '_FILES'                => ['_FILES'],
            'GLOBALS'               => ['GLOBALS'],
            'http_response_header'  => ['http_response_header'],
            'argc'                  => ['argc'],
            'argv'                  => ['argv'],
            'php_errormsg'          => ['php_errormsg'],
            'HTTP_SERVER_VARS'      => ['HTTP_SERVER_VARS'],
            'HTTP_GET_VARS'         => ['HTTP_GET_VARS'],
            'HTTP_POST_VARS'        => ['HTTP_POST_VARS'],
            'HTTP_SESSION_VARS'     => ['HTTP_SESSION_VARS'],
            'HTTP_ENV_VARS'         => ['HTTP_ENV_VARS'],
            'HTTP_COOKIE_VARS'      => ['HTTP_COOKIE_VARS'],
            'HTTP_POST_FILES'       => ['HTTP_POST_FILES'],
            'HTTP_RAW_POST_DATA'    => ['HTTP_RAW_POST_DATA'],
        ];
    }

    /**
     * Test non-reserved variable names.
     *
     * @dataProvider dataIsPHPReservedVarNameFalse
     *
     * @param string $name The variable name to test.
     *
     * @return void
     */
    public function testIsPHPReservedVarNameFalse($name)
    {
        $this->assertFalse(Variables::isPHPReservedVarName($name));
    }

    /**
     * Data provider.
     *
     * @see testIsPHPReservedVarNameFalse() For the array format.
     *
     * @return array<string, array<string>>
     */
    public static function dataIsPHPReservedVarNameFalse()
    {
        return [
            // Different case.
            'different-case-1'               => ['$_Server'],
            'different-case-2'               => ['$_get'],
            'different-case-3'               => ['$_pOST'],
            'different-case-4'               => ['$HTTP_RESPONSE_HEADER'],
            'different-case-5'               => ['_EnV'],
            'different-case-6'               => ['PHP_errormsg'],

            // Shouldn't be possible, but all the same: double dollar.
            'double-dollar'                  => ['$$_REQUEST'],

            // No underscore.
            'missing-underscore-var'         => ['$SERVER'],
            'missing-underscore-string'      => ['SERVER'],

            // Double underscore.
            'double-underscore-var'          => ['$__SERVER'],
            'double-underscore-string'       => ['__SERVER'],

            // Globals with underscore.
            'globals-with-underscore-var'    => ['$_GLOBALS'],
            'globals-with-underscore-string' => ['_GLOBALS'],

            // Array key with quotes.
            'array-key-with-quotes-1'        => ['"argc"'],
            'array-key-with-quotes-2'        => ["'argv'"],

            // Some completely different variable name.
            'name-not-in-list'               => ['my_php_errormsg'],
        ];
    }
}
