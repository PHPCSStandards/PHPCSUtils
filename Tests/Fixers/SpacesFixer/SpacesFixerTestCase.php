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
 * Test case for the \PHPCSUtils\Fixers\SpacesFixer::checkAndFix() method.
 *
 * @since 1.0.0
 */
abstract class SpacesFixerTestCase extends UtilityMethodTestCase
{

    /**
     * Expected number of spaces to use for these tests.
     *
     * Important: this MUST be set in the concrete test class!
     *
     * @var int|string|null Note: `null` is not an acceptable value for the overloaded constant!
     */
    const SPACES = null;

    /**
     * Dummy error message phrase to use for the test.
     *
     * @var string
     */
    const MSG = 'Expected: %s. Found: %s';

    /**
     * The expected replacement for the first placeholder.
     *
     * Important: this MUST be set in the concrete test class!
     *
     * @var string
     */
    const MSG_REPLACEMENT_1 = '';

    /**
     * Dummy error code to use for the test.
     *
     * Using the dummy full error code to force it to record.
     *
     * @var string
     */
    const CODE = 'PHPCSUtils.SpacesFixer.Test.Found';

    /**
     * Dummy metric name to use for the test.
     *
     * Important: this MUST be set in the concrete test class!
     *
     * @var string
     */
    const METRIC = '';

    /**
     * The names of the test case(s) in compliance.
     *
     * Important: this MUST be set in the concrete test class!
     *
     * @var array<string>
     */
    protected static $compliantCases = [];

    /**
     * Full path to the fixed version of the test case file associated with this test class.
     *
     * Important: this MUST be set in the concrete test class!
     *
     * @var string
     */
    protected static $fixedFile = '';

    /**
     * Set the name of a sniff to pass to PHPCS to limit the run (and force it to record errors).
     *
     * @var array<string>
     */
    protected static $selectedSniff = ['PHPCSUtils.SpacesFixer.Test'];

    /**
     * Initialize PHPCS & tokenize the test case file.
     *
     * @beforeClass
     *
     * @return void
     */
    public static function setUpTestFile()
    {
        self::$caseFile = __DIR__ . '/SpacesFixerTest.inc';
        parent::setUpTestFile();
    }

    /**
     * Test that no violation is reported for a test case complying with the correct number of spaces.
     *
     * @covers \PHPCSUtils\Fixers\SpacesFixer::checkAndFix
     *
     * @dataProvider dataCheckAndFixNoError
     *
     * @param string                     $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, string|bool> $expected   Expected error details (for the metric input).
     *
     * @return void
     */
    public function testCheckAndFixNoError($testMarker, $expected)
    {
        $stackPtr  = $this->getTargetToken($testMarker, \T_ARRAY);
        $secondPtr = $this->getTargetToken($testMarker, \T_OPEN_PARENTHESIS);

        /*
         * Note: passing $stackPtr and $secondPtr in reverse order to make sure that case is
         * covered by a test as well.
         */
        SpacesFixer::checkAndFix(
            self::$phpcsFile,
            $secondPtr,
            $stackPtr,
            static::SPACES,
            static::MSG,
            static::CODE,
            'error',
            0,
            static::METRIC
        );

        $result = \array_merge(self::$phpcsFile->getErrors(), self::$phpcsFile->getWarnings());

        // Expect no errors.
        $this->assertCount(0, $result, 'Failed to assert that no violations were found');

        // Check that the metric is recorded correctly.
        $metrics = self::$phpcsFile->getMetrics();
        $this->assertGreaterThanOrEqual(
            1,
            $metrics[static::METRIC]['values'][$expected['found']],
            'Failed recorded metric check'
        );
    }

    /**
     * Data Provider.
     *
     * @see testCheckAndFixNoError() For the array format.
     *
     * @return array<string, array<string, string|array<string, string|bool>>>
     */
    public static function dataCheckAndFixNoError()
    {
        $data     = [];
        $baseData = self::getAllData();

        foreach (static::$compliantCases as $caseName) {
            if (isset($baseData[$caseName])) {
                $data[$caseName] = $baseData[$caseName];
                unset($data[$caseName]['type']);
            }
        }

        return $data;
    }

