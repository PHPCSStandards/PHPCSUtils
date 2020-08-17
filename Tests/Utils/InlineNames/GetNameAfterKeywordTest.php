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
     * Tokens to consider as statement end tokens for the purposes of these tests.
     *
     * @var array
     */
    protected $endTokens = [
        \T_SEMICOLON,
        \T_OPEN_CURLY_BRACKET,
        \T_OPEN_PARENTHESIS,
    ];

    /**
     * Test receiving an expected exception when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException('$stackPtr not found in this file');
        InlineNames::getNameAfterKeyword(self::$phpcsFile, 10000, $this->endTokens);
    }

    /**
     * Test that `false` is returned in case an invalid name or dynamic name is encountered and
     * when stumbling over a parse error.
     *
     * @dataProvider dataGetNameAfterKeyword
     *
     * @param string     $commentString The comment which prefaces the target token in the test file.
     * @param int|string $targetTypes   The token type constants for the test target token.
     *
     * @return void
     */
    public function testGetNameAfterKeyword($commentString, $targetTypes)
    {
        $stackPtr = $this->getTargetToken($commentString, $targetTypes);
        $result   = InlineNames::getNameAfterKeyword(self::$phpcsFile, $stackPtr, $this->endTokens);
        $this->assertFalse($result);
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
            'hierarchy-keyword-not-first-and-only-1' => [
                '/* testHierarchyKeywordNotFirstAndOnlyParent */',
                \T_INSTANCEOF,
            ],
            'hierarchy-keyword-not-first-and-only-2' => [
                '/* testHierarchyKeywordNotFirstAndOnlyStatic */',
                \T_INSTANCEOF,
            ],
            'non-anon-class-reserved-keyword' => [
                '/* testNonAnonClassUsingReservedClassKeyword */',
                \T_NEW,
            ],
            'namespace-operator-not-first-1' => [
                '/* testNamespaceOperatorNotFirstExtends */',
                \T_EXTENDS,
            ],
            'namespace-operator-not-first-2' => [
                '/* testNamespaceOperatorNotFirstArrayAccess */',
                \T_NEW,
            ],
            'parse-error-1' => [
                '/* testParseError1 */',
                \T_INSTANCEOF,
            ],
            'parse-error-2' => [
                '/* testParseError2 */',
                \T_INSTANCEOF,
            ],
            'parse-error-3' => [
                '/* testParseError3 */',
                \T_INSTANCEOF,
            ],
            'parse-error-4' => [
                '/* testParseError4 */',
                \T_INSTANCEOF,
            ],
            'live-coding' => [
                '/* testLiveCoding */',
                \T_INSTANCEOF,
            ],
        ];
    }
}
