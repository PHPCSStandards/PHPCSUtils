<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\TestUtils\UtilityMethodTestCase;

use PHP_CodeSniffer\Exceptions\RuntimeException;
use PHP_CodeSniffer\Exceptions\TokenizerException;
use PHPCSUtils\Tests\PolyfilledTestCase;

/**
 * Tests for the \PHPCSUtils\TestUtils\UtilityMethodTestCase class.
 *
 * @covers \PHPCSUtils\TestUtils\UtilityMethodTestCase
 *
 * @group testutils
 *
 * @since 1.0.0
 */
class UtilityMethodTestCaseTest extends PolyfilledTestCase
{

    /**
     * Overload the "normal" set up to avoid the file being tokenized twice which would make
     * the test slower than necessary.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        // Deliberately left empty.
    }

    /**
     * Test that the setUpTestFile() method works correctly.
     *
     * @return void
     */
    public function testSetUp()
    {
        parent::setUpTestFile();
        $this->assertInstanceOf('PHP_CodeSniffer\Files\File', self::$phpcsFile);
        $this->assertSame(57, self::$phpcsFile->numTokens);

        $tokens = self::$phpcsFile->getTokens();
        $this->assertIsArray($tokens);
    }

    /**
     * Test the getTargetToken() method.
     *
     * @dataProvider dataGetTargetToken
     *
     * @param int|false        $expected      Expected function output.
     * @param string           $commentString The delimiter comment to look for.
     * @param int|string|array $tokenType     The type of token(s) to look for.
     * @param string           $tokenContent  Optional. The token content for the target token.
     *
     * @return void
     */
    public function testGetTargetToken($expected, $commentString, $tokenType, $tokenContent = null)
    {
        if (isset($tokenContent)) {
            $result = $this->getTargetToken($commentString, $tokenType, $tokenContent);
        } else {
            $result = $this->getTargetToken($commentString, $tokenType);
        }

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetTargetToken() For the array format.
     *
     * @return array
     */
    public function dataGetTargetToken()
    {
        return [
            'single-token-type' => [
                6,
                '/* testFindingTarget */',
                \T_VARIABLE,
            ],
            'multi-token-type-1' => [
                6,
                '/* testFindingTarget */',
                [\T_VARIABLE, \T_FALSE],
            ],
            'multi-token-type-2' => [
                11,
                '/* testFindingTarget */',
                [\T_FALSE, \T_LNUMBER],
            ],
            'content-method' => [
                23,
                '/* testFindingTargetWithContent */',
                \T_STRING,
                'method',
            ],
            'content-otherMethod' => [
                33,
                '/* testFindingTargetWithContent */',
                \T_STRING,
                'otherMethod',
            ],
            'content-$a' => [
                21,
                '/* testFindingTargetWithContent */',
                \T_VARIABLE,
                '$a',
            ],
            'content-$b' => [
                31,
                '/* testFindingTargetWithContent */',
                \T_VARIABLE,
                '$b',
            ],
            'content-foo' => [
                26,
                '/* testFindingTargetWithContent */',
                [\T_CONSTANT_ENCAPSED_STRING, \T_DOUBLE_QUOTED_STRING],
                "'foo'",
            ],
            'content-bar' => [
                36,
                '/* testFindingTargetWithContent */',
                [\T_CONSTANT_ENCAPSED_STRING, \T_DOUBLE_QUOTED_STRING],
                "'bar'",
            ],
        ];
    }

    /**
     * Test the behaviour of the getTargetToken() method when the test marker comment is not found.
     *
     * @return void
     */
    public function testGetTargetTokenCommentNotFound()
    {
        $msg       = 'Failed to find the test marker: ';
        $exception = 'PHPUnit\Framework\AssertionFailedError';
        if (\class_exists('PHPUnit_Framework_AssertionFailedError')) {
            // PHPUnit < 6.
            $exception = 'PHPUnit_Framework_AssertionFailedError';
        }

        $this->expectException($exception);
        $this->expectExceptionMessage($msg);

        $this->getTargetToken('/* testCommentDoesNotExist */', [\T_VARIABLE], '$a');
    }

    /**
     * Test the behaviour of the getTargetToken() method when the target is not found.
     *
     * @return void
     */
    public function testGetTargetTokenNotFound()
    {
        $msg       = 'Failed to find test target token for comment string: ';
        $exception = 'PHPUnit\Framework\AssertionFailedError';
        if (\class_exists('PHPUnit_Framework_AssertionFailedError')) {
            // PHPUnit < 6.
            $exception = 'PHPUnit_Framework_AssertionFailedError';
        }

        $this->expectException($exception);
        $this->expectExceptionMessage($msg);

        $this->getTargetToken('/* testNotFindingTarget */', [\T_VARIABLE], '$a');
    }

    /**
     * Test the behaviour of the getTargetToken() method when the target is not found.
     *
     * @return void
     */
    public function testGetTargetTokenNotFoundException()
    {
        $msg       = 'Failed to find test target token for comment string: ';
        $exception = '\RuntimeException';

        $this->expectException($exception);
        $this->expectExceptionMessage($msg);

        $this->getTargetToken('/* testNotFindingTarget */', [\T_VARIABLE], '$a', false);
    }

    /**
     * Test that the helper method to handle cross-version testing of exceptions in PHPUnit
     * works correctly.
     *
     * @return void
     */
    public function testExpectPhpcsRuntimeException()
    {
        $this->expectPhpcsException('testing-1-2-3');
        throw new RuntimeException('testing-1-2-3');
    }

    /**
     * Test that the helper method to handle cross-version testing of exceptions in PHPUnit
     * works correctly.
     *
     * @return void
     */
    public function testExpectPhpcsTokenizerException()
    {
        $this->expectPhpcsException('testing-1-2-3', 'tokenizer');
        throw new TokenizerException('testing-1-2-3');
    }

    /**
     * Test that the class is correct reset.
     *
     * @return void
     */
    public function testTearDown()
    {
        parent::resetTestFile();
        $this->assertNull(self::$phpcsFile);
    }
}
