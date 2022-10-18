<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Fixers\SpacesFixer;

use PHPCSUtils\Fixers\SpacesFixer;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the exceptions thrown in the \PHPCSUtils\Fixers\SpacesFixer::checkAndFix() method.
 *
 * @covers \PHPCSUtils\Fixers\SpacesFixer::checkAndFix
 *
 * @group fixers
 *
 * @since 1.0.0
 */
final class SpacesFixerExceptionsTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer for the stackPtr token.
     *
     * @return void
     */
    public function testNonExistentFirstToken()
    {
        $this->expectPhpcsException('The $stackPtr and the $secondPtr token must exist and not be whitespace');

        SpacesFixer::checkAndFix(self::$phpcsFile, 10000, 10, 0, 'Dummy');
    }

    /**
     * Test passing a non-existent token pointer for the second token.
     *
     * @return void
     */
    public function testNonExistentSecondToken()
    {
        $this->expectPhpcsException('The $stackPtr and the $secondPtr token must exist and not be whitespace');

        SpacesFixer::checkAndFix(self::$phpcsFile, 10, 10000, 0, 'Dummy');
    }

    /**
     * Test passing whitespace for the stackPtr token.
     *
     * @return void
     */
    public function testFirstTokenWhitespace()
    {
        $this->expectPhpcsException('The $stackPtr and the $secondPtr token must exist and not be whitespace');

        $stackPtr = $this->getTargetToken('/* testPassingWhitespace1 */', \T_WHITESPACE);
        SpacesFixer::checkAndFix(self::$phpcsFile, $stackPtr, 10, 0, 'Dummy');
    }

    /**
     * Test passing whitespace for the second token.
     *
     * @return void
     */
    public function testSecondTokenWhitespace()
    {
        $this->expectPhpcsException('The $stackPtr and the $secondPtr token must exist and not be whitespace');

        $secondPtr = $this->getTargetToken('/* testPassingWhitespace2 */', \T_WHITESPACE);
        SpacesFixer::checkAndFix(self::$phpcsFile, 10, $secondPtr, 0, 'Dummy');
    }

    /**
     * Test passing non-adjacent tokens.
     *
     * @return void
     */
    public function testNonAdjacentTokens()
    {
        $this->expectPhpcsException(
            'The $stackPtr and the $secondPtr token must be adjacent tokens separated only'
                . ' by whitespace and/or comments'
        );

        $stackPtr  = $this->getTargetToken('/* testPassingTokensWithSomethingBetween */', \T_ECHO);
        $secondPtr = $this->getTargetToken('/* testPassingTokensWithSomethingBetween */', \T_STRING_CONCAT);
        SpacesFixer::checkAndFix(self::$phpcsFile, $stackPtr, $secondPtr, 0, 'Dummy');
    }

    /**
     * Test passing non-adjacent tokens in reverse order.
     *
     * @return void
     */
    public function testNonAdjacentTokensReverseOrder()
    {
        $this->expectPhpcsException(
            'The $stackPtr and the $secondPtr token must be adjacent tokens separated only'
                . ' by whitespace and/or comments'
        );

        $stackPtr  = $this->getTargetToken('/* testPassingTokensWithSomethingBetween */', \T_ECHO);
        $secondPtr = $this->getTargetToken('/* testPassingTokensWithSomethingBetween */', \T_STRING_CONCAT);
        SpacesFixer::checkAndFix(self::$phpcsFile, $secondPtr, $stackPtr, 0, 'Dummy');
    }

    /**
     * Test passing an negative integer value for spaces.
     *
     * @return void
     */
    public function testInvalidExpectedSpacesNegativeValue()
    {
        $this->expectPhpcsException('The $expectedSpaces setting should be either "newline", 0 or a positive integer');

        $stackPtr  = $this->getTargetToken('/* testPassingWhitespace1 */', \T_ECHO);
        $secondPtr = $this->getTargetToken('/* testPassingWhitespace1 */', \T_CONSTANT_ENCAPSED_STRING);
        SpacesFixer::checkAndFix(self::$phpcsFile, $stackPtr, $secondPtr, -10, 'Dummy');
    }

    /**
     * Test passing an value of a type which is not accepted for spaces.
     *
     * @return void
     */
    public function testInvalidExpectedSpacesUnexpectedType()
    {
        $this->expectPhpcsException('The $expectedSpaces setting should be either "newline", 0 or a positive integer');

        $stackPtr  = $this->getTargetToken('/* testPassingWhitespace1 */', \T_ECHO);
        $secondPtr = $this->getTargetToken('/* testPassingWhitespace1 */', \T_CONSTANT_ENCAPSED_STRING);
        SpacesFixer::checkAndFix(self::$phpcsFile, $stackPtr, $secondPtr, false, 'Dummy');
    }

    /**
     * Test passing a non-decimal string value for spaces.
     *
     * @return void
     */
    public function testInvalidExpectedSpacesNonDecimalString()
    {
        $this->expectPhpcsException('The $expectedSpaces setting should be either "newline", 0 or a positive integer');

        $stackPtr  = $this->getTargetToken('/* testPassingWhitespace1 */', \T_ECHO);
        $secondPtr = $this->getTargetToken('/* testPassingWhitespace1 */', \T_CONSTANT_ENCAPSED_STRING);
        SpacesFixer::checkAndFix(self::$phpcsFile, $stackPtr, $secondPtr, ' ', 'Dummy');
    }
}
