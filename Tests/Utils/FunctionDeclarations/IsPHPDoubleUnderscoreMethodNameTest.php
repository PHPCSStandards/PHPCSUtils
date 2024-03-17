<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\FunctionDeclarations;

use PHPCSUtils\Utils\FunctionDeclarations;
use PHPUnit\Framework\TestCase;

/**
 * Tests for the \PHPCSUtils\Utils\FunctionDeclarations::isPHPDoubleUnderscoreMethodName()
 * and the \PHPCSUtils\Utils\FunctionDeclarations::isSpecialMethodName() method.
 *
 * @coversDefaultClass \PHPCSUtils\Utils\FunctionDeclarations
 *
 * @group functiondeclarations
 *
 * @since 1.0.0
 */
final class IsPHPDoubleUnderscoreMethodNameTest extends TestCase
{

    /**
     * Test valid PHP native double underscore method names.
     *
     * @dataProvider dataIsPHPDoubleUnderscoreMethodName
     * @covers       ::isPHPDoubleUnderscoreMethodName
     *
     * @param string $name The function name to test.
     *
     * @return void
     */
    public function testIsPHPDoubleUnderscoreMethodName($name)
    {
        $this->assertTrue(FunctionDeclarations::isPHPDoubleUnderscoreMethodName($name));
    }

    /**
     * Test valid PHP native double underscore method names.
     *
     * @dataProvider dataIsPHPDoubleUnderscoreMethodName
     * @covers       ::isSpecialMethodName
     *
     * @param string $name The function name to test.
     *
     * @return void
     */
    public function testIsSpecialMethodName($name)
    {
        $this->assertTrue(FunctionDeclarations::isSpecialMethodName($name));
    }

    /**
     * Data provider.
     *
     * @see testIsPHPDoubleUnderscoreMethodName() For the array format.
     * @see testIsSpecialMethodName()             For the array format.
     *
     * @return array<string, array<string>>
     */
    public static function dataIsPHPDoubleUnderscoreMethodName()
    {
        return [
            // Normal case.
            'doRequest-defined-case'              => ['__doRequest'],
            'getCookies-defined-case'             => ['__getCookies'],
            'getFunctions-defined-case'           => ['__getFunctions'],
            'getLastRequest-defined-case'         => ['__getLastRequest'],
            'getLastRequestHeaders-defined-case'  => ['__getLastRequestHeaders'],
            'getLastResponse-defined-case'        => ['__getLastResponse'],
            'getLastResponseHeaders-defined-case' => ['__getLastResponseHeaders'],
            'getTypes-defined-case'               => ['__getTypes'],
            'setCookie-defined-case'              => ['__setCookie'],
            'setLocation-defined-case'            => ['__setLocation'],
            'setSoapHeaders-defined-case'         => ['__setSoapHeaders'],
            'soapCall-defined-case'               => ['__soapCall'],

            // Uppercase et al.
            'doRequest-changed-case'              => ['__DOREQUEST'],
            'getCookies-changed-case'             => ['__getcookies'],
            'getFunctions-changed-case'           => ['__Getfunctions'],
            'getLastRequest-changed-case'         => ['__GETLASTREQUEST'],
            'getLastRequestHeaders-changed-case'  => ['__getlastrequestheaders'],
            'getLastResponse-changed-case'        => ['__GetlastResponse'],
            'getLastResponseHeaders-changed-case' => ['__GETLASTRESPONSEHEADERS'],
            'getTypes-changed-case'               => ['__GetTypes'],
            'setCookie-changed-case'              => ['__SETCookie'],
            'setLocation-changed-case'            => ['__sETlOCATION'],
            'setSoapHeaders-changed-case'         => ['__SetSOAPHeaders'],
            'soapCall-changed-case'               => ['__SOAPCall'],
        ];
    }

    /**
     * Test function names which are not valid PHP native double underscore methods.
     *
     * @dataProvider dataIsNotPHPDoubleUnderscoreMethodName
     * @covers       ::isPHPDoubleUnderscoreMethodName
     *
     * @param string $name The function name to test.
     *
     * @return void
     */
    public function testIsNotPHPDoubleUnderscoreMethodName($name)
    {
        $this->assertFalse(FunctionDeclarations::isPHPDoubleUnderscoreMethodName($name));
    }

    /**
     * Test function names which are not valid PHP native double underscore methods.
     *
     * @dataProvider dataIsNotPHPDoubleUnderscoreMethodName
     * @covers       ::isSpecialMethodName
     *
     * @param string $name The function name to test.
     *
     * @return void
     */
    public function testIsNotSpecialMethodName($name)
    {
        $this->assertFalse(FunctionDeclarations::isSpecialMethodName($name));
    }

    /**
     * Data provider.
     *
     * @see testIsNotPHPDoubleUnderscoreMethodName() For the array format.
     * @see testIsNotSpecialMethodName()             For the array format.
     *
     * @return array<string, array<string>>
     */
    public static function dataIsNotPHPDoubleUnderscoreMethodName()
    {
        return [
            'no_underscore'           => ['getLastResponseHeaders'],
            'single_underscore'       => ['_setLocation'],
            'triple_underscore'       => ['___getCookies'],
            'not_magic_function_name' => ['__getFirstRequestHeader'],
        ];
    }
}
