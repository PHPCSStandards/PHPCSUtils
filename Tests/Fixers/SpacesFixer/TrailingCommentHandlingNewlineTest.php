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
final class TrailingCommentHandlingNewlineTest extends UtilityMethodTestCase
{

    /**
     * Expected number of spaces to use for these tests.
     *
     * @var int|string
     */
    const SPACES = 'newline';

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
    const MSG_REPLACEMENT_1 = 'a new line';

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
     * Test that no violation is reported for a test case complying with the correct number of spaces.
     *
     * @dataProvider dataCheckAndFixNoError
     *
     * @param string $testMarker The comment which prefaces the target token in the test file.
     *
     * @return void
     */
    public function testCheckAndFixNoError($testMarker)
    {
        $stackPtr  = $this->getTargetToken($testMarker, \T_CLOSE_PARENTHESIS);
        $secondPtr = self::$phpcsFile->findPrevious(\T_WHITESPACE, ($stackPtr - 1), null, true);

        SpacesFixer::checkAndFix(
            self::$phpcsFile,
            $stackPtr,
            $secondPtr,
            self::SPACES,
            self::MSG,
            self::CODE,
            'error',
            0,
            self::METRIC
        );

        $result = \array_merge(self::$phpcsFile->getErrors(), self::$phpcsFile->getWarnings());

        // Expect no errors.
        $this->assertCount(0, $result, 'Failed to assert that no violations were found');

        // Check that the metric is recorded correctly.
        $metrics = self::$phpcsFile->getMetrics();
        $this->assertGreaterThanOrEqual(
            1,
            $metrics[self::METRIC]['values'][self::MSG_REPLACEMENT_1],
            'Failed recorded metric check'
        );
    }

    /**
     * Test that no fixes are made.
     *
     * @return void
     */
    public function testNoFixesMade()
    {
        self::$phpcsFile->fixer->startFile(self::$phpcsFile);
        self::$phpcsFile->fixer->enabled = true;

        $data = $this->dataCheckAndFixNoError();
        foreach ($data as $dataset) {
            $stackPtr  = $this->getTargetToken($dataset[0], \T_CLOSE_PARENTHESIS);
            $secondPtr = self::$phpcsFile->findPrevious(\T_WHITESPACE, ($stackPtr - 1), null, true);

            SpacesFixer::checkAndFix(
                self::$phpcsFile,
                $stackPtr,
                $secondPtr,
                self::SPACES,
                self::MSG,
                self::CODE,
                'error'
            );
        }

        $fixedFile = __DIR__ . '/TrailingCommentHandlingNewlineTest.inc';
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
     * @see testCheckAndFixNoError() For the array format.
     *
     * @return array<string, array<string>>
     */
    public static function dataCheckAndFixNoError()
    {
        return [
            'correct-newline-before' => [
                '/* testNewlineWithTrailingCommentBefore */',
            ],
            'correct-blank-line-before' => [
                '/* testNewlineWithTrailingBlankLineBefore */',
            ],
        ];
    }
}
