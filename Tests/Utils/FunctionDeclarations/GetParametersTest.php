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

use PHPCSUtils\Tests\BackCompat\BCFile\GetMethodParametersTest as BCFile_GetMethodParametersTest;
use PHPCSUtils\Utils\FunctionDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\FunctionDeclarations::getParameters method.
 *
 * @covers \PHPCSUtils\Utils\FunctionDeclarations::getParameters
 *
 * @group functiondeclarations
 *
 * @since 1.0.0
 */
final class GetParametersTest extends BCFile_GetMethodParametersTest
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
     * Overloaded to re-use the `$caseFile` from the BCFile test.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = \dirname(\dirname(__DIR__)) . '/BackCompat/BCFile/GetMethodParametersTest.inc';
        parent::setUpTestFile();
    }

    /**
     * Test receiving an expected exception when a non function/use token is passed.
     *
     * @dataProvider dataUnexpectedTokenException
     *
     * @param string $commentString   The comment which preceeds the test.
     * @param array  $targetTokenType The token type to search for after $commentString.
     *
     * @return void
     */
    public function testUnexpectedTokenException($commentString, $targetTokenType)
    {
        $this->expectPhpcsException('$stackPtr must be of type T_FUNCTION, T_CLOSURE or T_USE or an arrow function');

        $next = $this->getTargetToken($commentString, $targetTokenType);
        FunctionDeclarations::getParameters(self::$phpcsFile, $next);
    }

    /**
     * Test receiving an expected exception when a non-closure use token is passed.
     *
     * @dataProvider dataInvalidUse
     *
     * @param string $identifier The comment which preceeds the test.
     *
     * @return void
     */
    public function testInvalidUse($identifier)
    {
        $this->expectPhpcsException('$stackPtr was not a valid closure T_USE');

        $use = $this->getTargetToken($identifier, [\T_USE]);
        FunctionDeclarations::getParameters(self::$phpcsFile, $use);
    }

    /**
     * Test receiving an empty array when there are no parameters.
     *
     * @dataProvider dataNoParams
     *
     * @param string $commentString   The comment which preceeds the test.
     * @param array  $targetTokenType Optional. The token type to search for after $commentString.
     *                                Defaults to the function/closure/arrow tokens.
     *
     * @return void
     */
    public function testNoParams($commentString, $targetTokenType = [\T_FUNCTION, \T_CLOSURE, \T_FN])
    {
        $target = $this->getTargetToken($commentString, $targetTokenType);
        $result = FunctionDeclarations::getParameters(self::$phpcsFile, $target);

        $this->assertSame([], $result);
    }

    /**
     * Test helper.
     *
     * @param string $marker     The comment which preceeds the test.
     * @param array  $expected   The expected function output.
     * @param array  $targetType Optional. The token type to search for after $marker.
     *                           Defaults to the function/closure/arrow tokens.
     *
     * @return void
     */
    protected function getMethodParametersTestHelper($marker, $expected, $targetType = [\T_FUNCTION, \T_CLOSURE, \T_FN])
    {
        $target = $this->getTargetToken($marker, $targetType);
        $found  = FunctionDeclarations::getParameters(self::$phpcsFile, $target);

        foreach ($expected as $key => $param) {
            $expected[$key]['token'] += $target;

            if ($param['reference_token'] !== false) {
                $expected[$key]['reference_token'] += $target;
            }
            if ($param['variadic_token'] !== false) {
                $expected[$key]['variadic_token'] += $target;
            }
            if ($param['type_hint_token'] !== false) {
                $expected[$key]['type_hint_token'] += $target;
            }
            if ($param['type_hint_end_token'] !== false) {
                $expected[$key]['type_hint_end_token'] += $target;
            }
            if ($param['comma_token'] !== false) {
                $expected[$key]['comma_token'] += $target;
            }
            if (isset($param['default_token'])) {
                $expected[$key]['default_token'] += $target;
            }
            if (isset($param['default_equal_token'])) {
                $expected[$key]['default_equal_token'] += $target;
            }
            if (isset($param['visibility_token']) && $param['visibility_token'] !== false) {
                $expected[$key]['visibility_token'] += $target;
            }
            if (isset($param['readonly_token'])) {
                $expected[$key]['readonly_token'] += $target;
            }
        }

        $this->assertSame($expected, $found);
    }
}
