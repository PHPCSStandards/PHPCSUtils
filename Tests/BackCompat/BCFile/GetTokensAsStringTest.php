<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\BackCompat\BCFile;

use PHPCSUtils\BackCompat\BCFile;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;

/**
 * Tests for the \PHPCSUtils\BackCompat\BCFile::getTokensAsString() method.
 *
 * @covers \PHPCSUtils\BackCompat\BCFile::getTokensAsString
 *
 * @group gettokensasstring
 *
 * @since 1.0.0
 */
final class GetTokensAsStringTest extends UtilityMethodTestCase
{

    /**
     * Test passing a non-existent token pointer.
     *
     * @return void
     */
    public function testNonExistentToken()
    {
        $this->expectPhpcsException('The $start position for getTokensAsString() must exist in the token stack');

        BCFile::getTokensAsString(self::$phpcsFile, 100000, 10);
    }

    /**
     * Test passing a non integer `$start`, like the result of a failed $phpcsFile->findNext().
     *
     * @return void
     */
    public function testNonIntegerStart()
    {
        $this->expectPhpcsException('The $start position for getTokensAsString() must exist in the token stack');

        BCFile::getTokensAsString(self::$phpcsFile, false, 10);
    }

    /**
     * Test passing a non integer `$length`.
     *
     * @return void
     */
    public function testNonIntegerLength()
    {
        $result = BCFile::getTokensAsString(self::$phpcsFile, 10, false);
        $this->assertSame('', $result);

        $result = BCFile::getTokensAsString(self::$phpcsFile, 10, 1.5);
        $this->assertSame('', $result);
    }

    /**
     * Test passing a zero or negative `$length`.
     *
     * @return void
     */
    public function testLengthEqualToOrLessThanZero()
    {
        $result = BCFile::getTokensAsString(self::$phpcsFile, 10, -10);
        $this->assertSame('', $result);

        $result = BCFile::getTokensAsString(self::$phpcsFile, 10, 0);
        $this->assertSame('', $result);
    }

    /**
     * Test passing a `$length` beyond the end of the file.
     *
     * @return void
     */
    public function testLengthBeyondEndOfFile()
    {
        $semicolon = $this->getTargetToken('/* testEndOfFile */', \T_SEMICOLON);
        $result    = BCFile::getTokensAsString(self::$phpcsFile, $semicolon, 20);
        $this->assertSame(';
', $result);
    }

    /**
     * Test getting a token set as a string.
     *
     * @dataProvider dataGetTokensAsString
     *
     * @param string     $testMarker     The comment which prefaces the target token in the test file.
     * @param int|string $startTokenType The type of token(s) to look for for the start of the string.
     * @param int        $length         Token length to get.
     * @param string     $expected       The expected function return value.
     *
     * @return void
     */
    public function testGetTokensAsString($testMarker, $startTokenType, $length, $expected)
    {
        $start  = $this->getTargetToken($testMarker, $startTokenType);
        $result = BCFile::getTokensAsString(self::$phpcsFile, $start, $length);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetTokensAsString() For the array format.
     *
     * @return array<string, array<string, string|int>>
     */
    public static function dataGetTokensAsString()
    {
        $php8Names = parent::usesPhp8NameTokens();

        return [
            'length-0' => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => \T_LNUMBER,
                'length'         => 0,
                'expected'       => '',
            ],
            'length-1' => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => \T_LNUMBER,
                'length'         => 1,
                'expected'       => '1',
            ],
            'length-2' => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => \T_LNUMBER,
                'length'         => 2,
                'expected'       => '1 ',
            ],
            'length-3' => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => \T_LNUMBER,
                'length'         => 3,
                'expected'       => '1 +',
            ],
            'length-4' => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => \T_LNUMBER,
                'length'         => 4,
                'expected'       => '1 + ',
            ],
            'length-5' => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => \T_LNUMBER,
                'length'         => 5,
                'expected'       => '1 + 2',
            ],
            'length-6' => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => \T_LNUMBER,
                'length'         => 6,
                'expected'       => '1 + 2 ',
            ],
            'length-7' => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => \T_LNUMBER,
                'length'         => 7,
                'expected'       => '1 + 2 +',
            ],
            'length-8' => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => \T_LNUMBER,
                'length'         => 8,
                'expected'       => '1 + 2 +
