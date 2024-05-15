<?php
/**
 * PHPCompatibility, an external standard for PHP_CodeSniffer.
 *
 * @package   PHPCompatibility
 * @copyright 2012-2020 PHPCompatibility Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCompatibility/PHPCompatibility
 */

namespace PHPCompatibility\Util\Tests\Helpers\ResolveHelper;

use PHPCompatibility\Helpers\ResolveHelper;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the `getFQExtendedClassName()` utility function.
 *
 * @group utilityGetFQExtendedClassName
 * @group utilityFunctions
 *
 * @since 7.0.3
 */
final class GetFQExtendedClassNameUnitTest extends UtilityMethodTestCase
{

    /**
     * Test retrieving a fully qualified class name for the class being extended.
     *
     * @dataProvider dataGetFQExtendedClassName
     *
     * @covers \PHPCompatibility\Helpers\ResolveHelper::getFQExtendedClassName
     *
     * @param string $commentString The comment which prefaces the T_CLASS token in the test file.
     * @param string $expected      The expected fully qualified class name.
     *
     * @return void
     */
    public function testGetFQExtendedClassName($commentString, $expected)
    {
        $stackPtr = $this->getTargetToken($commentString, [\T_CLASS, \T_ANON_CLASS]);
        $result   = ResolveHelper::getFQExtendedClassName(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetFQExtendedClassName()
     *
     * @return array
     */
    public static function dataGetFQExtendedClassName()
    {
        return [
            ['/* test 1 */', ''],
            ['/* test 2 */', '\DateTime'],
            ['/* test 3 */', '\MyTesting\DateTime'],
            ['/* test 4 */', '\DateTime'],
            ['/* test 5 */', '\MyTesting\anotherNS\DateTime'],
            ['/* test 6 */', '\FQNS\DateTime'],
            ['/* test 7 */', '\AnotherTesting\DateTime'],
            ['/* test 8 */', '\DateTime'],
            ['/* test 9 */', '\AnotherTesting\anotherNS\DateTime'],
            ['/* test 10 */', '\FQNS\DateTime'],
            ['/* test 11 */', '\DateTime'],
            ['/* test 12 */', '\DateTime'],
            ['/* test 13 */', '\Yet\More\Testing\DateTime'],
            ['/* test 14 */', '\Yet\More\Testing\anotherNS\DateTime'],
            ['/* test 15 */', '\FQNS\DateTime'],
            ['/* test 16 */', '\SomeClass'],
            ['/* test 17 */', '\Yet\More\Testing\SomeClass'],
        ];
    }

    /**
     * Test an empty string is returned when an invalid token is passed.
     *
     * @dataProvider dataGetFQExtendedClassNameInvalidToken
     *
     * @covers \PHPCompatibility\Helpers\ResolveHelper::getFQExtendedClassName
     *
     * @param string     $commentString The comment which prefaces the T_CLASS token in the test file.
     * @param int|string $targetType    The token to pass to the method.
     *
     * @return void
     */
    public function testGetFQExtendedClassNameInvalidToken($commentString, $targetType)
    {
        $stackPtr = $this->getTargetToken($commentString, $targetType);
        $result   = ResolveHelper::getFQExtendedClassName(self::$phpcsFile, $stackPtr);
        $this->assertSame('', $result);
    }

    /**
     * Data provider.
     *
     * @see testGetFQExtendedClassNameInvalidToken()
     *
     * @return array
     */
    public static function dataGetFQExtendedClassNameInvalidToken()
    {
        return [
            ['/* test 2 */', \T_EXTENDS],
            ['/* test 18 */', \T_INTERFACE],
        ];
    }
}
