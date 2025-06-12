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
use PHPCSUtils\Tests\PolyfilledTestCase;

/**
 * Tests for the exceptions thrown in the \PHPCSUtils\Fixers\SpacesFixer::checkAndFix() method.
 *
 * @covers \PHPCSUtils\Fixers\SpacesFixer::checkAndFix
 *
 * @since 1.0.0
 */
final class SpacesFixerExceptionsTest extends PolyfilledTestCase
{

    /**
     * Test passing a non-integer token pointer for the stackPtr token (like an unchecked result of File::findPrevious()).
     *
     * @return void
     */
    public function testNonIntegerFirstToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type integer, boolean given');

        SpacesFixer::checkAndFix(self::$phpcsFile, false, 10, 0, 'Dummy');
    }

    /**
     * Test passing a non-integer token pointer for the second token (like an unchecked result of File::findNext()).
     *
     * @return void
     */
    public function testNonIntegerSecondToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\TypeError');
        $this->expectExceptionMessage('Argument #3 ($secondPtr) must be of type integer, boolean given');

        SpacesFixer::checkAndFix(self::$phpcsFile, 10, false, 0, 'Dummy');
    }

    /**
     * Test passing a non-existent token pointer for the stackPtr token.
     *
     * @return void
     */
    public function testNonExistentFirstToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage(
            'Argument #2 ($stackPtr) must be a stack pointer which exists in the $phpcsFile object, 10000 given'
        );

        SpacesFixer::checkAndFix(self::$phpcsFile, 10000, 10, 0, 'Dummy');
    }

    /**
     * Test passing a non-existent token pointer for the second token.
     *
     * @return void
     */
    public function testNonExistentSecondToken()
    {
        $this->expectException('PHPCSUtils\Exceptions\OutOfBoundsStackPtr');
        $this->expectExceptionMessage(
            'Argument #3 ($secondPtr) must be a stack pointer which exists in the $phpcsFile object, 10000 given'
        );

        SpacesFixer::checkAndFix(self::$phpcsFile, 10, 10000, 0, 'Dummy');
    }

    /**
     * Test passing whitespace for the stackPtr token.
     *
     * @return void
     */
    public function testFirstTokenWhitespace()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage('Argument #2 ($stackPtr) must be of type any, except whitespace;');

        $stackPtr  = $this->getTargetToken('/* testPassingWhitespace1 */', \T_WHITESPACE);
        $secondPtr = $this->getTargetToken('/* testPassingWhitespace1 */', \T_ECHO);
        SpacesFixer::checkAndFix(self::$phpcsFile, $stackPtr, $secondPtr, 0, 'Dummy');
    }

    /**
     * Test passing whitespace for the second token.
     *
     * @return void
     */
    public function testSecondTokenWhitespace()
    {
        $this->expectException('PHPCSUtils\Exceptions\UnexpectedTokenType');
        $this->expectExceptionMessage('Argument #3 ($secondPtr) must be of type any, except whitespace;');

        $stackPtr  = $this->getTargetToken('/* testPassingWhitespace1 */', \T_CONSTANT_ENCAPSED_STRING);
        $secondPtr = $this->getTargetToken('/* testPassingWhitespace2 */', \T_WHITESPACE);
        SpacesFixer::checkAndFix(self::$phpcsFile, $stackPtr, $secondPtr, 0, 'Dummy');
    }

    /**
     * Test passing non-adjacent tokens.
     *
     * @return void
     */
    public function testNonAdjacentTokens()
    {
        $this->expectException('PHPCSUtils\Exceptions\LogicException');
        $this->expectExceptionMessage(
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
        $this->expectException('PHPCSUtils\Exceptions\LogicException');
        $this->expectExceptionMessage(
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
        $this->expectException('PHPCSUtils\Exceptions\ValueError');
        $this->expectExceptionMessage(
            'The value of argument #4 ($expectedSpaces) should be either "newline", 0 or a positive integer'
        );

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
        $this->expectException('PHPCSUtils\Exceptions\ValueError');
        $this->expectExceptionMessage(
            'The value of argument #4 ($expectedSpaces) should be either "newline", 0 or a positive integer'
        );

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
        $this->expectException('PHPCSUtils\Exceptions\ValueError');
        $this->expectExceptionMessage(
            'The value of argument #4 ($expectedSpaces) should be either "newline", 0 or a positive integer'
        );

        $stackPtr  = $this->getTargetToken('/* testPassingWhitespace1 */', \T_ECHO);
        $secondPtr = $this->getTargetToken('/* testPassingWhitespace1 */', \T_CONSTANT_ENCAPSED_STRING);
        SpacesFixer::checkAndFix(self::$phpcsFile, $stackPtr, $secondPtr, ' ', 'Dummy');
    }
}
