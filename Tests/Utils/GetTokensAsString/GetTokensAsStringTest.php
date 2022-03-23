<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\GetTokensAsString;

use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Utils\GetTokensAsString;

/**
 * Tests for the \PHPCSUtils\Utils\GetTokensAsString class.
 *
 * @covers \PHPCSUtils\Utils\GetTokensAsString
 *
 * @group gettokensasstring
 *
 * @since 1.0.0
 */
class GetTokensAsStringTest extends UtilityMethodTestCase
{

    /**
     * Full path to the test case file associated with this test class.
     *
     * @var string
     */
    protected static $caseFile = '';

    /**
     * Initialize PHPCS & tokenize the test case file.
     *
     * Overloaded to re-use the `$caseFile` from the BCFile test.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = \dirname(\dirname(__DIR__)) . '/BackCompat/BCFile/GetTokensAsStringTest.inc';
        parent::setUpTestFile();
    }

    /**
     * Test passing a non-existent $start token pointer.
     *
     * @return void
     */
    public function testNonExistentStart()
    {
        $this->expectPhpcsException('The $start position for GetTokensAsString methods must exist in the token stack');

        GetTokensAsString::normal(self::$phpcsFile, 100000, 100010);
    }

    /**
     * Test passing a non integer `$start`, like the result of a failed $phpcsFile->findNext().
     *
     * @return void
     */
    public function testNonIntegerStart()
    {
        $this->expectPhpcsException('The $start position for GetTokensAsString methods must exist in the token stack');

        GetTokensAsString::noEmpties(self::$phpcsFile, false, 10);
    }

    /**
     * Test passing a non integer `$end`, like the result of a failed $phpcsFile->findNext().
     *
     * @return void
     */
    public function testNonIntegerEnd()
    {
        $result = GetTokensAsString::tabReplaced(self::$phpcsFile, 10, false);
        $this->assertSame('', $result);

        $result = GetTokensAsString::origContent(self::$phpcsFile, 10, 11.5);
        $this->assertSame('', $result);
    }

    /**
     * Test passing a token pointer to $end which is less than $start.
     *
     * @return void
     */
    public function testEndBeforeStart()
    {
        $result = GetTokensAsString::noComments(self::$phpcsFile, 10, 5);
        $this->assertSame('', $result);
    }