    /**
     * Test that violations are correctly reported.
     *
     * @covers \PHPCSUtils\Fixers\SpacesFixer::checkAndFix
     *
     * @dataProvider dataCheckAndFix
     *
     * @param string                     $testMarker The comment which prefaces the target token in the test file.
     * @param array<string, string|bool> $expected   Expected error details.
     * @param string                     $type       The message type to test: 'error' or 'warning'.
     *
     * @return void
     */
    public function testCheckAndFix($testMarker, $expected, $type)
    {
        $stackPtr  = $this->getTargetToken($testMarker, \T_ARRAY);
        $secondPtr = $this->getTargetToken($testMarker, \T_OPEN_PARENTHESIS);

        SpacesFixer::checkAndFix(
            self::$phpcsFile,
            $stackPtr,
            $secondPtr,
            static::SPACES,
            static::MSG,
            static::CODE,
            $type,
            0,
            static::METRIC
        );

        if ($type === 'error') {
            $result = self::$phpcsFile->getErrors();
        } else {
            $result = self::$phpcsFile->getWarnings();
        }

        $tokens = self::$phpcsFile->getTokens();

        if (isset($result[$tokens[$stackPtr]['line']][$tokens[$stackPtr]['column']]) === false) {
            $this->fail('Expected 1 violation. None found.');
        }

        $messages = $result[$tokens[$stackPtr]['line']][$tokens[$stackPtr]['column']];

        // Expect one violation.
        $this->assertCount(1, $messages, 'Expected 1 violation, found: ' . \count($messages));

        /*
         * Test the violation details.
         */

        $expectedMessage = \sprintf(static::MSG, static::MSG_REPLACEMENT_1, $expected['found']);
        $this->assertSame($expectedMessage, $messages[0]['message'], 'Message comparison failed');

        $this->assertSame(static::CODE, $messages[0]['source'], 'Error code comparison failed');

        $this->assertSame($expected['fixable'], $messages[0]['fixable'], 'Fixability comparison failed');

        // Check that the metric is recorded correctly.
        $metrics = self::$phpcsFile->getMetrics();
        $this->assertGreaterThanOrEqual(
            1,
            $metrics[static::METRIC]['values'][$expected['found']],
            'Failed recorded metric check'
        );
    }

    /**
     * Data Provider.
     *
     * @see testCheckAndFix() For the array format.
     *
     * @return array<string, array<string, string|array<string, string|bool>>>
     */
    public static function dataCheckAndFix()
    {
        $data = self::getAllData();

        foreach (static::$compliantCases as $caseName) {
            unset($data[$caseName]);
        }

        return $data;
    }

    /**
     * Test that the fixes are correctly made.
     *
     * @covers \PHPCSUtils\Fixers\SpacesFixer::checkAndFix
     *
     * @return void
     */
    public function testFixesMade()
    {
        self::$phpcsFile->fixer->startFile(self::$phpcsFile);
        self::$phpcsFile->fixer->enabled = true;

        $data = $this->getAllData();
        foreach ($data as $dataset) {
            $stackPtr  = $this->getTargetToken($dataset['testMarker'], \T_ARRAY);
            $secondPtr = $this->getTargetToken($dataset['testMarker'], \T_OPEN_PARENTHESIS);

            SpacesFixer::checkAndFix(
                self::$phpcsFile,
                $stackPtr,
                $secondPtr,
                static::SPACES,
                static::MSG,
                static::CODE,
                $dataset['type'],
                0
            );
        }

        $fixedFile = __DIR__ . static::$fixedFile;
        $result    = self::$phpcsFile->fixer->getContents();

        $this->assertStringEqualsFile(
            $fixedFile,
            $result,
            \sprintf(
                'Fixed version of %s does not match expected version in %s',
                \basename(static::$caseFile),
                \basename($fixedFile)
            )
        );
    }

    /**
     * Helper function holding the base data for the data providers.
     *
     * @return array<string, array<string, string|array<string, string|bool>>>
     */
    protected static function getAllData()
    {
        return [
            'no-space' => [
                'testMarker' => '/* testNoSpace */',
                'expected'   => [
                    'found'   => 'no spaces',
                    'fixable' => true,
                ],
                'type'       => 'error',
            ],
            'one-space' => [
                'testMarker' => '/* testOneSpace */',
                'expected'   => [
                    'found'   => '1 space',
                    'fixable' => true,
                ],
                'type'       => 'error',
            ],
            'two-spaces' => [
                'testMarker' => '/* testTwoSpaces */',
                'expected'   => [
                    'found'   => '2 spaces',
                    'fixable' => true,
                ],
                'type'       => 'error',
            ],
            'multiple-spaces' => [
                'testMarker' => '/* testMultipleSpaces */',
                'expected'   => [
                    'found'   => '13 spaces',
                    'fixable' => true,
                ],
                'type'       => 'warning',
            ],
            'newline-and-trailing-spaces' => [
                'testMarker' => '/* testNewlineAndTrailingSpaces */',
                'expected'   => [
                    'found'   => 'a new line',
                    'fixable' => true,
                ],
                'type'       => 'error',
            ],
            'multiple-newlines-and-spaces' => [
                'testMarker' => '/* testMultipleNewlinesAndSpaces */',
                'expected'   => [
                    'found'   => 'multiple new lines',
                    'fixable' => true,
                ],
                'type'       => 'error',
            ],
            'comment-no-space' => [
                'testMarker' => '/* testCommentNoSpace */',
                'expected'   => [
                    'found'   => 'non-whitespace tokens',
                    'fixable' => false,
                ],
                'type'       => 'warning',
            ],
            'comment-and-space' => [
                'testMarker' => '/* testCommentAndSpaces */',
                'expected'   => [
                    'found'   => '1 space',
                    'fixable' => false,
                ],
                'type'       => 'error',
            ],
            'comment-and-new line' => [
                'testMarker' => '/* testCommentAndNewline */',
                'expected'   => [
                    'found'   => 'a new line',
                    'fixable' => false,
                ],
                'type'       => 'error',
            ],
        ];
    }
}
