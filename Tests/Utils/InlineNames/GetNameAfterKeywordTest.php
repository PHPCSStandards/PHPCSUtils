<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\InlineNames;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\InlineNames;

/**
 * Tests for the \PHPCSUtils\Utils\InlineNames::getNameAfterKeyword() method.
 *
 * This method is mostly tested via the getNameFromNew(), getNameFromInstanceOf() and
 * related methods.
 *
 * The tests in this file cover exceptional circumstances which shouldn't ever exist in code
 * in the first place as they are mostly parse errors.
 *
 * @covers \PHPCSUtils\Utils\InlineNames::getNameAfterKeyword
 *
 * @group inlinenames
 *
 * @since 1.0.0
 */
class GetNameAfterKeywordTest extends UtilityMethodTestCase
{

    /**
     * Test receiving an expected exception when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException('$stackPtr not found in this file');
        InlineNames::getNameAfterKeyword(self::$phpcsFile, 10000);
    }

    /**
     * Test retrieving the name used in an object instantiation.
     *
     * @dataProvider dataGetNameAfterKeyword
     *
     * @param string $commentString The comment which prefaces the T_INSTANCEOF token in the test file.
     * @param string $expected      The expected function return value.
     *
     * @return void
     */
    public function testGetNameAfterKeyword($commentString, $targetTypes, $expected)
    {
        $stackPtr = $this->getTargetToken($commentString, $targetTypes);
        $result   = InlineNames::getNameAfterKeyword(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetNameAfterKeyword() For the array format.
     *
     * @return array
     */
    public function dataGetNameAfterKeyword()
    {
        return [
            'unqualified-name' => [
                '/* testUnqualifiedName */',
                'Name',
            ],

            'live-coding' => [
                '/* testLiveCoding */',
                false,
            ],
        ];
    }
}
