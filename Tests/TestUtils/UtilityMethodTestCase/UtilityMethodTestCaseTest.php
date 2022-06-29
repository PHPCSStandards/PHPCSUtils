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
                'expected'      => 6,
                'commentString' => '/* testFindingTarget */',
                'tokenType'     => \T_VARIABLE,
            ],
            'multi-token-type-1' => [
                'expected'      => 6,
                'commentString' => '/* testFindingTarget */',
                'tokenType'     => [\T_VARIABLE, \T_FALSE],
            ],
            'multi-token-type-2' => [
                'expected'      => 11,
                'commentString' => '/* testFindingTarget */',
                'tokenType'     => [\T_FALSE, \T_LNUMBER],
            ],
            'content-method' => [
                'expected'      => 23,
                'commentString' => '/* testFindingTargetWithContent */',
                'tokenType'     => \T_STRING,
                'tokenContent'  => 'method',
            ],
            'content-otherMethod' => [
                'expected'      => 33,
                'commentString' => '/* testFindingTargetWithContent */',
                'tokenType'     => \T_STRING,
                'tokenContent'  => 'otherMethod',
            ],
            'content-$a' => [
                'expected'      => 21,
                'commentString' => '/* testFindingTargetWithContent */',
                'tokenType'     => \T_VARIABLE,
                'tokenContent'  => '$a',
            ],
            'content-$b' => [
                'expected'      => 31,
                'commentString' => '/* testFindingTargetWithContent */',
                'tokenType'     => \T_VARIABLE,
                'tokenContent'  => '$b',
            ],
            'content-foo' => [
                'expected'      => 26,
                'commentString' => '/* testFindingTargetWithContent */',
                'tokenType'     => [\T_CONSTANT_ENCAPSED_STRING, \T_DOUBLE_QUOTED_STRING],
                'tokenContent'  => "'foo'",
            ],
            'content-bar' => [
                'expected'      => 36,
                'commentString' => '/* testFindingTargetWithContent */',
                'tokenType'     => [\T_CONSTANT_ENCAPSED_STRING, \T_DOUBLE_QUOTED_STRING],
                'tokenContent'  => "'bar'",
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
     * Test that the class is correct reset.
     *
     * @return void
     */
    public function testTearDown()
    {
        parent::resetTestFile();
        $this->assertSame('0', self::$phpcsVersion, 'phpcsVersion was not reset');
        $this->assertSame('inc', self::$fileExtension, 'fileExtension was not reset');
        $this->assertSame('', self::$caseFile, 'caseFile was not reset');
        $this->assertSame(4, self::$tabWidth, 'tabWidth was not reset');
        $this->assertNull(self::$phpcsFile, 'phpcsFile was not reset');
        $this->assertSame(['Dummy.Dummy.Dummy'], self::$selectedSniff, 'selectedSniff was not reset');
    }
}
