<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\ObjectDeclarations;

use PHPCSUtils\Tests\BackCompat\BCFile\GetDeclarationNameTest as BCFile_GetDeclarationNameTest;
use PHPCSUtils\Utils\ObjectDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\ObjectDeclarations::getName() method.
 *
 * @covers \PHPCSUtils\Utils\ObjectDeclarations::getName
 *
 * @group objectdeclarations
 *
 * @since 1.0.0
 */
final class GetNameTest extends BCFile_GetDeclarationNameTest
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
        self::$caseFile = \dirname(\dirname(__DIR__)) . '/BackCompat/BCFile/GetDeclarationNameTest.inc';
        parent::setUpTestFile();
    }

    /**
     * Test receiving an expected exception when a non-supported token is passed.
     *
     * @return void
     */
    public function testTrulyInvalidTokenPassed()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be of type T_FUNCTION, T_CLASS, T_INTERFACE, T_TRAIT or T_ENUM'
        );

        $target = $this->getTargetToken('/* testInvalidTokenPassed */', \T_STRING);
        ObjectDeclarations::getName(self::$phpcsFile, $target);
    }

    /**
     * Test receiving "null" when passed an anonymous construct or in case of a parse error.
     *
     * Note: the upstream and the BCFile method no longer returns `null`, but throws an exception.
     * For PHPCSUtils, this change needs to wait for the next major.
     *
     * {@internal Method name not adjusted as otherwise it wouldn't overload the parent method.}
     *
     * @dataProvider dataInvalidTokenPassed
     *
     * @param string     $testMarker The comment which prefaces the target token in the test file.
     * @param int|string $targetType Token type of the token to get as stackPtr.
     *
     * @return void
     */
    public function testInvalidTokenPassed($testMarker, $targetType)
    {
        $target = $this->getTargetToken($testMarker, $targetType);
        $result = ObjectDeclarations::getName(self::$phpcsFile, $target);
        $this->assertNull($result);
    }

    /**
     * Data provider.
     *
     * @see testInvalidTokenPassed() For the array format.
     *
     * @return array<string, array<string, int|string>>
     */
    public static function dataInvalidTokenPassed()
    {
        $data = parent::dataInvalidTokenPassed();
        unset($data['unsupported token T_STRING']);

        return $data;
    }

    /**
     * Test retrieving the name of a function or OO structure.
     *
     * {@internal Method name not adjusted as otherwise it wouldn't overload the parent method.}
     *
     * @dataProvider dataGetDeclarationName
     *
     * @param string          $testMarker The comment which prefaces the target token in the test file.
     * @param string          $expected   Expected function output.
     * @param int|string|null $targetType Token type of the token to get as stackPtr.
     *
     * @return void
     */
    public function testGetDeclarationName($testMarker, $expected, $targetType = null)
    {
        if (isset($targetType) === false) {
            $targetType = [\T_CLASS, \T_INTERFACE, \T_TRAIT, \T_ENUM, \T_FUNCTION];
        }

        $target = $this->getTargetToken($testMarker, $targetType);
        $result = ObjectDeclarations::getName(self::$phpcsFile, $target);
        $this->assertSame($expected, $result);
    }
}
