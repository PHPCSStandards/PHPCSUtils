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
 * @since 1.0.8
 */
final class SpacesFixerAtEOFTest extends UtilityMethodTestCase
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
     * Dummy error code to use for the test.
     *
     * Using the dummy full error code to force it to record.
     *
     * @var string
     */
    const CODE = 'PHPCSUtils.SpacesFixer.Test.Found';

    /**
     * Test marker.
     *
     * @var string
     */
    const TESTMARKER = '/* testCommentAtEndOfFile */';

    /**
     * Set the name of a sniff to pass to PHPCS to limit the run (and force it to record errors).
     *
     * @var array<string>
     */
    protected static $selectedSniff = ['PHPCSUtils.SpacesFixer.Test'];

    /**
     * Test that violations are correctly reported when there is no non-empty token after the second stack pointer.
     *
     * @return void
     */
    public function testCheckAndFix()
    {
        $stackPtr  = $this->getTargetToken(self::TESTMARKER, \T_SEMICOLON);
        $secondPtr = $this->getTargetToken(self::TESTMARKER, \T_COMMENT);

        SpacesFixer::checkAndFix(
            self::$phpcsFile,
            $stackPtr,
            $secondPtr,
            self::SPACES,
            self::MSG,
            self::CODE
        );

        $result = self::$phpcsFile->getErrors();
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
        $this->assertSame(self::CODE, $messages[0]['source'], 'Error code comparison failed');

        $this->assertSame(true, $messages[0]['fixable'], 'Fixability comparison failed');
    }

    /**
     * Test that the fixes are correctly made when there is no non-empty token after the second stack pointer.
     *
     * @return void
     */
    public function testFixesMade()
    {
        self::$phpcsFile->fixer->startFile(self::$phpcsFile);
        self::$phpcsFile->fixer->enabled = true;

        $stackPtr  = $this->getTargetToken(self::TESTMARKER, \T_SEMICOLON);
        $secondPtr = $this->getTargetToken(self::TESTMARKER, \T_COMMENT);

        SpacesFixer::checkAndFix(
            self::$phpcsFile,
            $stackPtr,
            $secondPtr,
            self::SPACES,
            self::MSG,
            self::CODE
        );

        $fixedFile = self::$phpcsFile->getFilename() . '.fixed';
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
}
