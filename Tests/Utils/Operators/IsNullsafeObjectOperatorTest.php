<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\Operators;

use PHP_CodeSniffer\Util\Tokens;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\Operators;

/**
 * Tests for the \PHPCSUtils\Utils\Operators::isNullsafeObjectOperator() method.
 *
 * @covers \PHPCSUtils\Utils\Operators::isNullsafeObjectOperator
 *
 * @group operators
 *
 * @since 1.0.0
 */
class IsNullsafeObjectOperatorTest extends UtilityMethodTestCase
{

    /**
     * Test that false is returned when a non-existent token is passed.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->assertFalse(Operators::isNullsafeObjectOperator(self::$phpcsFile, 10000));
    }

    /**
     * Test that false is returned when an unsupported token is passed.
     *
     * @return void
     */
    public function testUnsupportedToken()
    {
        $target = $this->getTargetToken('/* testUnsupportedToken */', \T_DOUBLE_COLON);
        $this->assertFalse(Operators::isNullsafeObjectOperator(self::$phpcsFile, $target));
    }

    /**
     * Test whether a nullsafe object operator is correctly identified as such.
     *
     * @dataProvider dataIsNullsafeObjectOperator
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @return void
     */
    public function testIsNullsafeObjectOperator($testMarker)
    {
        $targetTokenTypes = $this->getTargetTokensTypes();
        $stackPtr         = $this->getTargetToken($testMarker, $targetTokenTypes);

        $this->assertTrue(
            Operators::isNullsafeObjectOperator(self::$phpcsFile, $stackPtr),
            'Failed asserting that (first) token is the nullsafe object operator'
        );

        // Also test the second token of a non-backfilled nullsafe object operator.
        $tokens = self::$phpcsFile->getTokens();
        if ($tokens[$stackPtr]['code'] === \T_INLINE_THEN) {
            $stackPtr = $this->getTargetToken($testMarker, [\T_OBJECT_OPERATOR]);

            $this->assertTrue(
                Operators::isNullsafeObjectOperator(self::$phpcsFile, $stackPtr),
                'Failed asserting that (second) token is the nullsafe object operator'
            );
        }
    }

    /**
     * Data provider.
     *
     * @see testIsNullsafeObjectOperator()
     *
     * @return array
     */
    public function dataIsNullsafeObjectOperator()
    {
        return [
            'nullsafe'               => ['/* testNullsafeObjectOperator */'],
            'nullsafe-write-context' => ['/* testNullsafeObjectOperatorWriteContext */'],
        ];
    }

    /**
     * Test whether tokens which can be confused with a non-nullsafe object operator are
     * not misidentified as a nullsafe object operator.
     *
     * @dataProvider dataNotNullsafeObjectOperator
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param bool   $textNext   Whether to also test the next non-empty token. Defaults to false.
     *
     * @return void
     */
    public function testNotNullsafeObjectOperator($testMarker, $textNext = false)
    {
        $stackPtr = $this->getTargetToken($testMarker, $this->getTargetTokensTypes());

        $this->assertFalse(Operators::isNullsafeObjectOperator(self::$phpcsFile, $stackPtr));

        if ($textNext === true) {
            $next = self::$phpcsFile->findNext(Tokens::$emptyTokens, ($stackPtr + 1), null, true);
            $this->assertFalse(Operators::isNullsafeObjectOperator(self::$phpcsFile, $next));
        }
    }

    /**
     * Data provider.
     *
     * @see testNotNullsafeObjectOperator()
     *
     * @return array
     */
    public function dataNotNullsafeObjectOperator()
    {
        return [
            'normal-object-operator'             => ['/* testObjectOperator */'],
            'ternary-then'                       => ['/* testTernaryThen */'],
            'object-operator-in-ternary'         => ['/* testObjectOperatorInTernary */'],
            'parse-error-whitespace-not-allowed' => ['/* testParseErrorWhitespaceNotAllowed */', true],
            'parse-error-comment-not-allowed'    => ['/* testParseErrorCommentNotAllowed */', true],
            'live-coding'                        => ['/* testLiveCoding */'],
        ];
    }

    /**
     * Get the target token types to pass to the getTargetToken() method.
     *
     * @return array <int|string> => <int|string>
     */
    private function getTargetTokensTypes()
    {
        $targets = [
            \T_OBJECT_OPERATOR,
            \T_INLINE_THEN,
        ];

        if (defined('T_NULLSAFE_OBJECT_OPERATOR') === true) {
            $targets[] = \T_NULLSAFE_OBJECT_OPERATOR;
        }

        return $targets;
    }
}
