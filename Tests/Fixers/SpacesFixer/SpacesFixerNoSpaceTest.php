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
 * Tests for the \PHPCSUtils\Fixers\SpacesFixer::checkAndFix() method.
 *
 * @covers \PHPCSUtils\Fixers\SpacesFixer::checkAndFix
 *
 * @group fixers
 *
 * @since 1.0.0
 */
class SpacesFixerNoSpaceTest extends UtilityMethodTestCase
{

    /**
     * Expected number of spaces to use for these tests.
     *
     * @var int|string
     */
    const SPACES = 0;

    /**
     * Dummy error message phrase to use for the test.
     *
     * @var string
     */
    const MSG = 'Expected: %s. Found: %s';

    /**
     * The expected replacement for the first placeholder.
     *
     * @var string
     */
    const MSG_REPLACEMENT_1 = 'no space';

    /**
     * Dummy error code to use for the test.
     *
     * Using the dummy full error code to force it to record.
     *
     * @var string
     */
    const CODE = 'PHPCSUtils.SpacerFixer.Test.Found';

    /**
     * Dummy metric name to use for the test.
     *
     * @var string
     */
    const METRIC = 'metric name';

    /**
     * The names of the test case(s) in compliance.
     *
     * @var array
     */
    protected $compliantCases = ['no-space'];

    /**
     * Full path to the test case file associated with this test class.
     *
     * @var string
     */
    protected static $caseFile = '';

    /**
     * Full path to the fixed version of the test case file associated with this test class.
     *
     * @var string
     */
    protected static $fixedFile = '/SpacesFixerNoSpaceTest.inc.fixed';

    /**
     * Set the name of a sniff to pass to PHPCS to limit the run (and force it to record errors).
     *
     * @var array
     */
    protected static $selectedSniff = ['PHPCSUtils.SpacerFixer.Test'];

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
     * @dataProvider dataCheckAndFixNoError
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   Expected error details (for the metric input).
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
     * @return array
     */
    public function dataCheckAndFixNoError()
    {
        $data     = [];
        $baseData = $this->getAllData();

        foreach ($this->compliantCases as $caseName) {
            if (isset($baseData[$caseName])) {
                $data[$caseName] = $baseData[$caseName];
            }
        }

        return $data;
    }

    /**
     * Test that violations are correctly reported.
     *
     * @dataProvider dataCheckAndFix
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     * @param array  $expected   Expected error details.
     * @param string $type       The message type to test: 'error' or 'warning'.
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

        // PHPCS 2.x places `unknownSniff.` before the actual error code for utility tests with a dummy error code.
        $errorCodeResult = \str_replace('unknownSniff.', '', $messages[0]['source']);
        $this->assertSame(static::CODE, $errorCodeResult, 'Error code comparison failed');

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
     * @return array
     */
    public function dataCheckAndFix()
    {
        $data = $this->getAllData();

        foreach ($this->compliantCases as $caseName) {
            unset($data[$caseName]);
        }

        return $data;
    }

    /**
     * Test that the fixes are correctly made.
     *
     * @return void
     */
    public function testFixesMade()
    {
        self::$phpcsFile->fixer->startFile(self::$phpcsFile);
        self::$phpcsFile->fixer->enabled = true;

        $data = $this->getAllData();
        foreach ($data as $dataset) {
            $stackPtr  = $this->getTargetToken($dataset[0], \T_ARRAY);
            $secondPtr = $this->getTargetToken($dataset[0], \T_OPEN_PARENTHESIS);

            SpacesFixer::checkAndFix(
                self::$phpcsFile,
                $stackPtr,
                $secondPtr,
                static::SPACES,
                static::MSG,
                static::CODE,
                $dataset[1],
                0
            );
        }

        $fixedFile = __DIR__ . static::$fixedFile;
        $expected  = \file_get_contents($fixedFile);
        $result    = self::$phpcsFile->fixer->getContents();

        $this->assertSame(
            $expected,
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
     * @return array
     */
    protected function getAllData()
    {
        return [
            'no-space' => [
                '/* testNoSpace */',
                [
                    'found'   => 'no spaces',
                    'fixable' => true,
                ],
                'error',
            ],
            'one-space' => [
                '/* testOneSpace */',
                [
                    'found'   => '1 space',
                    'fixable' => true,
                ],
                'error',
            ],
            'two-spaces' => [
                '/* testTwoSpaces */',
                [
                    'found'   => '2 spaces',
                    'fixable' => true,
                ],
                'error',
            ],
            'multiple-spaces' => [
                '/* testMultipleSpaces */',
                [
                    'found'   => '13 spaces',
                    'fixable' => true,
                ],
                'warning',
            ],
            'newline-and-trailing-spaces' => [
                '/* testNewlineAndTrailingSpaces */',
                [
                    'found'   => 'a new line',
                    'fixable' => true,
                ],
                'error',
            ],
            'multiple-newlines-and-spaces' => [
                '/* testMultipleNewlinesAndSpaces */',
                [
                    'found'   => 'multiple new lines',
                    'fixable' => true,
                ],
                'error',
            ],
            'comment-no-space' => [
                '/* testCommentNoSpace */',
                [
                    'found'   => 'non-whitespace tokens',
                    'fixable' => false,
                ],
                'warning',
            ],
            'comment-and-space' => [
                '/* testCommentAndSpaces */',
                [
                    'found'   => '1 space',
                    'fixable' => false,
                ],
                'error',
            ],
            'comment-and-new line' => [
                '/* testCommentAndNewline */',
                [
                    'found'   => 'a new line',
                    'fixable' => false,
                ],
                'error',
            ],
        ];
    }
}
