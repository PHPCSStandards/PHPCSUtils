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

use PHPCSUtils\Internal\Cache;
use PHPCSUtils\Tests\BackCompat\BCFile\GetMemberPropertiesTest as BCFile_GetMemberPropertiesTest;
use PHPCSUtils\Utils\Variables;

/**
 * Tests for the \PHPCSUtils\Utils\Variables::getMemberProperties method.
 *
 * @covers \PHPCSUtils\Utils\Variables::getMemberProperties
 *
 * @group variables
 *
 * @since 1.0.0
 */
final class GetMemberPropertiesTest extends BCFile_GetMemberPropertiesTest
{

    /**
     * The fully qualified name of the class being tested.
     *
     * This allows for the same unit tests to be run for both the BCFile functions
     * as well as for the related PHPCSUtils functions.
     *
     * @var string
     */
    const TEST_CLASS = '\PHPCSUtils\Utils\Variables';

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
        self::$caseFile = \dirname(\dirname(__DIR__)) . '/BackCompat/BCFile/GetMemberPropertiesTest.inc';
        parent::setUpTestFile();
    }

    /**
     * Test receiving an expected exception when a non property is passed.
     *
     * @dataProvider dataNotClassProperty
     *
     * @param string $identifier Comment which precedes the test case.
     *
     * @return void
     */
    public function testNotClassPropertyException($identifier)
    {
        $this->expectException('PHPCSUtils\Exceptions\ValueError');
        $this->expectExceptionMessage('The value of argument #2 ($stackPtr) must be the pointer to a class member var');

        $variable = $this->getTargetToken($identifier, \T_VARIABLE);
        Variables::getMemberProperties(self::$phpcsFile, $variable);
    }

    /**
     * Test receiving an expected exception when a non variable is passed.
     *
     * @return void
     */
    public function testNotAVariableException()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type T_VARIABLE;');

        $next = $this->getTargetToken('/* testNotAVariable */', \T_RETURN);
        Variables::getMemberProperties(self::$phpcsFile, $next);
    }

    /**
     * Verify that the build-in caching is used when caching is enabled.
     *
     * @return void
     */
    public function testResultIsCached()
    {
        $methodName = 'PHPCSUtils\\Utils\\Variables::getMemberProperties';
        $cases      = self::dataGetMemberProperties();
        $identifier = $cases['php8.2-pseudo-type-true-in-union']['identifier'];
        $expected   = $cases['php8.2-pseudo-type-true-in-union']['expected'];

        $variable = $this->getTargetToken($identifier, \T_VARIABLE);

        if (isset($expected['type_token']) && \is_int($expected['type_token']) === true) {
            $expected['type_token'] += $variable;
        }
        if (isset($expected['type_end_token']) && \is_int($expected['type_end_token']) === true) {
            $expected['type_end_token'] += $variable;
        }

        // Verify the caching works.
        $origStatus     = Cache::$enabled;
        Cache::$enabled = true;

        $resultFirstRun  = Variables::getMemberProperties(self::$phpcsFile, $variable);
        $isCached        = Cache::isCached(self::$phpcsFile, $methodName, $variable);
        $resultSecondRun = Variables::getMemberProperties(self::$phpcsFile, $variable);

        if ($origStatus === false) {
            Cache::clear();
        }
        Cache::$enabled = $origStatus;

        $this->assertSame($expected, $resultFirstRun, 'First result did not match expectation');
        $this->assertTrue($isCached, 'Cache::isCached() could not find the cached value');
        $this->assertSame($resultFirstRun, $resultSecondRun, 'Second result did not match first');
    }
}
