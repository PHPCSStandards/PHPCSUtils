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
 * Tests for the \PHPCSUtils\TestUtils\UtilityMethodTestCase::getTargetToken() method.
 *
 * @covers \PHPCSUtils\TestUtils\UtilityMethodTestCase::getTargetToken
 *
 * @since 1.0.0
 */
final class GetTargetTokenTest extends PolyfilledTestCase
{

    /**
     * Initialize PHPCS & tokenize the test case file.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = __DIR__ . '/SetUpTestFileTest.inc';
        parent::setUpTestFile();
    }

    /**
     * Test the getTargetToken() method.
     *
     * @dataProvider dataGetTargetToken
     *
     * @param int                          $expected      Expected function output.
     * @param string                       $commentString The delimiter comment to look for.
     * @param int|string|array<int|string> $tokenType     The type of token(s) to look for.
     * @param string                       $tokenContent  Optional. The token content for the target token.
     *
     * @return void
     */
    public function testGetTargetToken($expected, $commentString, $tokenType, $tokenContent = null)
    {
        if (isset($tokenContent)) {
            $result = $this->getTargetToken($commentString, $tokenType, $tokenContent);
        } else {
            $result = self::getTargetToken($commentString, $tokenType);
        }

        $this->assertSame($expected, $result);
    }

    /**
     * Data provider.
     *
     * @see testGetTargetToken() For the array format.
     *
     * @return array<string, array<string, int|string|array<int|string>>>
     */
    public static function dataGetTargetToken()
    {
        // Token offsets based on PHPCS 3.x tokenization.
        $data = [
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

        // The PHP open tag + whitespace is two tokens in PHPCS 4.x, while it is one in PHPCS 3.x.
        if (parent::usesPhp8NameTokens() === true) {
            foreach ($data as $key => $dataset) {
                ++$data[$key]['expected'];
            }
        }

        return $data;
    }

    /**
     * Test the behaviour of the getTargetToken() method when the test marker comment is not found.
     *
     * @return void
     */
    public function testGetTargetTokenCommentNotFound()
    {
        $this->expectException('PHPCSUtils\Exceptions\TestMarkerNotFound');
        $this->expectExceptionMessage('Failed to find the test marker: ');

        $this->getTargetToken('/* testCommentDoesNotExist */', [\T_VARIABLE], '$a');
    }

    /**
     * Test the behaviour of the getTargetToken() method when the target is not found.
     *
     * @return void
     */
    public function testGetTargetTokenNotFoundException()
    {
        $this->expectException('PHPCSUtils\Exceptions\TestTargetNotFound');
        $this->expectExceptionMessage('Failed to find test target token for comment string: ');

        self::getTargetToken('/* testNotFindingTarget */', [\T_VARIABLE], '$a');
    }
}