    /**
     * Test passing a `$end` beyond the end of the file.
     *
     * @return void
     */
    public function testLengthBeyondEndOfFile()
    {
        $semicolon = $this->getTargetToken('/* testEndOfFile */', \T_SEMICOLON);
        $result    = GetTokensAsString::origContent(self::$phpcsFile, $semicolon, 1000);
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
     * @param array            $expected       The expected function's return values.
     *
     * @return void
     */
    public function testNormal($testMarker, $startTokenType, $expected)
    {
        $start = $this->getTargetToken($testMarker, $startTokenType);
        $end   = $this->getTargetToken($testMarker, \T_SEMICOLON);

        $result = GetTokensAsString::tabReplaced(self::$phpcsFile, $start, $end);
        $this->assertSame($expected['tab_replaced'], $result);
    }

    /**
     * Test getting a token set as a string with the original content.
     *
     * @dataProvider dataGetTokensAsString()
     *
     * @param string           $testMarker     The comment which prefaces the target token in the test file.
     * @param int|string|array $startTokenType The type of token(s) to look for for the start of the string.
     * @param array            $expected       The expected function's return values.
     *
     * @return void
     */
    public function testOrigContent($testMarker, $startTokenType, $expected)
    {
        $start = $this->getTargetToken($testMarker, $startTokenType);
        $end   = $this->getTargetToken($testMarker, \T_SEMICOLON);

        $result = GetTokensAsString::origContent(self::$phpcsFile, $start, $end);
        $this->assertSame($expected['orig'], $result);
    }

    /**
     * Test getting a token set as a string without comments.
     *
     * @dataProvider dataGetTokensAsString()
     *
     * @param string           $testMarker     The comment which prefaces the target token in the test file.
     * @param int|string|array $startTokenType The type of token(s) to look for for the start of the string.
     * @param array            $expected       The expected function's return values.
     *
     * @return void
     */
    public function testNoComments($testMarker, $startTokenType, $expected)
    {
        $start = $this->getTargetToken($testMarker, $startTokenType);
        $end   = $this->getTargetToken($testMarker, \T_SEMICOLON);

        $result = GetTokensAsString::noComments(self::$phpcsFile, $start, $end);
        $this->assertSame($expected['no_comments'], $result);
    }

    /**
     * Test getting a token set as a string without comments or whitespace.
     *
     * @dataProvider dataGetTokensAsString()
     *
     * @param string           $testMarker     The comment which prefaces the target token in the test file.
     * @param int|string|array $startTokenType The type of token(s) to look for for the start of the string.
     * @param array            $expected       The expected function's return values.
     *
     * @return void
     */
    public function testNoEmpties($testMarker, $startTokenType, $expected)
    {
        $start = $this->getTargetToken($testMarker, $startTokenType);
        $end   = $this->getTargetToken($testMarker, \T_SEMICOLON);

        $result = GetTokensAsString::noEmpties(self::$phpcsFile, $start, $end);
        $this->assertSame($expected['no_empties'], $result);
    }

    /**
     * Test getting a token set as a string with compacted whitespace.
     *
     * @dataProvider dataGetTokensAsString()
     *
     * @param string           $testMarker     The comment which prefaces the target token in the test file.
     * @param int|string|array $startTokenType The type of token(s) to look for for the start of the string.
     * @param array            $expected       The expected function's return values.
     *
     * @return void
     */
    public function testCompact($testMarker, $startTokenType, $expected)
    {
        $start = $this->getTargetToken($testMarker, $startTokenType);
        $end   = $this->getTargetToken($testMarker, \T_SEMICOLON);

        $result = GetTokensAsString::compact(self::$phpcsFile, $start, $end);
        $this->assertSame($expected['compact'], $result);
    }

    /**
     * Test getting a token set as a string without comments and with compacted whitespace.
     *
     * @dataProvider dataGetTokensAsString()
     *
     * @param string           $testMarker     The comment which prefaces the target token in the test file.
     * @param int|string|array $startTokenType The type of token(s) to look for for the start of the string.
     * @param array            $expected       The expected function's return values.
     *
     * @return void
     */
    public function testCompactNoComments($testMarker, $startTokenType, $expected)
    {
        $start = $this->getTargetToken($testMarker, $startTokenType);
        $end   = $this->getTargetToken($testMarker, \T_SEMICOLON);

        $result = GetTokensAsString::compact(self::$phpcsFile, $start, $end, true);
        $this->assertSame($expected['compact_nc'], $result);
    }

    /**
     * Data provider.
     *
     * @see testNormal()            For the array format.
     * @see testOrigContent()       For the array format.
     * @see testNoComments()        For the array format.
     * @see testNoEmpties()         For the array format.
     * @see testCompact()           For the array format.
     * @see testCompactNoComments() For the array format.
     *
     * @return array
     */
    public function dataGetTokensAsString()
    {
        return [
            'namespace' => [
                'marker'   => '/* testNamespace */',
                'type'     => \T_NAMESPACE,
                'expected' => [
                    'tab_replaced' => 'namespace Foo\Bar\Baz;',
                    'orig'         => 'namespace Foo\Bar\Baz;',
                    'no_comments'  => 'namespace Foo\Bar\Baz;',
                    'no_empties'   => 'namespaceFoo\Bar\Baz;',
                    'compact'      => 'namespace Foo\Bar\Baz;',
                    'compact_nc'   => 'namespace Foo\Bar\Baz;',
                ],
            ],
            'use-with-comments' => [
                'marker'   => '/* testUseWithComments */',
                'type'     => \T_STRING,
                'expected' => [
                    'tab_replaced' => 'Foo /*comment*/ \ Bar
    // phpcs:ignore Stnd.Cat.Sniff --    For reasons.
    \ Bah;',
                    'orig'         => 'Foo /*comment*/ \ Bar
	// phpcs:ignore Stnd.Cat.Sniff --	 For reasons.
	\ Bah;',
                    'no_comments'  => 'Foo  \ Bar
        \ Bah;',
                    'no_empties'   => 'Foo\Bar\Bah;',
                    'compact'      => 'Foo /*comment*/ \ Bar // phpcs:ignore Stnd.Cat.Sniff --    For reasons.
 \ Bah;',
                    'compact_nc'   => 'Foo \ Bar \ Bah;',
                ],
            ],
            'calculation' => [
                'marker'   => '/* testCalculation */',
                'type'     => \T_LNUMBER,
                'expected' => [
                    'tab_replaced' => '1 + 2 +
        // Comment.
        3 + 4
        + 5 + 6 + 7 > 20;',
                    'orig'         => '1 + 2 +
        // Comment.
        3 + 4
        + 5 + 6 + 7 > 20;',
                    'no_comments'  => '1 + 2 +
                3 + 4
        + 5 + 6 + 7 > 20;',
                    'no_empties'   => '1+2+3+4+5+6+7>20;',
                    'compact'      => '1 + 2 + // Comment.
 3 + 4 + 5 + 6 + 7 > 20;',
                    'compact_nc'   => '1 + 2 + 3 + 4 + 5 + 6 + 7 > 20;',
                ],
            ],
            'echo-with-tabs' => [
                'marker'   => '/* testEchoWithTabs */',
                'type'     => \T_ECHO,
                'expected' => [
                    'tab_replaced' => 'echo \'foo\',
    \'bar\'   ,
        \'baz\';',
                    'orig'         => 'echo \'foo\',
	\'bar\'	,
		\'baz\';',
                    'no_comments'  => 'echo \'foo\',
    \'bar\'   ,
        \'baz\';',
                    'no_empties'   => 'echo\'foo\',\'bar\',\'baz\';',
                    'compact'      => 'echo \'foo\', \'bar\' , \'baz\';',
                    'compact_nc'   => 'echo \'foo\', \'bar\' , \'baz\';',
                ],
            ],
            'end-of-file' => [
                'marker'   => '/* testEndOfFile */',
                'type'     => \T_ECHO,
                'expected' => [
                    'tab_replaced' => 'echo   $foo;',
                    'orig'         => 'echo   $foo;',
                    'no_comments'  => 'echo   $foo;',
                    'no_empties'   => 'echo$foo;',
                    'compact'      => 'echo $foo;',
                    'compact_nc'   => 'echo $foo;',
                ],
            ],
        ];
    }
}
