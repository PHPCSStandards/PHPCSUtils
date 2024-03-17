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
 * @since 1.0.0
 */
final class TrailingCommentHandlingTest extends UtilityMethodTestCase
{

    /**
     * Expected number of spaces to use for these tests.
     *
     * @var int|string
     */
    const SPACES = 1;

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
    const MSG_REPLACEMENT_1 = '1 space';

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
     * @var string
     */
    const METRIC = 'metric name';

    /**
     * Set the name of a sniff to pass to PHPCS to limit the run (and force it to record errors).
     *
     * @var array<string>
     */
    protected static $selectedSniff = ['PHPCSUtils.SpacesFixer.Test'];

    /**
     * Test that violations are correctly reported.
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
        $stackPtr  = $this->getTargetToken($testMarker, \T_COMMENT);
        $secondPtr = $this->getTargetToken($testMarker, \T_LNUMBER, '3');

        SpacesFixer::checkAndFix(
            self::$phpcsFile,
            $stackPtr,
            $secondPtr,
            self::SPACES,
            self::MSG,
            self::CODE,
            $type,
            8
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

        $expectedMessage = \sprintf(self::MSG, self::MSG_REPLACEMENT_1, $expected['found']);
        $this->assertSame($expectedMessage, $messages[0]['message'], 'Message comparison failed');

        $this->assertSame(self::CODE, $messages[0]['source'], 'Error code comparison failed');

        $this->assertSame($expected['fixable'], $messages[0]['fixable'], 'Fixability comparison failed');

        // Additional test checking changed severity.
        $this->assertSame(8, $messages[0]['severity'], 'Severity comparison failed');

        // Check that no metric is recorded.
        $metrics = self::$phpcsFile->getMetrics();
        $this->assertFalse(
            isset($metrics[self::METRIC]['values'][$expected['found']]),
            'Failed recorded metric check'
        );
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

        $data = $this->dataCheckAndFix();
        foreach ($data as $dataset) {
            $stackPtr  = $this->getTargetToken($dataset['testMarker'], \T_COMMENT);
            $secondPtr = $this->getTargetToken($dataset['testMarker'], \T_LNUMBER, '3');

            SpacesFixer::checkAndFix(
                self::$phpcsFile,
                $stackPtr,
                $secondPtr,
                self::SPACES,
                self::MSG,
                self::CODE,
                $dataset['type']
            );
        }

        $fixedFile = __DIR__ . '/TrailingCommentHandlingTest.inc.fixed';
        $result    = self::$phpcsFile->fixer->getContents();

        $this->assertStringEqualsFile(
            $fixedFile,
            $result,
            \sprintf(
                'Fixed version of %s does not match expected version in %s',
                \basename(self::$caseFile),
                \basename($fixedFile)
            )
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
        return [
            'trailing-comment-not-fixable' => [
                'testMarker' => '/* testTrailingOpenCommentAsPtrA */',
                'expected'   => [
                    'found'   => 'a new line',
                    'fixable' => false,
                ],
                'type'       => 'error',
            ],
            'trailing-comment-fixable' => [
                'testMarker' => '/* testTrailingClosedCommentAsPtrA */',
                'expected'   => [
                    'found'   => 'a new line',
                    'fixable' => true,
                ],
                'type'       => 'error',
            ],
        ];
    }
}