',
            ],
            'length-9' => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => \T_LNUMBER,
                'length'         => 9,
                'expected'       => '1 + 2 +
        ',
            ],
            'length-10' => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => \T_LNUMBER,
                'length'         => 10,
                'expected'       => '1 + 2 +
        // Comment.
',
            ],
            'length-11' => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => \T_LNUMBER,
                'length'         => 11,
                'expected'       => '1 + 2 +
        // Comment.
        ',
            ],
            'length-12' => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => \T_LNUMBER,
                'length'         => 12,
                'expected'       => '1 + 2 +
        // Comment.
        3',
            ],
            'length-13' => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => \T_LNUMBER,
                'length'         => 13,
                'expected'       => '1 + 2 +
        // Comment.
        3 ',
            ],
            'length-14' => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => \T_LNUMBER,
                'length'         => 14,
                'expected'       => '1 + 2 +
        // Comment.
        3 +',
            ],
            'length-34' => [
                'testMarker'     => '/* testCalculation */',
                'startTokenType' => \T_LNUMBER,
                'length'         => 34,
                'expected'       => '1 + 2 +
        // Comment.
        3 + 4
        + 5 + 6 + 7 > 20;',
            ],
            'namespace' => [
                'testMarker'     => '/* testNamespace */',
                'startTokenType' => \T_NAMESPACE,
                'length'         => ($php8Names === true) ? 4 : 8,
                'expected'       => 'namespace Foo\Bar\Baz;',
            ],
            'use-with-comments' => [
                'testMarker'     => '/* testUseWithComments */',
                'startTokenType' => \T_USE,
                'length'         => 17,
                'expected'       => 'use Foo /*comment*/ \ Bar
    // phpcs:ignore Stnd.Cat.Sniff --    For reasons.
    \ Bah;',
            ],
            'echo-with-tabs' => [
                'testMarker'     => '/* testEchoWithTabs */',
                'startTokenType' => \T_ECHO,
                'length'         => 13,
                'expected'       => 'echo \'foo\',
    \'bar\'   ,
        \'baz\';',
            ],
            'end-of-file' => [
                'testMarker'     => '/* testEndOfFile */',
                'startTokenType' => \T_ECHO,
                'length'         => 4,
                'expected'       => 'echo   $foo;',
            ],
        ];
    }

    /**
     * Test getting a token set as a string with the original, non tab-replaced content.
     *
     * @dataProvider dataGetOrigContent
     *
     * @param string     $testMarker     The comment which prefaces the target token in the test file.
     * @param int|string $startTokenType The type of token(s) to look for for the start of the string.
     * @param int        $length         Token length to get.
     * @param string     $expected       The expected function return value.
     *
     * @return void
     */
    public function testGetOrigContent($testMarker, $startTokenType, $length, $expected)
    {
        $start  = $this->getTargetToken($testMarker, $startTokenType);
        $result = BCFile::getTokensAsString(self::$phpcsFile, $start, $length, true);
        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetOrigContent() For the array format.
     *
     * @return array<string, array<string, string|int>>
     */
    public static function dataGetOrigContent()
    {
        return [
            'use-with-comments' => [
                'testMarker'     => '/* testUseWithComments */',
                'startTokenType' => \T_USE,
                'length'         => 17,
                'expected'       => 'use Foo /*comment*/ \ Bar
	// phpcs:ignore Stnd.Cat.Sniff --	 For reasons.
	\ Bah;',
            ],
            'echo-with-tabs' => [
                'testMarker'     => '/* testEchoWithTabs */',
                'startTokenType' => \T_ECHO,
                'length'         => 13,
                'expected'       => 'echo \'foo\',
	\'bar\'	,
		\'baz\';',
            ],
            'end-of-file' => [
                'testMarker'     => '/* testEndOfFile */',
                'startTokenType' => \T_ECHO,
                'length'         => 4,
                'expected'       => 'echo   $foo;',
            ],
        ];
    }
}
