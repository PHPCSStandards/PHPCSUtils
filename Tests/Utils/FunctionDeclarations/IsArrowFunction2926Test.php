<?php
/**
 * PHPCSUtils, utility functions and classes for PHP_CodeSniffer sniff developers.
 *
 * @package   PHPCSUtils
 * @copyright 2019-2020 PHPCSUtils Contributors
 * @license   https://opensource.org/licenses/LGPL-3.0 LGPL3
 * @link      https://github.com/PHPCSStandards/PHPCSUtils
 */

namespace PHPCSUtils\Tests\Utils\FunctionDeclarations;

use PHPCSUtils\BackCompat\Helper;
use PHPCSUtils\TestUtils\UtilityMethodTestCase;
use PHPCSUtils\Tokens\Collections;
use PHPCSUtils\Utils\FunctionDeclarations;

/**
 * Tests for the \PHPCSUtils\Utils\FunctionDeclarations::isArrowFunction() and the
 * \PHPCSUtils\Utils\FunctionDeclarations::getArrowFunctionOpenClose() methods for
 * a particular situation which will hang the tokenizer.
 *
 * These tests are based on the `Tokenizer/BackfillFnTokenTest` file in PHPCS itself.
 *
 * @link https://github.com/squizlabs/php_codesniffer/issues/2926
 *
 * @covers \PHPCSUtils\Utils\FunctionDeclarations::isArrowFunction
 * @covers \PHPCSUtils\Utils\FunctionDeclarations::getArrowFunctionOpenClose
 *
 * @group functiondeclarations
 *
 * @since 1.0.0
 */
class IsArrowFunction2926Test extends UtilityMethodTestCase
{

    /**
     * PHPCS versions in which the tokenizer will hang for these particular test cases.
     *
     * @var array
     */
    private $unsupportedPHPCSVersions = [
        '3.5.3' => true,
        '3.5.4' => true,
        '3.5.5' => true,
    ];

    /**
     * Whether the test case file has been tokenized.
     *
     * Efficiency tweak as the tokenization is done in "before" not in "before class"
     * for this test.
     *
     * @var bool
     */
    private static $tokenized = false;

    /**
     * Do NOT Initialize PHPCS & tokenize the test case file.
     *
     * Skip tokenizing the test case file on "before class" as at that time, we can't skip the test
     * yet if the PHPCS version in incompatible and it would hang the Tokenizer (and therefore
     * the test) if it is.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        // Skip the tokenizing of the test case file at this time.
    }

    /**
     * Initialize PHPCS & tokenize the test case file on compatible PHPCS versions.
     *
     * Skip this test on PHPCS versions on which the Tokenizer will hang.
     *
     * @before
     *
     * @return void
     */
    public function setUpTestFileForReal()
    {
        $phpcsVersion = Helper::getVersion();

        if (isset($this->unsupportedPHPCSVersions[$phpcsVersion]) === true) {
            $this->markTestSkipped("Issue 2926 can not be tested on PHPCS $phpcsVersion as the Tokenizer will hang.");
        }

        if (self::$tokenized === false) {
            parent::setUpTestFile();
            self::$tokenized = true;
        }
    }

    /**
     * Test correctly detecting arrow functions.
     *
     * @dataProvider dataArrowFunction
     *
     * @param string $testMarker    The comment which prefaces the target token in the test file.
     * @param array  $expected      The expected return value for the respective functions.
     * @param array  $targetContent The content for the target token to look for in case there could
     *                              be confusion.
     *
     * @return void
     */
    public function testIsArrowFunction($testMarker, $expected, $targetContent = null)
    {
        $targets  = Collections::arrowFunctionTokensBC();
        $stackPtr = $this->getTargetToken($testMarker, $targets, $targetContent);
        $result   = FunctionDeclarations::isArrowFunction(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['is'], $result);
    }

    /**
     * Test correctly detecting arrow functions.
     *
     * @dataProvider dataArrowFunction
     *
     * @param string $testMarker    The comment which prefaces the target token in the test file.
     * @param array  $expected      The expected return value for the respective functions.
     * @param string $targetContent The content for the target token to look for in case there could
     *                              be confusion.
     *
     * @return void
     */
    public function testGetArrowFunctionOpenClose($testMarker, $expected, $targetContent = 'fn')
    {
        $targets  = Collections::arrowFunctionTokensBC();
        $stackPtr = $this->getTargetToken($testMarker, $targets, $targetContent);

        // Change from offsets to absolute token positions.
        if ($expected['get'] !== false) {
            foreach ($expected['get'] as $key => $value) {
                $expected['get'][$key] += $stackPtr;
            }
        }

        $result = FunctionDeclarations::getArrowFunctionOpenClose(self::$phpcsFile, $stackPtr);
        $this->assertSame($expected['get'], $result);
    }

    /**
     * Data provider.
     *
     * @see testIsArrowFunction()           For the array format.
     * @see testgetArrowFunctionOpenClose() For the array format.
     *
     * @return array
     */
    public function dataArrowFunction()
    {
        return [
            'arrow-function-returning-heredoc' => [
                'testMarker' => '/* testHeredoc */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 2,
                        'scope_opener'       => 4,
                        'scope_closer'       => 9,
                    ],
                ],
            ],
            'arrow-function-returning-nowdoc' => [
                'testMarker' => '/* testNowdoc */',
                'expected'   => [
                    'is'  => true,
                    'get' => [
                        'parenthesis_opener' => 1,
                        'parenthesis_closer' => 2,
                        'scope_opener'       => 4,
                        'scope_closer'       => 9,
                    ],
                ],
            ],
        ];
    }
}
