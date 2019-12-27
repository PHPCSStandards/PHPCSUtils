<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019 PHPCSUtils Contributors
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
 * @since 1.0.0
 */
class GetTokensAsStringTest extends UtilityMethodTestCase
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
     * @dataProvider dataGetTokensAsString()
     *
     * @param string           $testMarker     The comment which prefaces the target token in the test file.
     * @param int|string|array $startTokenType The type of token(s) to look for for the start of the string.
     * @param int              $length         Token length to get.
     * @param string           $expected       The expected function return value.
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
     * @return array
     */
    public function dataGetTokensAsString()
    {
        return [
            'length-0' => [
                '/* testNamespace */',
                \T_NAMESPACE,
                0,
                '',
            ],
            'length-1' => [
                '/* testNamespace */',
                \T_NAMESPACE,
                1,
                'namespace',
            ],
            'length-2' => [
                '/* testNamespace */',
                \T_NAMESPACE,
                2,
                'namespace ',
            ],
            'length-3' => [
                '/* testNamespace */',
                \T_NAMESPACE,
                3,
                'namespace Foo',
            ],
            'length-4' => [
                '/* testNamespace */',
                \T_NAMESPACE,
                4,
                'namespace Foo\\',
            ],
            'length-5' => [
                '/* testNamespace */',
                \T_NAMESPACE,
                5,
                'namespace Foo\Bar',
            ],
            'length-6' => [
                '/* testNamespace */',
                \T_NAMESPACE,
                6,
                'namespace Foo\Bar\\',
            ],
            'length-7' => [
                '/* testNamespace */',
                \T_NAMESPACE,
                7,
                'namespace Foo\Bar\Baz',
            ],
            'length-8' => [
                '/* testNamespace */',
                \T_NAMESPACE,
                8,
                'namespace Foo\Bar\Baz;',
            ],
            'length-9' => [
                '/* testNamespace */',
                \T_NAMESPACE,
                9,
                'namespace Foo\Bar\Baz;
',
            ],
            'use-with-comments' => [
                '/* testUseWithComments */',
                \T_USE,
                17,
                'use Foo /*comment*/ \ Bar
    // phpcs:ignore Stnd.Cat.Sniff --    For reasons.
    \ Bah;',
            ],
            'echo-with-tabs' => [
                '/* testEchoWithTabs */',
                \T_ECHO,
                13,
                'echo \'foo\',
    \'bar\'   ,
        \'baz\';',
            ],
            'end-of-file' => [
                '/* testEndOfFile */',
                \T_ECHO,
                4,
                'echo   $foo;',
            ],
        ];
    }

    /**
     * Test getting a token set as a string with the original, non tab-replaced content.
     *
     * @dataProvider dataGetOrigContent()
     *
     * @param string           $testMarker     The comment which prefaces the target token in the test file.
     * @param int|string|array $startTokenType The type of token(s) to look for for the start of the string.
     * @param int              $length         Token length to get.
     * @param string           $expected       The expected function return value.
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
     * @return array
     */
    public function dataGetOrigContent()
    {
        return [
            'use-with-comments' => [
                '/* testUseWithComments */',
                \T_USE,
                17,
                'use Foo /*comment*/ \ Bar
	// phpcs:ignore Stnd.Cat.Sniff --	 For reasons.
	\ Bah;',
            ],
            'echo-with-tabs' => [
                '/* testEchoWithTabs */',
                \T_ECHO,
                13,
                'echo \'foo\',
	\'bar\'	,
		\'baz\';',
            ],
            'end-of-file' => [
                '/* testEndOfFile */',
                \T_ECHO,
                4,
                'echo   $foo;',
            ],
        ];
    }
}
